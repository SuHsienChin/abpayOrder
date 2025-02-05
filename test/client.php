<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventSource Broadcast Example</title>
</head>
<body>

<button id="startBtn">Start EventSource</button>

<script>
    let eventSource = null;

    document.getElementById('startBtn').addEventListener('click', startEventSource);

    function startEventSource() {
        // 檢查 EventSource 是否已經存在，避免重複建立
        if (!eventSource) {
            eventSource = new EventSource('server.php');

            // 處理通知事件
            eventSource.addEventListener('message', handleNotification);

            // 連接關閉事件
            eventSource.addEventListener('error', handleError);
        }
    }

    function handleNotification(event) {
        const message = event.data;
        console.log('Received new broadcast:', message);

        // 在這裡處理收到的通知，例如顯示提示或更新介面
        alert('Received new broadcast: ' + message);
    }

    function handleError(event) {
        console.log('EventSource connection closed');
        eventSource.close();
        eventSource = null;
    }
</script>

</body>
</html>
