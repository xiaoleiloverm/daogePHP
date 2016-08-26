<?php
function asdf()
{
    echo microtime(true) . '<br>';
    sleep(1);
    echo microtime(true) . '<br>';
    sleep(1);
    echo microtime(true) . '<br>';
}
register_shutdown_function('asdf');
set_time_limit(1);

while (true) {}
