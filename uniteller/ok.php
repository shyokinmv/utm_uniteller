<html>
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
<title>Успешная оплата</title>
</head>
<body>

<?php
include_once dirname(__FILE__)."/uniteller.php";

$order_id = $_REQUEST["Order_ID"];
logger("Заказ " . $order_id . " успешно оплачен");

LogArray($_REQUEST, "Ok: Request");
LogArray($_SERVER, "Ok: Server");
LogArray($_SESSION, "Ok: Session");

$status = Uniteller::GetPaymentResult($order_id);

//var_dump($status);
foreach ($status as $s){
    LogToScreen('----');
    //var_dump($s);
    LogToScreen('ordernumber: ' . $s->ordernumber);
    LogToScreen('response_code: ' . $s->response_code);
    LogToScreen('recommendation: ' . $s->recommendation);
    LogToScreen('message: ' . $s->message);
    LogToScreen('comment: ' . $s->comment);
    LogToScreen('date: ' . $s->date);
    LogToScreen('total: ' . $s->total);
    LogToScreen('currency: ' . $s->currency);
    LogToScreen('cardtype: ' . $s->cardtype);
    LogToScreen('cardnumber: ' . $s->cardnumber);
    LogToScreen('lastname: ' . $s->lastname);
    LogToScreen('firstname: ' . $s->firstname);
    LogToScreen('middlename: ' . $s->middlename);
    LogToScreen('address: ' . $s->address);
    LogToScreen('email: ' . $s->email);
    LogToScreen('country: ' . $s->country);
    LogToScreen('rate: ' . $s->rate);
    LogToScreen('approvalcode: ' . $s->approvalcode);
    LogToScreen('cardsubtype: ' . $s->cardsubtype);
    LogToScreen('cvc2: ' . $s->cvc2);
    LogToScreen('cardholder: ' . $s->cardholder);
    LogToScreen('ipaddress: ' . $s->ipaddress);
    LogToScreen('protocoltypename: ' . $s->protocoltypename);
    LogToScreen('billnumber: ' . $s->billnumber);
    LogToScreen('bankname: ' . $s->bankname);
    LogToScreen('status: ' . $s->status);
    LogToScreen('error_code: ' . $s->error_code);
    LogToScreen('error_comment: ' . $s->error_comment);
    LogToScreen('packetdate: ' . $s->packetdate);
    LogToScreen('signature: ' . $s->signature);
    LogToScreen('processingname: ' . $s->processingname);
    LogToScreen('paymenttransactiontype_id: ' . $s->paymenttransactiontype_id);
    LogToScreen('phone: ' . $s->phone);
    LogToScreen('idata: ' . $s->idata);
    LogToScreen('pt_code: ' . $s->pt_code);
}
LogToScreen('--------');

?>

</body>
</html>
