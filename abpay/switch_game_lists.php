<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>
    <style>
        #loading {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 20px;
            color: white;
            background-color: rgba(0, 0, 0, 0.5);
            padding: 10px;
            border-radius: 5px;
            z-index: 1000;
        }
    </style>
    <title>後台訂單管理</title>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <?php
                        require_once 'menu_temp.php';
                        ?>
                        <!-- 其他菜單項目 -->
                    </ul>
                </div>
            </nav>

            <!-- 主要內容 -->
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">控制遊戲開關</h1>
                </div>

                <!-- 遊戲列表 -->
                <div class="container">
                    <div id="loading" style="display: none;">讀取中...</div>
                    <div id="data"></div>
                    <table class="table table-striped table-bordered" id="data-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                            </tr>
                        </thead>
                        <tbody id="gameTbody">
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            <button class="btn btn-primary" id="select-all">全選</button>
                            <button class="btn btn-default" id="deselect-all">全不選</button>
                            <button class="btn btn-success" id="update-games">更新遊戲列表(把系統的遊戲列表更新過來)</button>
                            <button class="btn btn-info" id="update-selected">更新選取項目</button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


</body>
<script>
    $(document).ready(function () {
        start();
        // 設置全局攔截器
        axios.interceptors.request.use(function (config) {
            document.getElementById('loading').style.display = 'block'; // 顯示加載動畫
            return config;
        }, function (error) {
            document.getElementById('loading').style.display = 'none'; // 隱藏加載動畫
            return Promise.reject(error);
        });

        axios.interceptors.response.use(function (response) {
            document.getElementById('loading').style.display = 'none'; // 隱藏加載動畫
            return response;
        }, function (error) {
            document.getElementById('loading').style.display = 'none'; // 隱藏加載動畫
            return Promise.reject(error);
        });

    });



    function start() {


        document.getElementById('loading').style.display = 'block'; // 顯示加載動畫

        //資料庫有沒有資料，flase就是無資料，true就是有資料
        dataNoEmptyFlag = false;

        // 先看資料庫有沒有資料
        getSwitchGameLists();
    }

    function getSwitchGameLists() {
        axios.get('get_switch_game_lists.php')
            .then(function (response) {

                if (response.data === false) {
                    //無資料
                } else {
                    dataNoEmptyFlag = true;

                    setDataToTable(response.data);
                    document.getElementById('loading').style.display = 'none'; // 隱藏加載動畫
                }
            })
            .catch(function (error) {
                // 處理錯誤
                console.error(error);
                document.getElementById('loading').style.display = 'none'; // 隱藏加載動畫
            });
    }

    function setDataToTable(data) {

        // 先清空原本的資料
        $('#data-table tbody').empty();

        // 遍歷 JSON 資料並生成表格
        $.each(data, function (index, item) {
            var row = $('<tr>');
            var checkbox = $('<input type="checkbox" name="selectedItems" value="' + item.Sid + '">');
            if (item.flag == false) {
                checkbox.prop('checked', false);
            } else {
                checkbox.prop('checked', true);
            }

            row.append($('<td>').append(checkbox));
            row.append($('<td>').text(item.Name));

            $('#data-table tbody').append(row);
        });


        let table = new DataTable('#data-table', {
            destroy: true,
            // config options...
        });
    }

    // 全選/全不選功能
    $('#select-all').click(function () {
        $('input[name="selectedItems"]').prop('checked', true);
    });

    $('#deselect-all').click(function () {
        $('input[name="selectedItems"]').prop('checked', false);
    });

    // 更新遊戲列表功能
    $('#update-games').click(function () {

        axios.get('../getGameList.php')
            .then(function (response) {
                // 向 PHP 後端發送請求更新資料庫
                axios.post('update_games.php', response.data)
                    .then(function (response) {
                        alert('遊戲列表已更新!');
                        //重新讀取datatable
                        getSwitchGameLists();
                    })
                    .catch(function (error) {
                        alert('更新遊戲列表時發生錯誤: ' + error);
                    });
            })

            .catch(function (error) {
                alert('無法獲取遊戲列表: ' + error);
            });
    });

    // 更新選取項目功能
    $('#update-selected').click(function () {
        var selectedItemSids = [];
        var selectedItemFlags = [];
        $('input[name="selectedItems"]').each(function () {
            var sid = $(this).val();
            var flag = $(this).prop('checked') ? 1 : 0;
            selectedItemSids.push(sid);
            selectedItemFlags.push(flag);
        });

        axios.post('update_switch_game_lists_selected.php', { selectedSids: selectedItemSids, selectedFlags: selectedItemFlags })
            .then(function (response) {
                alert('選取項目已更新!');
            })
            .catch(function (error) {
                alert('更新選取項目時發生錯誤: ' + error);
            });
    });
</script>

</html>