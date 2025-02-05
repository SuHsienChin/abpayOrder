<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Connection: keep-alive');

// 模擬一次廣播通知
broadcast("This is a broadcast notification");

function broadcast($message) {
    echo "data: $message\n\n";
    ob_flush();
    flush();
}
?>