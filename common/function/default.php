<?php

function logger($content, $commitNow = true) {
    global $logs;
    $logs = empty($logs) ? [] : $logs;
    $logs[] = $content;
    if($commitNow) {
        foreach($logs as $log) {
            $filename = "hour_" . date('Y-m-d_H') . ".log";
            file_put_contents(LOG_ROOT . '/' . $filename, $log . "\n", FILE_APPEND);
        }
        $logs = [];
    }
}
