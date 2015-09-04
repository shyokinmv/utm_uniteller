<?php

//include dirname(__FILE__) . "/config.php";
include_once dirname(__FILE__).'/../lib/payment_systems_config.php';
include_once dirname(__FILE__).'/Order.php';

global $uniteller_test;

// задаем адреса обращения к системе uniteller
$uniteller_address = "https://wpay.uniteller.ru/pay/";
//$uniteller_result = "https://wpay.uniteller.ru/results/";
$uniteller_wsdl = "https://wpay.uniteller.ru/results/wsdl/";
//$uniteller_lk = "https://lk.uniteller.ru/";
if ($uniteller_test == 1) {
    // если система в тестовом доступе, то меняем на соответствующие адреса
    $uniteller_address = "https://test.wpay.uniteller.ru/pay/";
    //$uniteller_result = "https://test.wpay.uniteller.ru/results/";
    $uniteller_wsdl = "https://test.wpay.uniteller.ru/results/wsdl/";
    //$uniteller_lk = "https://test.lk.uniteller.ru/";
}

function LogToScreen($msg){
    echo $msg . '<br />' . "\n";
}

function GetPrefix($moduleName){
    $prefix='';
    if (isset($moduleName)) {
        if ($moduleName != '') {
            $prefix = $moduleName . ': ';
        }
    }
    return $prefix;
}

function LogToFile($msg, $moduleName = ''){
    LogToScreen($msg);

    global $uniteller_log;

    $prefix = GetPrefix($moduleName);
    
    $content = date("Ymd His", time()) . ': ' . $prefix . $msg . "\n";

    // Вначале давайте убедимся, что файл существует и доступен для записи.
    if (is_writable($uniteller_log)) {

        // В нашем примере мы открываем $filename в режиме "записи в конец".
        // Таким образом, смещение установлено в конец файла и
        // наш $somecontent допишется в конец при использовании fwrite().
        if (!$handle = fopen($uniteller_log, 'a+')) {
            LogToScreen($prefix . "Не могу открыть файл ($uniteller_log)");
            exit;
        }

        // Записываем $somecontent в наш открытый файл.
        if (fwrite($handle, $content) === FALSE) {
            LogToScreen($prefix . "Не могу произвести запись в файл ($uniteller_log)");
            exit;
        }

        //LogToScreen("Ура! Записали ($somecontent) в файл ($filename)");

        fclose($handle);

    } else {
        LogToScreen($prefix . "Файл $uniteller_log недоступен для записи");
    }
}

function LogArray($array, $moduleName){
    $prefix = GetPrefix($moduleName);
    foreach ($array as $k=>$v) {
        LogToFile($prefix . $k . ' -> ' . $v);
    }
}

function LogOrder(Order $o){
    LogToFile('Order:');
    LogToFile('AccountId: ' . $o->AccountId);
    LogToFile('ClienIP: '   . $o->ClientIP);
    LogToFile('OrderId: '   . $o->OrderId);
    LogToFile('Status: '    . $o->Status);
    LogToFile('Total: '     . $o->Total);
}

