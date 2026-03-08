<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JobPosting;

$jobs = JobPosting::all(['id', 'title', 'deadline']);
foreach ($jobs as $job) {
    echo "ID: {$job->id}, Title: {$job->title}, Deadline: " . ($job->deadline ?? 'NULL') . "\n";
}
