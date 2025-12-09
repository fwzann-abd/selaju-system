@props(['level' => 'info'])

@php
    // Keep this simple for broader PHP compatibility
    if ($level === 'success') {
        $style = 'background-color: #22c55e; border-color: #22c55e;';
    } elseif ($level === 'error') {
        $style = 'background-color: #ef4444; border-color: #ef4444;';
    } else {
        $style = 'background-color: #334155; border-color: #334155;';
    }

    $fontFamily = "font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, 'Noto Sans', 'Liberation Sans', 'Lucida Grande', sans-serif;";
@endphp

<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>{{ config('app.name') }}</title>
    </head>
    <body style="margin:0;padding:0;{{ $fontFamily }};background-color:#f1f5f9;color:#0f172a;">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9;width:100%;">
            <tr>
                <td align="center" style="padding:40px 0;">
                    <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:6px;overflow:hidden;">
                        <tr>
                            <td style="padding:24px 32px 0 32px;text-align:left;">
                                {{-- Optional logo area --}}
                                @if (file_exists(public_path('assets/images/logo.png')))
                                    <img src="{{ asset('assets/images/logo.png') }}" alt="{{ config('app.name') }}" style="max-height:48px;">
                                @endif
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:24px 32px 0 32px;">
                                <h1 style="margin:0 0 12px 0;font-size:20px;line-height:1.25;color:#0f172a;font-weight:700;">Halo! 👋</h1>
                                <p style="margin:0 0 18px 0;color:#6b7280;line-height:1.6;">Terima kasih sudah mendaftar di Selaju! Untuk menyelesaikan pendaftaran, silakan klik tombol di bawah ini untuk memverifikasi email Anda.</p>

                                @isset($actionText)
                                    <table role="presentation" cellpadding="0" cellspacing="0" style="margin:16px 0 24px 0;width:100%;">
                                        <tr>
                                            <td align="center">
                                                <a href="{{ $actionUrl }}" style="display:inline-block;padding:12px 28px;border-radius:6px;color:#fff;text-decoration:none;font-weight:600;{{ $style }}">{{ $actionText }}</a>
                                            </td>
                                        </tr>
                                    </table>
                                @endisset

                                <p style="margin:0 0 18px 0;color:#6b7280;line-height:1.6;">Jika Anda tidak membuat akun ini, abaikan email ini dan tidak ada tindakan yang diperlukan.</p>

                                <p style="margin:0 0 28px 0;color:#6b7280;line-height:1.6;">Semangat! 🚀<br><strong>{{ config('app.name') }}</strong></p>

                                <hr style="border:none;border-top:1px solid #e6eef6;margin:0 0 18px 0;">

                                <p style="margin:18px 0 0 0;color:#94a3b8;font-size:13px;">
                                    Jika tombol di atas tidak berfungsi, salin dan tempel URL berikut ke browser Anda:
                                </p>

                                <p style="word-break:break-all;margin:8px 0 0 0;color:#3b82f6;font-size:13px;">{{ $actionUrl }}</p>
                            </td>
                        </tr>

                        <tr>
                            <td style="padding:16px 32px 24px 32px;background-color:#f8fafc;color:#94a3b8;font-size:12px;text-align:center;">
                                &copy; {{ date('Y') }} {{ config('app.name') }}. Semua hak dilindungi.
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
</html>
