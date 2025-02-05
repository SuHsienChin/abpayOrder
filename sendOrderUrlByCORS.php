<?php
require_once 'getApiJsonClass.php';

$url = 'http://www.adp.idv.tw/api/Order?';

// 檢查是否有 GET 參數存在
if (isset($_GET) && !empty($_GET)) {
    // 迭代所有 GET 參數
    foreach ($_GET as $key => $value) {
        // 輸出每個 GET 參數的名稱和值
        $url = $url . $key . '=' . $value . '&';
    }
    $url = substr($url, 0, -1);
}

$curlRequest = new CurlRequest($url);
$response = $curlRequest->sendRequest();

$data = json_decode($response, true);

if ($data === null) {
    die("無法取得API資料");
}
ob_start();
header('Content-Type: application/json');
ob_end_flush();
echo json_encode($data);
