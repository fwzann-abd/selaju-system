<?php

require __DIR__ . '/vendor/autoload.php';

$app = require __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

echo "Sending test email...\n";

try {
    Mail::raw('Test email dari selaju-system at ' . now(), function($m) {
        $m->to('presetpjr@gmail.com')
          ->subject('Test Selaju System - ' . date('Y-m-d H:i:s'));
    });
    echo "✓ Email sent successfully!\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Code: " . $e->getCode() . "\n";
}
