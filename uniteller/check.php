<?php
include_once dirname(__FILE__)."/uniteller.php";

//// получить список незавершенных заказов
//$orders = Uniteller::GetOrdersInProcess();
//
//foreach ($orders as $o){
//    //проверить статус заказов
//    $status = 0; //Uniteller::GetPaymentResult($o);
//    LogToScreen($o . ' ' . $status . ' ' . count($status));
//}

//завершить оплаченные заказы
LogToScreen(md5(''));

libxml_disable_entity_loader(false);

$o = 538;
$status = Uniteller::GetPaymentResult($o);
LogToScreen($o . ' ' . $status . ' ' . count($status));
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
LogToScreen('----------');


//$o = 539;
//$status = Uniteller::GetPaymentResult($o);
//LogToScreen($o . ' ' . $status . ' ' . count($status));
//var_dump($status);
//foreach ($status as $s){
//    LogToScreen($s);
//    var_dump($s);
//}

?>
