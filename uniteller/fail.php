<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>Заказ не оплачен</title>
</head>
<body>

<?php
include_once dirname(__FILE__)."/uniteller.php";

$order_id = $_REQUEST["Order_ID"];
LogToScreen("Заказ " . $order_id . " не оплачен");

LogToScreen("Request");
var_dump($_REQUEST);
LogToScreen("Server");
var_dump($_SERVER);
LogToScreen("Session");
var_dump($_SESSION);

$status = Uniteller::GetPaymentResult($order_id);
var_dump($status);

?>

</body>
</html>