function LogStatus($s){
    LogToFile('Status:');
    LogToFile('ordernumber: '    . $s->ordernumber);
    LogToFile('response_code: '  . $s->response_code);
    LogToFile('recommendation: ' . $s->recommendation);
    LogToFile('message: '        . $s->message);
    LogToFile('comment: '        . $s->comment);
    LogToFile('date: '           . $s->date);
    LogToFile('total: '          . $s->total);
    LogToFile('currency: '       . $s->currency);
    LogToFile('cardtype: '       . $s->cardtype);
    LogToFile('cardnumber: '     . $s->cardnumber);
    LogToFile('lastname: '       . $s->lastname);
    LogToFile('firstname: '      . $s->firstname);
    LogToFile('middlename: '     . $s->middlename);
    LogToFile('address: '        . $s->address);
    LogToFile('email: '          . $s->email);
    //LogToFile('country: '        . $s->country);
    LogToFile('rate: '           . $s->rate);
    LogToFile('approvalcode: '   . $s->approvalcode);
    LogToFile('cardsubtype: '    . $s->cardsubtype);
    LogToFile('cvc2: '           . $s->cvc2);
    LogToFile('cardholder: '     . $s->cardholder);
    LogToFile('ipaddress: '      . $s->ipaddress);
    LogToFile('protocoltypename: ' . $s->protocoltypename);
    LogToFile('billnumber: '     . $s->billnumber);
    LogToFile('bankname: '       . $s->bankname);
    LogToFile('status: '         . $s->status);
    LogToFile('error_code: '     . $s->error_code);
    LogToFile('error_comment: '  . $s->error_comment);
    LogToFile('packetdate: '     . $s->packetdate);
    //LogToFile('signature: '      . $s->signature);
    LogToFile('processingname: ' . $s->processingname);
    //LogToFile('paymenttransactiontype_id: ' . $s->paymenttransactiontype_id);
    LogToFile('phone: '          . $s->phone);
    LogToFile('idata: '          . $s->idata);
    LogToFile('pt_code: '        . $s->pt_code);
}
    
function LogCurrState(Order $o, $status){
    LogOrder($o);
    // логируем полученные данные о платеже
    foreach ($status as $s){
        LogToFile('----');
        LogStatus($s);
    }
    LogToFile('--------');
}

