<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\StoreManualTransferRequest;
use App\Models\Donation;
use App\Models\ManualTransfer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                'donations' => $donations->map(fn($donation) => [
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
            if ($request->payment_method === 'qris') {
                $paymentData = $this->generateQrisPayment($donation);
            } elseif ($request->payment_method === 'virtual_account') {
                $paymentData = $this->generateVirtualAccountPayment($donation, $request->bank_code);
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

            return response()->json([
                'message' => 'Gagal membuat donasi',
                'error' => $e->getMessage(),
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

            return response()->json([
                'message' => 'Gagal mengirim bukti transfer',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Callback from DOKU payment gateway.
     */
    public function paymentCallback(Request $request)
    {
        // Verify DOKU signature
        // TODO: Implement DOKU signature verification

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
            return response()->json([
                'message' => 'Failed to process callback',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate QRIS payment using DOKU.
     */
    protected function generateQrisPayment(Donation $donation): array
    {
        // TODO: Implement DOKU QRIS generation when library is installed
        // For now, return dummy data
        return [
            'transaction_id' => 'TRX-' . time() . '-' . $donation->id,
            'qris_string' => 'dummy_qris_string',
            'qris_url' => 'https://example.com/qris.png',
            'expired_at' => now()->addMinutes(30),
        ];
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

            if (!file_exists($privateKeyPath) || !file_exists($publicKeyPath) || !file_exists($dokuPublicKeyPath)) {
                // If keys are missing (dev environment without keys), fallback to mock with warning
                Log::warning('DOKU Keys not found. Returning Mock VA. Path: ' . $privateKeyPath);
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
            $virtualAccountNo = $partnerServiceId . $customerNo;
            $trxId = 'DONATION-' . $donation->id;

            // Amount string with 2 decimals
            $amountStr = number_format($donation->amount, 2, '.', '');
            $totalAmount = new \Doku\Snap\Models\TotalAmount\TotalAmount($amountStr, 'IDR');

            // Config: Not reusable (One time)
            $vaConfig = new \Doku\Snap\Models\VA\VirtualAccountConfig\CreateVaVirtualAccountConfig(false);

            // Channel mapping
            $channelMap = [
                'PERMATA' => 'VIRTUAL_ACCOUNT_BANK_PERMATA',
                'MANDIRI' => 'VIRTUAL_ACCOUNT_BANK_MANDIRI',
                'BRI'     => 'VIRTUAL_ACCOUNT_BANK_BRI',
                'BNI'     => 'VIRTUAL_ACCOUNT_BANK_BNI',
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
                        'Transfer ke nomor Virtual Account ' . $this->getBankName($bankCode),
                        'Nomor VA: ' . $vaNo,
                        'Total: Rp ' . number_format($donation->amount, 0, ',', '.'),
                    ]
                ];
            } else {
                // Handle error structure
                // Response might be array if error simulation?
                Log::error('DOKU VA Error', (array)$response);
                throw new \Exception('Gagal membuat VA DOKU. Response invalid.');
            }
        } catch (\Exception $e) {
            Log::error('Failed to generate VA Real: ' . $e->getMessage());
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
        $virtualAccountNo = $partnerServiceId . $customerNo;

        return [
            'transaction_id' => 'MOCK-' . time(),
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
            $token = base64_encode($clientId . ':' . $timestamp . ':' . $secretKey);

            return response()->json([
                'token' => $token,
                'expires_in' => 3600,
                'timestamp' => $timestamp,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to generate token',
                'error' => $e->getMessage(),
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
            $code = date('ymd') . mt_rand(10000, 99999);
            $exists = Donation::where('payment_code', $code)->exists();
        } while ($exists);

        return $code;
    }
}
