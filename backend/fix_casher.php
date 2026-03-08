<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JobPosting;

$job = JobPosting::where('title', 'Casher')->latest()->first();
if ($job) {
    echo "Updating Casher (ID: {$job->id}) deadline to 2026-05-28...\n";
    $job->update(['deadline' => '2026-05-28']);
    echo "Done.\n";
} else {
    echo "Casher job not found.\n";
}
