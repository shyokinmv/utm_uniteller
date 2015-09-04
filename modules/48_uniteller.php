<?php
include_once dirname(__FILE__).'/../lib/payment_systems_config.php';
include_once dirname(__FILE__).'/../uniteller/Uniteller.php';

$MOD_TITLE='uniteller';
$MOD_GROUP='payments';
$MOD_VISIBLE=true;
$MOD_CLASS='UnitellerModule';
$MOD_LOGIN=false;
$MOD_SYSTEM=1;

if(!class_exists($MOD_CLASS)) {
class UnitellerModule extends Module {
    private $accounts;
    
    function init() {
        global $MOD_TITLE,$MOD_GROUP,$MOD_VISIBLE,$MOD_SUBMENU;
        parent::init($MOD_TITLE,$MOD_GROUP,$MOD_VISIBLE);

        // получаем список лицевых счетов абонента
        $this->accounts = array();

        $this->urfa->call(-16469);
        $this->urfa->send();
        
        // получаем количество записей
        $count = $this->urfa->get_int();
        for($i = 0; $i < $count; $i++) {
            $aid = $this->urfa->get_int(); // номер лицевого счета

            // пропускаем два неинтересных нам параметра
            $this->urfa->get_double();  // денег на счету
            $this->urfa->get_double();  // ??
            
            // добавлем в списков лицевых счетов
            $this->accounts[$aid]=$aid;
        }
        $this->urfa->finish();
    	
        if(isset($_REQUEST['Status'])) $status=$_REQUEST['Status'];
    	else                           $status='';
		
        if($status=='pay') {
            // получаем информацию о текущем пользователе
            $this->user = array();
	        $this->urfa->call(-0x4052);
	        $this->urfa->send();
	        $this->user['id'] =               $this->urfa->get_int();
	        $this->user['login'] =            $this->urfa->get_string();
	        $this->user['basic_account'] =    $this->urfa->get_int();
	        $this->user['balance'] =          roundDouble($this->urfa->get_double());
	        $this->user['credit'] =           roundDouble($this->urfa->get_double());
	        $this->user['is_blocked'] =       resolveBlockState($this->urfa->get_int());
	        $this->user['create_date'] =      getDateFromTimestamp($this->urfa->get_int());
	        $this->user['last_change_date'] = getDateFromTimestamp($this->urfa->get_int());
	        $this->user['who_create'] =       resolveUserName($this->urfa->get_int());
	        $this->user['who_change'] =       resolveUserName($this->urfa->get_int());
	        $this->user['is_juridical'] =     $this->urfa->get_int();
	        $this->user['full_name'] =        $this->urfa->get_string();
    	    
            $accountId = intval($_REQUEST["AccountId"]);

            // проверка введенного значения суммы оплаты
            $subtotal_P = $_REQUEST['OutSum'];
            $subtotal_P = trim($subtotal_P);    //убиарем лишние пробелы
            $subtotal_P = str_replace(',', '.', $subtotal_P); // заменяем запятые на точку
            $subtotal_P = floatval($subtotal_P);   // пробуем преобразовать к числу
            $subtotal_P = round($subtotal_P, 2);   // округляем до 2 знаков после запятой
            
            if (($subtotal_P != 0)
                && ($subtotal_P >= 10)
                && ($subtotal_P < 10000)) {
                $client_ip = $_SERVER["REMOTE_ADDR"];
                echo $client_ip . '<br \>' . "\n";

                $order_IDP = Uniteller::NewOrder($accountId, $subtotal_P, $client_ip);
                echo $order_IDP . '<br \>' . "\n";

                //// Для отладки разрешаем переход на страницу оплаты только с определённого IP 
                //if ((strpos($client_ip, '10.79.124.') == 0)
                //    or (strpos($client_ip, '10.78.252.') == 0)) {
                    Uniteller::GoToPaymentPage($accountId, $order_IDP, $subtotal_P);
                //}
            } else {
                // при некорректно введенной сумме платежа возвращаемся на эту же страницу
                $url_return = $_SERVER['HTTP_REFERER'];
                header('Location: ' . $url_return);
            }
            exit;
		}
    }
    
    function writeBody() {
        global $rbk_login, $rbk_lang, $rbk_curr, $rbk_encoding, $rbk_shp_item, $rbk_pass1;
    	

        if(isset($_REQUEST['Status'])) $status=$_REQUEST['Status'];
    	else                           $status='';
    	//echo $status . '<br />' . "\n";
        
        if($status=='success') {
            $out_summ=floatval($_REQUEST['OutSum']);
            $shp_item = intval($_REQUEST["Shp_item"]);
            $uniteller_crc = strtoupper($_REQUEST["SignatureValue"]);
            $my_crc = strtoupper(md5($_REQUEST['OutSum'].':'.$_REQUEST["InvId"].':'.$rbk_pass1.':Shp_item='.$shp_item));
            if ($my_crc != $rbk_crc) {
                echo langGet('uniteller_fail');
            } else {
                echo langGet('uniteller_success');
            }
        } elseif($status=='fail') {
            echo langGet('uniteller_fail');
        } else {
            //echo 'Рисуем страницу оплаты<br />' . "\n";
            $form = new HtmlForm('uniteller_payment');
            $layout = new GridLayout(3,2);
            $layout->setCaption('uniteller_payment1');

            // список лицевых счетов
            // если несколько, то предоставить выбор
            $layout->addWidget(new HtmlLabel(langGet('account')));
            $account = new HtmlComboBox('AccountId', $this->accounts);
            $layout->addWidget($account);
            // если один, то не показывать
	        
            $layout->addWidget(new HtmlLabel(langGet('amount_text')));
            //$sum = array(100 => 100, 200 => 200, 300 => 300, 400 => 400,500 => 500,1000 => 1000,1500 => 1500,2000 => 2000);
            ////$sum = array(1 => 1, 100 => 100, 200 => 200, 300 => 300, 400 => 400,500 => 500,1000 => 1000,1500 => 1500,2000 => 2000);
            //$sum = new HtmlComboBox('OutSum', $sum);
            $sum = new HtmlTextField('OutSum'); 
            $layout->addWidget($sum);

            $layout->addWidget(new HtmlLabel(''));
            $submit = new HtmlSubmit(langGet('new_payment_next'));
            $layout->addWidget($submit);
            $form->addWidget($layout);

            $form->addData('Status', 'pay');
            $form->writeHtml();

            $form = new HtmlForm('payment');
            $layout = new GridLayout(2,1);
            $layout->addWidget(new HtmlLabel(langGet('uniteller_note')));
            //$aid = $this->accounts[1];
            $layout->addWidget(new HtmlLabel('<A HREF="?module=promised_payment">'.langGet('promised_payment').'</A>'));
            $form->addWidget($layout);
            $form->writeHtml();

        }
    }

}
}
?>