if(!class_exists('Uniteller')) {
class Uniteller {

    private static function GetPaySignature($shop_IDP, $order_IDP, $subtotal_P, $customer_IDP, $password) {
        $signature = md5($shop_IDP)
                . '&' . md5($order_IDP)
                . '&' . md5($subtotal_P)
                . '&' . md5('') //(MeanType)
                . '&' . md5('') //(EMoneyType)
                . '&' . md5('') //(Lifetime)
                . '&' . md5($customer_IDP)
                . '&' . md5('') //(Card_IDP)
                . '&' . md5('') //(IData)
                . '&' . md5('') //(PT_Code)
                . '&' . md5($password);

        $signature = strtoupper(md5($signature));

        return $signature;
    }
    
    public static function IsSignatureCorrect($orderId, $status, $signature) {
        global $uniteller_pass;
        $signature1 = strtoupper(md5($orderId . $status . $uniteller_pass));
        $res = ($signature == $signature1);
        
        $moduleName = 'uniteller';
        //LogToFile('OrderId: ' . $orderId, $moduleName);
        //LogToFile('Status: ' . $status, $moduleName);
        //LogToFile('Pass: ' . $uniteller_pass, $moduleName);
        //LogToFile('Signature:  ' . $signature, $moduleName);
        //LogToFile('Signature1: ' . $signature1, $moduleName);
        //LogToFile('Result: ' . $res, $moduleName);
        
        return $res;
    }

    private static function GetConnection() {
        global $uniteller_db_host, $uniteller_db_login, $uniteller_db_pass, $uniteller_db_db;

        $m = new mysqli($uniteller_db_host, $uniteller_db_login, $uniteller_db_pass, $uniteller_db_db);

        if ($m->connect_errno) {
            echo('Не удалось соединиться: ' . $m->connect_error);
            exit();
        }
        
        return $m;
    }
    
    public static function NewOrder($account_id, $total, $client_ip, $status = OrderStatus::Created) {
        $msg = $account_id . ' ' . $total . ' ' . $client_ip;
        $moduleName = 'Uniteller';
        LogToFile($msg, $moduleName);
        
        $m = self::GetConnection();

        $query = 'insert into Orders(account_id, total, started, client_ip, status) values(?, ?, now(), inet_aton(?), ?)';
        $q = $m->prepare($query);

        $q->bind_param('idsi', $account_id, $total, $client_ip, $status);
        $q->execute();

        $id = $q->insert_id;

        $m->close();

        return $id;
    }
    
    public static function GetOrderById($orderId){
        $m = self::GetConnection();

        $query = 'select order_id, account_id, total, status, inet_ntoa(client_ip) from Orders where order_id=?';
        $q = $m->prepare($query);
        $q->bind_param('i', $orderId);
        $q->execute();
        $q->bind_result($order_id, $accountId, $total, $status, $client_ip);

        $orders = array();
        while ($q->fetch()) {
            $o = New Order();
            $o->OrderId   = $orderId;
            $o->AccountId = $accountId;
            $o->Total     = $total;
            $o->Status    = $status;
            $o->ClientIP  = $client_ip;
            $orders[] = $o;
        }

        $m->close();
        
        return $orders[0];
    }
    
    static function GetOrdersInProcess(){
        $m = self::GetConnection();

        $query = 'select order_id from Orders Order by Started desc Limit 0, 10';
        $q = $m->prepare($query);
        $q->execute();
        $q->bind_result($order_id);

        $orders = array();
        while ($q->fetch()) {
            $orders[] = $order_id;
        }

        $m->close();
        
        return $orders;
    }

    static function SetOrderStatus($order_id, $status) {
        $m = self::GetConnection();

        $query = 'update Orders set status=? where order_id = ?';
        $q = $m->prepare($query);

        $q->bind_param('ii', $status, $order_id);
        $q->execute();

        $m->close();
    }

    static function FinishOrder($order_id, $status) {
        $m = self::GetConnection();

        $query = 'update Orders set finished = now(), status = ? where order_id = ?';
        $q = $m->prepare($query);

        $q->bind_param('ii', $status, $order_id);
        $q->execute();

        $m->close();
    }

    static function GetOrderStatus($order_id) {
        $status = self::GetPaymentResult($order_id);
        return $status;
    }

    static function GetUnfinishedOrders() {
        $m = self::GetConnection();

        $query = 'select order_id, total, started, status from Orders where finished is null';
        $q = $m->prepare($query);
        $q->execute();

        $q->bind_result($order_id, $total, $started, $status);

        while ($q->fetch()) {
            $moduleName = 'Uniteller';
            LogToFile($order_id . ', ' . $total . ', ' . $started . ', ' . $status, $moduleName);
        }
        
        $m->close();
    }

    static function GetPaymentResult($order_id) {
        global $uniteller_shopid, $uniteller_login, $uniteller_pass, $uniteller_wsdl;
        try {
            ini_set('soap.wsdl_cache_enabled', '0');
            ini_set('soap.wsdl_cache_ttl', '0');
            //$wsdl_url = 'https://test.wpay.uniteller.ru/results/wsdl/';
            $wsdl_url = $uniteller_wsdl;
            $client = new SOAPClient($wsdl_url);

            //$msg = $uniteller_shopid . ' ' . $uniteller_login . ' ' . $uniteller_pass;
            //$moduleName = 'Uniteller';
            //LogToFile($msg, $moduleName);

            $result = $client->GetPaymentsResult(
                $uniteller_shopid,
                $uniteller_login,
                $uniteller_pass,
                $order_id,
                $success = 1,
                $startmin = null,
                $starthour = null,
                $startday = null,
                $startmonth = null,
                $startyear = null,
                $endmin = null,
                $endhour = null,
                $endday = null,
                $endmonth = null,
                $endyear = null,
                $meantype = null,
                $paymentype = null,
                $english = null
            );
            return $result;
        } catch (Exception $e) {
            $moduleName = 'Uniteller';
            LogToFile("Exception occured: " . $e, $moduleName);
        }
    }

    static function GoToPaymentPage($customer_id, $order_id, $subtotal_p){
        global $uniteller_address, $uniteller_shopid, $uniteller_pass, $uniteller_return, $uniteller_return_ok, $uniteller_return_no, $uniteller_commission;


        $subtotal_recalc = $subtotal_p;

        if ($uniteller_commission > 0) {
            $subtotal_recalc = round($subtotal_p * 100 / (100 - $uniteller_commission), 2);
        }

        $url = $uniteller_address;
        $url = $url . '?Shop_IDP=' . $uniteller_shopid;

        $url = $url . '&Customer_IDP=' . $customer_id;
        $url = $url . '&Order_IDP=' . $order_id;
        $url = $url . '&Subtotal_P=' . $subtotal_recalc;

        $signature = self::GetPaySignature($uniteller_shopid, $order_id, $subtotal_recalc, $customer_id, $uniteller_pass);

        $url = $url . '&Signature=' . $signature;

        //$moduleName = 'Uniteller';
        //LogArray($_SERVER, $moduleName);
        
        // адрес страницы модуля оплаты
        $uniteller_return = $_SERVER['HTTP_REFERER'];
        
        // адрес главной страницы личного кабинета
        //$uniteller_return = $_SERVER['HTTP_REFERER'];

        $url = $url . '&URL_RETURN=' . $uniteller_return;
        //$url = $url . '&URL_RETURN_OK='.$uniteller_return_ok;
        //$url = $url . '&URL_RETURN_NO='.$uniteller_return_no;

        header('Location: ' . $url);
    }
    
    static function UTM5Pay($account_id, $total, $billnumber, $paymenttype, $comment = ''){
        $cmd = '/netup/utm5/bin/utm5_payment_tool';

        // номер лицевого счета
        $cmd = $cmd . ' -a ' . $account_id;

        // сумма платежа
        $cmd = $cmd . ' -b ' . $total;

        // номер платежного документа
        $cmd = $cmd . ' -e ' . 'uniteller_' . $billnumber;

        // тип платежа Uniteller
        $cmd = $cmd . ' -m ' . $paymenttype;

        // комментарий для администратора
        if ($comment != '') {
            $cmd = $cmd . ' -L ' . "\'" . $comment . "\'";
        }

        LogToFile($cmd, 'result');
        exec($cmd);
    }
    
    static function OrderPaymentCallback($order_id) {
        global $uniteller_paymenttype;
        $moduleName = 'result';
        
        // запрос имеющихся данных по заказу из своей базы
        $o = self::GetOrderById($order_id);

        // запрос к Uniteller о состоянии платежа
        $status = self::GetPaymentResult($order_id);
        $s = $status[0];

        // логируем текущее состояние и оплату
        LogCurrState($o, $status);
        
        // проверка данных о завершенни заказа,
        // для предотвращения повторной обработки
        if ($s->response_code == 'AS000') {
            switch ($o->Status) {
                case OrderStatus::Created:
                    
                    switch ($s->status) {
                        case 'Authorized':
                            // зачисляем оплату
                            self::UTM5Pay($o->AccountId, $o->Total, $s->billnumber, $uniteller_paymenttype);
                            self::FinishOrder($o->OrderId, OrderStatus::Paid);

                            break;

                        case 'Paid':
                            // зачисляем оплату
                            self::UTM5Pay($o->AccountId, $o->Total, $s->billnumber, $uniteller_paymenttype);
                            self::FinishOrder($o->OrderId, OrderStatus::Paid);

                            break;

                        case 'Canceled':
                            // отмечаем как ошибочный
                            self::FinishOrder($o->OrderId, OrderStatus::Failed);
                            break;

                        default:
                            // логируем текущее состояние заказа и полученные данные для последующего разбора
                            LogToFile('Неопределенное состояние 1');
                            break;
                    }
                    break;

                case OrderStatus::Paid:

                    switch ($s->status) {
                        case 'Canceled':
                            // отменяем оплату
                            self::UTM5Pay($o->AccountId, -$o->Total, $s->billnumber, $uniteller_paymenttype, 'Отмена платежа');
                            self::FinishOrder($o->OrderId, OrderStatus::Canceled);
                            break;

                        default:
                            // логируем текущее состояние заказа и полученные данные для последующего разбора
                            LogToFile('Неопределенное состояние 2');
                            break;
                    }   // switch ($s->status)

                    break;

                default:
                    // логируем текущее состояние заказа и полученные данные для последующего разбора
                    LogToFile('Неопределенное состояние 3');
                    break;
            }   // switch
            
        } else {    // if ($s->response_code == 'AS000') {
            // логируем текущее состояние заказа и полученные данные для последующего разбора
            LogToFile('Неопределенное состояние 4');
        }   // if ($s->response_code == 'AS000') {
    }

}
}

?>
