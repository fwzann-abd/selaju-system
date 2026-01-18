<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Spatie\Image\Image;

class BankAccountController extends Controller
{
    public function index()
    {
        $bankAccounts = BankAccount::orderBy('order')->paginate(15);

        return view('admin.bank-accounts.index', compact('bankAccounts'));
    }

    public function create()
    {
        return view('admin.bank-accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:255'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:50'],
            'logo' => ['nullable', 'image', 'max:200'],
            'is_active' => ['boolean'],
        ]);

        $logo = null;
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = public_path('assets/modules/bank-accounts/logos');

            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }

            Image::load($file)
                ->quality(80)
                ->save($path . '/' . $filename);

            $logo = $filename;
        }

        BankAccount::create([
            'bank_name' => $validated['bank_name'],
            'account_name' => $validated['account_name'],
            'account_number' => $validated['account_number'],
            'logo' => $logo,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.bank-accounts.index')->with('success', 'Akun Bank berhasil ditambahkan');
    }

    public function show(BankAccount $bankAccount)
    {
        return view('admin.bank-accounts.show', compact('bankAccount'));
    }

    public function edit(BankAccount $bankAccount)
    {
        return view('admin.bank-accounts.edit', compact('bankAccount'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:255'],
            'account_name' => ['required', 'string', 'max:255'],
            'account_number' => ['required', 'string', 'max:50'],
            'logo' => ['nullable', 'image', 'max:200'],
            'is_active' => ['boolean'],
        ]);

        if ($request->hasFile('logo')) {
            // Delete old logo
            if ($bankAccount->logo) {
                $oldPath = public_path('assets/modules/bank-accounts/logos/' . $bankAccount->logo);
                if (file_exists($oldPath)) {
                    unlink($oldPath);
                }
            }

            // Upload new logo
            $file = $request->file('logo');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
            $path = public_path('assets/modules/bank-accounts/logos');

            if (!is_dir($path)) {
                mkdir($path, 0755, true);
            }

            Image::load($file)
                ->quality(80)
                ->save($path . '/' . $filename);

            $validated['logo'] = $filename;
        }

        $bankAccount->update($validated);

        return redirect()->route('admin.bank-accounts.index')->with('success', 'Akun Bank berhasil diperbarui');
    }

    public function destroy(BankAccount $bankAccount)
    {
        // Delete logo
        if ($bankAccount->logo) {
            $path = public_path('assets/modules/bank-accounts/logos/' . $bankAccount->logo);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $bankAccount->delete();

        return redirect()->route('admin.bank-accounts.index')->with('success', 'Akun Bank berhasil dihapus');
    }
}
