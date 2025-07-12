<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$lockFile = __DIR__ . '/time_transaction_queue.lock';

// Check if the lock file exists
// if (file_exists($lockFile)) {
//     $pid = (int) file_get_contents($lockFile);

//     // Check if the process is still running
//     if (posix_kill($pid, 0)) {
//         echo "Another instance is already running (PID: $pid).\n";
//         exit;
//     } else {
//         echo "Stale lock file detected. Removing it.\n";
//         unlink($lockFile);
//     }
// }

// // Write the current process ID to the lock file
// file_put_contents($lockFile, getmypid());
// register_shutdown_function(function () use ($lockFile) {
//     if (file_exists($lockFile)) {
//         unlink($lockFile);
//     }
// });

while (true) {
    $currentSeconds = now()->second;

    if ($currentSeconds % 10 === 0) {
        $command = 'php artisan time-transaction-queue:handler > /dev/null 2>&1';
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $command = "start /B php artisan time-transaction-queue:handler";
        }
        exec($command);
        echo 'Async Command executed at ' . now() . PHP_EOL;

        sleep(1);
    }

    usleep(500000); // Avoid CPU overuse
}
