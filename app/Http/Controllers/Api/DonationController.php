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
    protected function generateVirtualAccountPayment(Donation $donation, string $bankCode): array
    {
        try {
            // Map bank code to DOKU channel format
            // Currently using Bank Permata (DGPC - FIX_BILL, Company Code: 89656)
            $channelMap = [
                'PERMATA' => 'VIRTUAL_ACCOUNT_BANK_PERMATA',
            ];

            $channel = $channelMap[$bankCode] ?? 'VIRTUAL_ACCOUNT_BANK_PERMATA';
            $companyCode = '89656'; // Company Code for Permata from DOKU dashboard
            $prefix = 'Galactic'; // Merchant Prefix from DOKU dashboard

            // Use payment_code (numeric) for VA number generation
            // Ensure payment_code exists, otherwise fallback to something safe or throw
            $customerNo = str_pad($donation->payment_code ?? time(), 10, '0', STR_PAD_LEFT);
            $virtualAccountNo = $companyCode . $customerNo; // Format: {companyCode}{customerNo}
            $transactionId = 'DONATION-' . $donation->id . '-' . time();
            $expiredDate = now()->addHours(24)->format('Y-m-d\\TH:i:sP');

            // For now, return formatted data structure (actual DOKU API call will be implemented)
            // TODO: Implement actual DOKU SDK createVa call when ready
            return [
                'transaction_id' => $transactionId,
                'va_number' => $virtualAccountNo,
                'bank_code' => $bankCode,
                'bank_name' => $this->getBankName($bankCode),
                'channel' => $channel,
                'company_code' => $companyCode,
                'merchant_prefix' => $prefix,
                'billing_type' => 'FIX_BILL',
                'feature' => 'DGPC',
                'amount' => $donation->amount,
                'customer_name' => $donation->donor_name,
                'expired_at' => $expiredDate,
                'payment_instructions' => [
                    'Transfer ke nomor Virtual Account Bank Permata di atas',
                    'Jumlah transfer harus SESUAI PERSIS dengan nominal: Rp ' . number_format((float) $donation->amount, 0, ',', '.'),
                    'Virtual Account berlaku hingga ' . now()->addHours(24)->format('d/m/Y H:i'),
                    'Pembayaran akan otomatis dikonfirmasi setelah transfer berhasil',
                ],
            ];
        } catch (\Exception $e) {
            Log::error('Failed to generate VA: ' . $e->getMessage());
            throw $e;
        }
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
