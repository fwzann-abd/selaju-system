<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.required' => 'Kolom nama wajib diisi.',
            'name.max' => '422: Nama maksimal :max karakter.',
            'email.required' => 'Kolom email wajib diisi.',
            'email.email' => '422: Format email tidak valid.',
            'email.unique' => '422: Email sudah terdaftar di sistem.',
            'email.lowercase' => '422: Email harus berupa huruf kecil.',
            'password.required' => 'Kolom password wajib diisi.',
            'password.confirmed' => '422: Konfirmasi password tidak cocok.',
            'password.min' => '422: Password minimal :min karakter.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        return redirect()->route('login')->with('status', 'Pendaftaran berhasil! Silakan login dengan akun Anda.');
    }
}
