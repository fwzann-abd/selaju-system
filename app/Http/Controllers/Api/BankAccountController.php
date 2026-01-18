<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;

class BankAccountController extends Controller
{
    public function index()
    {
        $accounts = BankAccount::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->map(function ($account) {
                return [
                    'id' => $account->id,
                    'bank_name' => $account->bank_name,
                    'account_name' => $account->account_name,
                    'account_number' => $account->account_number,
                    'logo' => $account->logo ? url('assets/modules/bank-accounts/logos/' . $account->logo) : null,
                ];
            });

        return response()->json([
            'data' => $accounts,
        ]);
    }
}
