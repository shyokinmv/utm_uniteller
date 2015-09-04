<?php

include_once dirname(__FILE__)."/Uniteller.php";

$moduleName = 'result';

if (!isset($_REQUEST["Order_ID"])){
    LogToFile('Отсутствует номер заказа', $moduleName);
    exit;
}
$order_id = $_REQUEST["Order_ID"];
LogToFile("Результат оплаты заказа " . $order_id, $moduleName);

if (!isset($_SERVER['REMOTE_ADDR'])){
    LogToFile('Отсутствует обязательный параметр запроса', $moduleName);

    // логирование параметров запроса
    LogArray($_REQUEST, $modulename . ': request');
    LogArray($_SERVER,  $modulename . ': server');

    exit;
}
$remoteAddr = $_SERVER['REMOTE_ADDR'];
LogToFile('Уведомление пришло с адреса ' . $remoteAddr, $moduleName);


// проверка наличия параметра подписи
if (!isset($_REQUEST['Signature'])) {
    LogToFile('Отсутствует подпись', $moduleName);
    exit;
}
$signature = $_REQUEST['Signature'];

// проверка наличия параметра Статуса операции
if (!isset($_REQUEST['Status'])) {
    LogToFile('Отсутствует статус оплаты', $moduleName);
    exit;
}
$status0 = $_REQUEST['Status'];
LogToFile('Статус оплаты заказа ' . $status0, $moduleName);

// $status0 может быть
// authorized - средства успешно заблокированы (выполнена авторизационная
//   транзакция)
// not authorized - средства не заблокированы (авторизационная транзакция не
//   выполнена) по ряду причин
// paid - оплачен (выполнена финансовая транзакция или заказ оплачен в
//   электронной платёжной системе)
// canceled - отменён (выполнена транзакция разблокировки средств или выполнена
//   операция по возврату платежа после списания средств)
// waiting - ожидается оплата выставленного счёта. Статус используется только
//   для оплат электронными валютами, при которых процесс оплаты может содержать
//   этап выставления черех систему Uniteller счёта на оплату и этап фактической
//   оплаты этого счета покупателем, которые существенно разнесены по времени

// проверка достоверности подписи
if (!Uniteller::IsSignatureCorrect($order_id, $status0, $signature)){
    LogToFile('Подпись неверна', $moduleName);
    exit;
}

Uniteller::OrderPaymentCallback($order_id);

exit;

?>
