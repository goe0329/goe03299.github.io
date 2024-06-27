<?php
$servername = "localhost";
$username = "root";  // XAMPP默認的MySQL用戶名是root
$password = "0927399987";  // XAMPP默認的MySQL用戶密碼是空
$dbname = "45_long";  // 使用你的資料庫名稱

// 創建連接
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連接
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $account = $_POST['account'];
    $amount = $_POST['amount'];
    $p_id = 44070; // 示例道具編號
    $p_name = "贊助道具"; // 示例道具名稱

    // 假設付款成功，將贊助資料寫入資料庫
    $sql = "INSERT INTO shop_user (p_id, p_name, count, account, isget, time) VALUES ('$p_id', '$p_name', '$amount', '$account', 0, NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "新紀錄創建成功";
    } else {
        echo "錯誤: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>贊助網頁</title>
    <script src="https://www.paypal.com/sdk/js?client-id=AdFLku7tBm4DSK2n3UJuQJoeeQq5SKvmc1e_s28Ltdi3el4HInw8ZCxGocLU_xwSgJEdHtDklVeCZHAg&currency=TWD"></script>
</head>
<body>
    <h1>贊助道具</h1>
    <form id="payment-form" method="post" action="">
        <label for="account">遊戲帳號:</label>
        <input type="text" id="account" name="account" required>
        <br><br>
        <label for="amount">金額:</label>
        <input type="number" id="amount" name="amount" required>
        <br><br>
        <div id="paypal-button-container"></div>
    </form>

    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                var amount = document.getElementById('amount').value;
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: amount,
                            currency_code: 'TWD' // 設置貨幣為新台幣
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    var account = document.getElementById('account').value;
                    var amount = document.getElementById('amount').value;

                    fetch('/donation_app/process-payment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            orderID: data.orderID,
                            account: account,
                            amount: amount,
                        }),
                    }).then(response => {
                        if (response.ok) {
                            alert('付款成功，已發放道具！');
                        } else {
                            alert('付款失敗，請重試。');
                        }
                    });
                });
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>
