<?php
// Uniteller
//$uniteller_visible = true;

//параметры доступа к базе дынных 
$uniteller_db_host = '127.0.0.1';
$uniteller_db_login = 'root';
$uniteller_db_pass = '';
$uniteller_db_db = 'uniteller';

$uniteller_log = '/var/log/uniteller.log';

// параметры тестового доступа
$uniteller_login = '1234';
$uniteller_pass = '1qaz2wsx3edc4rfv5tgb6yhn7ujm8ik,9ol.0p;/1qaz2wsx3edc4rfv5tgb6yhn7ujm8ik,9ol.9u89';
$uniteller_shopid = '12345678-1234';

// параметры боевого доступа
$uniteller_login = '4321';
$uniteller_pass = 'zaq1xsw2cde3vfr4bgt5nhy6mju7,ki8.lo9/;phryfcubweucnweouvnuwencuywenxyue2buh3bfeb';
$uniteller_shopid = '00009876';

// % - комиссия платежной системы, которую будем добавлять к платежу, чтобы брать её с клиента
$uniteller_commission = 0;

// идентификатор типа оплаты из справочника типов платеже
// 1 - наличные
// 2 - банковский перевод
// ...
$uniteller_paymenttype = 103;

$uniteller_test = 0; // 0 - боевой режим; 1 - тестовый режим

//$uniteller_return_result = "http://stat.ultra-telecom.ru/uniteller/result.php";
$uniteller_return = "http://stat.info-svyaz.net/";
$uniteller_return_ok = "http://stat.info-svyaz.net/uniteller/ok.php";
$uniteller_return_no = "http://stat.info-svyaz.net/uniteller/fail.php";

?>
