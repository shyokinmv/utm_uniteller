<?php

include_once dirname(__FILE__).'/OrderStatus.php';
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Order
 *
 * @author maks
 */
if(!class_exists('Order')) {
class Order {
    public $OrderId;
    public $AccountId;
    public $Total;
    public $ClientIP;
    public $Status;
    //put your code here
    
    public function IsFinished(){
        $res = (($this->Status == OrderStatus::Auth)
            or ($this->Status == OrderStatus::Paid));
        return $res;
    }
}
}


?>
