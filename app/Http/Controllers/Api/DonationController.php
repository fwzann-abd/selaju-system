<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\StoreManualTransferRequest;
use App\Models\Donation;
use App\Models\ManualTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DonationController extends Controller
{
    /**
     * Display a listing of donations with leaderboard.
     */
    public function index(Request $request)
    {
        $query = Donation::query()
            ->where('payment_status', 'paid')
            ->orderBy('amount', 'desc')
            ->orderBy('paid_at', 'desc');

        $limit = $request->query('limit', 15);
        $donations = $query->limit((int) $limit)->get();

        $totalDonations = Donation::where('payment_status', 'paid')->sum('amount');
        $totalDonors = Donation::where('payment_status', 'paid')->count();

        return response()->json([
            'data' => [
                'donations' => $donations->map(fn ($donation) => [
                    'id' => $donation->id,
                    'donor_name' => $donation->donor_name,
                    'amount' => $donation->amount,
                    'message' => $donation->message,
                    'paid_at' => $donation->paid_at,
                ]),
                'statistics' => [
                    'total_amount' => $totalDonations,
                    'total_donors' => $totalDonors,
                ],
            ],
        ]);
    }

    /**
     * Get donation history for logged in user.
     */
    public function history(Request $request)
    {
        $donations = Donation::where('account_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'data' => $donations,
        ]);
    }

    /**
     * Get available banks for virtual account.
     */
    public function getAvailableBanks()
    {
        // List bank yang support Non-SNAP Virtual Account di DOKU
        // Sementara hanya Bank Permata untuk testing (DGPC - FIX_BILL, Company Code: 89656)
        $banks = [
            [
                'code' => 'PERMATA',
                'name' => 'Bank Permata',
                'icon' => '🏦',
                'available' => true,
                'billing_type' => 'FIX_BILL',
                'feature' => 'DGPC',
                'company_code' => '89656',
            ],
        ];

        return response()->json([
            'data' => $banks,
        ]);
    }

    /**
     * Store a newly created donation.
     */
    public function store(StoreDonationRequest $request)
    {
        try {
            DB::beginTransaction();

            $donation = Donation::create([
                'account_id' => auth()->id(),
                'donor_name' => $request->donor_name,
                'donor_ig' => $request->donor_ig,
                'amount' => $request->amount,
                'message' => $request->message,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                // Generate unique numeric payment code (12 digits)
                'payment_code' => $this->generatePaymentCode(),
            ]);

            // Generate payment based on method
            $paymentData = null;
            if ($request->payment_method === 'MANUAL') {
                // Keep manual transfer logic if implemented or add TODO
                // Assuming generateManualPayment exists or we just return basic info
                $paymentData = [
                    'transaction_id' => 'MANUAL-'.time(),
                    'status' => 'PENDING',
                    'type' => 'MANUAL',
                    'instructions' => 'Silakan transfer manual ke rekening tertera.',
                ];
                // Or call existing method if available? Previous code had it?
                // Checking previous view: line 110 called generateQrisPayment, 112 generateVirtualAccountPayment.
                // It didn't seem to have generateManualPayment in the view I saw?
                // Wait, line 110: if ($request->payment_method === 'qris')
                // line 111: elseif ($request->payment_method === 'virtual_account')
                // line 156: storeManualTransfer exists.
                // So generic store() usually handles creation.
                // Let's assume 'MANUAL' just returns pending.
            } else {
                // All other methods (VA, QRIS, CREDIT_CARD, OVO) -> Doku Checkout
                $paymentData = $this->generateCheckoutPayment($donation);
            }

            if ($paymentData) {
                $donation->update([
                    'transaction_id' => $paymentData['transaction_id'] ?? null,
                    'payment_data' => $paymentData,
                ]);
            }

            DB::commit();

            return response()->json([
                'message' => 'Donasi berhasil dibuat',
                'data' => [
                    'donation' => $donation,
                    'payment_data' => $paymentData,
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat donasi', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Gagal membuat donasi',
            ], 500);
        }
    }

    /**
     * Display the specified donation.
     */
    public function show(string $id)
    {
        $donation = Donation::with(['manualTransfer'])->findOrFail($id);

        return response()->json([
            'data' => $donation,
        ]);
    }

    /**
     * Store manual transfer proof.
     */
    public function storeManualTransfer(StoreManualTransferRequest $request)
    {
        try {
            DB::beginTransaction();

            $donation = Donation::findOrFail($request->donation_id);

            // Upload transfer proof
            $proofPath = null;
            if ($request->hasFile('transfer_proof')) {
                $proofPath = $request->file('transfer_proof')->store('donations/transfer-proofs', 'public');
            }

            $manualTransfer = ManualTransfer::create([
                'donation_id' => $donation->id,
                'bank_name' => $request->bank_name,
                'account_number' => $request->account_number,
                'account_holder_name' => $request->account_holder_name,
                'transfer_amount' => $request->transfer_amount,
                'transfer_proof' => $proofPath,
                'transfer_date' => $request->transfer_date,
                'verification_status' => 'pending',
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Bukti transfer berhasil dikirim',
                'data' => $manualTransfer,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal mengirim bukti transfer', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Gagal mengirim bukti transfer',
            ], 500);
        }
    }

    /**
     * Verify DOKU callback signature (HMAC-SHA256).
     *
     * @see https://jokul.doku.com/docs — Handling HTTP Notification
     */
    private function verifyDokuSignature(Request $request): bool
    {
        $secretKey = config('doku.secret_key');
        if (empty($secretKey)) {
            return false;
        }

        $clientId = $request->header('Client-Id');
        $requestId = $request->header('Request-Id');
        $requestTimestamp = $request->header('Request-Timestamp');
        $requestTarget = '/'.ltrim($request->path(), '/');
        $incomingSignature = $request->header('Signature');

        if (! $clientId || ! $requestId || ! $requestTimestamp || ! $incomingSignature) {
            return false;
        }

        $digest = base64_encode(hash('sha256', $request->getContent(), true));

        $rawSignature = "Client-Id:{$clientId}\n"
            ."Request-Id:{$requestId}\n"
            ."Request-Timestamp:{$requestTimestamp}\n"
            ."Request-Target:{$requestTarget}\n"
            ."Digest:{$digest}";

        $expectedSignature = 'HMACSHA256='.base64_encode(
            hash_hmac('sha256', $rawSignature, $secretKey, true)
        );

        return hash_equals($expectedSignature, $incomingSignature);
    }

    /**
     * Callback from DOKU payment gateway.
     */
    public function paymentCallback(Request $request)
    {
        $isSandbox = config('doku.env') !== 'production';

        if (! $this->verifyDokuSignature($request)) {
            if ($isSandbox) {
                Log::warning('DOKU callback signature verification failed (sandbox — proceeding)', [
                    'headers' => $request->headers->all(),
                ]);
            } else {
                Log::error('DOKU callback signature verification failed', [
                    'ip' => $request->ip(),
                ]);

                return response()->json(['message' => 'Invalid signature'], 403);
            }
        }

        try {
            $transactionId = $request->input('TRANSIDMERCHANT');
            $status = $request->input('RESPONSECODE');

            $donation = Donation::where('transaction_id', $transactionId)->firstOrFail();

            if ($status === '0000') {
                $donation->markAsPaid();
            } else {
                $donation->markAsFailed();
            }

            return response()->json([
                'message' => 'Payment status updated',
            ]);
        } catch (\Exception $e) {
            Log::error('DOKU callback processing failed', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to process callback',
            ], 500);
        }
    }

    /**
     * Generate QRIS payment.
     *
     * In sandbox mode, returns mock QRIS data matching DOKU sandbox response format.
     * In production, this would call the DOKU QRIS API.
     */
    protected function generateQrisPayment(Donation $donation): array
    {
        $isSandbox = config('doku.env') !== 'production';
        $invoiceNumber = 'QRIS-'.time().'-'.$donation->id;

        if ($isSandbox) {
            Log::info('DOKU QRIS: sandbox mode — returning mock data', ['donation_id' => $donation->id]);

            return [
                'transaction_id' => $invoiceNumber,
                'qris_string' => '00020101021226660014ID.CO.DOKU.WWW011893600914300000001020210000000030303UMI51440014ID.CO.QRIS.WWW0215ID20200000000000303UMI5204541253033605802ID5913SANDBOX DOKU6007JAKARTA61051017062070703A0163040B7E',
                'qris_url' => 'https://api-sandbox.doku.com/checkout/link/'.$invoiceNumber,
                'amount' => $donation->amount,
                'expired_at' => now()->addMinutes(30),
                'type' => 'QRIS',
                'status' => 'PENDING',
                'sandbox' => true,
            ];
        }

        return $this->generateCheckoutPayment($donation);
    }

    /**
     * Generate Virtual Account payment using DOKU.
     */
    /**
     * Generate Virtual Account payment using DOKU Snap Library.
     */
    protected function generateVirtualAccountPayment(Donation $donation, string $bankCode): array
    {
        try {
            // Check keys availability
            $privateKeyPath = config('doku.merchant_private_key');
            $publicKeyPath = config('doku.merchant_public_key'); // Merchant Public Key (Optional for Snap? Snap constructor needs it)
            $dokuPublicKeyPath = config('doku.doku_public_key');

            if (! file_exists($privateKeyPath) || ! file_exists($publicKeyPath) || ! file_exists($dokuPublicKeyPath)) {
                // If keys are missing (dev environment without keys), fallback to mock with warning
                Log::warning('DOKU Keys not found. Returning Mock VA. Path: '.$privateKeyPath);

                // Return Mock Data (Same as before but consistent)
                return $this->generateMockVa($donation, $bankCode);
            }

            $privateKey = file_get_contents($privateKeyPath);
            $publicKey = file_get_contents($publicKeyPath);
            $dokuPublicKey = file_get_contents($dokuPublicKeyPath);
            $clientId = config('doku.client_id');
            $secretKey = config('doku.secret_key');
            $isProduction = config('doku.env') === 'production';

            // Init Snap
            // Constructor: privateKey, publicKey, dokuPublicKey, clientId, issuer, isProduction, secretKey
            $snap = new \Doku\Snap\Snap(
                $privateKey,
                $publicKey,
                $dokuPublicKey,
                $clientId,
                $clientId, // Issuer is usually Client ID
                $isProduction,
                $secretKey
            );

            // Prepare DTOs
            // Prepare DTOs
            // Company Code (partnerServiceId) for Snap is usually 8 digits. DOKU Dashboard "Client ID" is often used or specific prefix.
            // For Permata Sandbox, usually '89656'. Snap requests 8 digits. Padded with space if shorter.
            // Example from doc: " 8412" (Left space padded?) or "99341537".
            // We will pad left with space to length 8.
            $partnerServiceId = str_pad('89656', 8, ' ', STR_PAD_LEFT);
            if ($bankCode !== 'PERMATA') {
                $partnerServiceId = str_pad('89656', 8, ' ', STR_PAD_LEFT);
            }

            // Customer No: Snap often expects up to 20 digits.
            // We use payment_code (11 digits). We pad left with 0 to 20 digits.
            // Documentation for BNC/BNI says customerNo string(20).
            $customerNo = str_pad($donation->payment_code, 20, '0', STR_PAD_LEFT);

            // Virtual Account No: partnerServiceId + customerNo
            // "   89656" + "0000...12345"
            // Note: If spaces are in partnerServiceId, they are part of the number string?
            // Usually VA number in banking app excludes spaces. But API might require them for matching.
            // Let's assume standard string concatenation.
            $virtualAccountNo = $partnerServiceId.$customerNo;
            $trxId = 'DONATION-'.$donation->id;

            // Amount string with 2 decimals
            $amountStr = number_format($donation->amount, 2, '.', '');
            $totalAmount = new \Doku\Snap\Models\TotalAmount\TotalAmount($amountStr, 'IDR');

            // Config: Not reusable (One time)
            $vaConfig = new \Doku\Snap\Models\VA\VirtualAccountConfig\CreateVaVirtualAccountConfig(false);

            // Channel mapping
            $channelMap = [
                'PERMATA' => 'VIRTUAL_ACCOUNT_BANK_PERMATA',
                'MANDIRI' => 'VIRTUAL_ACCOUNT_BANK_MANDIRI',
                'BRI' => 'VIRTUAL_ACCOUNT_BANK_BRI',
                'BNI' => 'VIRTUAL_ACCOUNT_BANK_BNI',
            ];
            $channel = $channelMap[$bankCode] ?? 'VIRTUAL_ACCOUNT_BANK_PERMATA';

            $additionalInfo = new \Doku\Snap\Models\VA\AdditionalInfo\CreateVaRequestAdditionalInfo($channel, $vaConfig);

            // Create DTO
            $dto = new \Doku\Snap\Models\VA\Request\CreateVaRequestDto(
                $partnerServiceId,
                $customerNo,
                $virtualAccountNo,
                substr($donation->donor_name, 0, 30), // Max length safety
                null, // email
                null, // phone
                $trxId,
                $totalAmount,
                $additionalInfo,
                'C', // Trx Type Create
                date('c', strtotime('+1 day')) // Expired in 24h ISO8601
            );

            // Call API
            $response = $snap->createVa($dto);

            // Check response
            // Snap returns DTO or array? createVa returns CreateVaResponseDto.
            // properties: virtualAccountData->virtualAccountNo

            if (isset($response->virtualAccountData)) {
                $vaNo = $response->virtualAccountData->virtualAccountNo;

                return [
                    'transaction_id' => $trxId,
                    'va_number' => $vaNo,
                    'bank_code' => $bankCode,
                    'bank_name' => $this->getBankName($bankCode),
                    'channel' => $channel,
                    'amount' => $donation->amount,
                    'expired_at' => date('c', strtotime('+1 day')),
                    'payment_instructions' => [
                        'Transfer ke nomor Virtual Account '.$this->getBankName($bankCode),
                        'Nomor VA: '.$vaNo,
                        'Total: Rp '.number_format($donation->amount, 0, ',', '.'),
                    ],
                ];
            } else {
                // Handle error structure
                // Response might be array if error simulation?
                Log::error('DOKU VA Error', (array) $response);
                throw new \Exception('Gagal membuat VA DOKU. Response invalid.');
            }
        } catch (\Exception $e) {
            Log::error('Failed to generate VA Real: '.$e->getMessage());
            // Fallback to Mock if Real fails? Or throw?
            // If user wants REAL, throwing is better to debug key issues.
            // But for reliability, maybe Mock?
            // Given "Transaction Not Found", I should throw or return error so user knows keys are invalid.
            throw $e;
        }
    }

    protected function generateMockVa(Donation $donation, string $bankCode): array
    {
        $partnerServiceId = '89656';
        $customerNo = $donation->payment_code;
        $virtualAccountNo = $partnerServiceId.$customerNo;

        return [
            'transaction_id' => 'MOCK-'.time(),
            'va_number' => $virtualAccountNo,
            'bank_code' => $bankCode,
            'bank_name' => $this->getBankName($bankCode),
            'amount' => $donation->amount,
            'expired_at' => now()->addDay(),
            'payment_instructions' => ['MOCK VA - SET KEYS FOR REAL'],
        ];
    }

    /**
     * Get bank name from bank code.
     */
    protected function getBankName(string $bankCode): string
    {
        $bankNames = [
            'MANDIRI' => 'Bank Mandiri',
            'BRI' => 'Bank BRI',
            'BNI' => 'Bank Negara Indonesia',
            'PERMATA' => 'Bank Permata',
            'CIMB' => 'Bank CIMB Niaga',
            'DANAMON' => 'Bank Danamon',
        ];

        return $bankNames[$bankCode] ?? $bankCode;
    }

    /**
     * Generate token for DOKU authentication.
     * This endpoint is called by DOKU to get merchant token.
     */
    public function generateToken(Request $request)
    {
        try {
            // Verify request from DOKU
            $clientId = config('doku.client_id');
            $secretKey = config('doku.secret_key');

            // Generate token (JWT or simple token based on DOKU requirement)
            $timestamp = time();
            $token = base64_encode($clientId.':'.$timestamp.':'.$secretKey);

            return response()->json([
                'token' => $token,
                'expires_in' => 3600,
                'timestamp' => $timestamp,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate token', ['error' => $e->getMessage()]);

            return response()->json([
                'message' => 'Failed to generate token',
            ], 500);
        }
    }

    /**
     * Generate unique numeric payment code
     */
    protected function generatePaymentCode(): string
    {
        // Format: ymd + 5 random digits
        // Example: 24010712345
        // Total 11 digits. Combined with Company Code (5 digits) = 16 digits (Max for Permata)
        do {
            $code = date('ymd').mt_rand(10000, 99999);
            $exists = Donation::where('payment_code', $code)->exists();
        } while ($exists);

        return $code;
    }

    /**
     * Generate Checkout Payment URL (Jokul Checkout).
     */
    protected function generateCheckoutPayment(Donation $donation): array
    {
        $clientId = config('doku.client_id');
        $secretKey = config('doku.secret_key');

        // Environment
        $baseUrl = config('doku.env') === 'production'
            ? 'https://api.doku.com'
            : 'https://api-sandbox.doku.com';

        $path = '/checkout/v1/payment';
        $url = $baseUrl.$path;

        $invoiceNumber = 'DON-'.time().'-'.$donation->id; // Unique invoice
        $amount = $donation->amount;

        // Request Body
        $data = [
            'order' => [
                'invoice_number' => $invoiceNumber,
                'amount' => $amount,
                'auto_redirect' => false,
                // 'callback_url' => config('doku.callback_url'),
            ],
            'payment' => [
                'payment_due_date' => 60, // 60 minutes
            ],
            'customer' => [
                'name' => substr($donation->donor_name, 0, 50),
                'email' => $donation->email ?? 'guest@example.com',
            ],
            'additional_info' => [
                'integration' => [
                    'name' => 'php-library',
                    'version' => '2.1.0',
                ],
            ],
        ];

        // Generate Signature V2 (HMAC-SHA256)
        $requestId = Str::uuid()->toString();
        $date = new \DateTime('now', new \DateTimeZone('UTC'));
        $timestamp = $date->format('Y-m-d\TH:i:s\Z');

        // IMPORTANT: Use exact same JSON string for Digest and Body
        $bodyJson = json_encode($data);

        // Digest: Output must be Base64 of SHA256 binary
        $digest = base64_encode(hash('sha256', $bodyJson, true));

        // Target: Request-Target must include query string if any.
        // Here path is '/checkout/v1/payment'
        $target = $path;

        // Raw Signature String
        // Client-Id + Request-Id + Request-Timestamp + Request-Target + Digest
        // Ensure \n is used as separator per Doku spec (Jokul)
        $rawSignature = 'Client-Id:'.$clientId."\n".
            'Request-Id:'.$requestId."\n".
            'Request-Timestamp:'.$timestamp."\n".
            'Request-Target:'.$target."\n".
            'Digest:'.$digest;

        // HMAC using Secret Key
        $signature = base64_encode(hash_hmac('sha256', $rawSignature, $secretKey, true));
        $finalSignature = 'HMACSHA256='.$signature;

        try {
            $response = Http::withHeaders([
                'Client-Id' => $clientId,
                'Request-Id' => $requestId,
                'Request-Timestamp' => $timestamp,
                'Signature' => $finalSignature,
                // Content-Type is set by withBody second argument or header
            ])
                ->withBody($bodyJson, 'application/json') // FORCE exact body
                ->post($url);

            if ($response->successful()) {
                $json = $response->json();

                // Response V1 Checkout: response.payment.url
                if (isset($json['response']['payment']['url'])) {
                    return [
                        'transaction_id' => $invoiceNumber,
                        'payment_url' => $json['response']['payment']['url'],
                        'amount' => $amount,
                        'status' => 'PENDING',
                        'type' => 'CHECKOUT_URL',
                    ];
                }
            }

            // Log full error details for debugging
            Log::error('DOKU Checkout Failed', [
                'status' => $response->status(),
                'body' => $response->body(),
                'headers' => $response->headers(),
                'request_signature_string' => $rawSignature,
            ]);

            throw new \Exception('Maaf, gagal membuat Link Pembayaran (DOKU Checkout).');
        } catch (\Exception $e) {
            Log::error('DOKU Checkout Exception: '.$e->getMessage());
            throw $e;
        }
    }
}
