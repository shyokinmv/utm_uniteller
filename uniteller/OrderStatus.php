<?php

/**
 * Description of OrderStatus
 *
 * @author maks
 */
abstract class OrderStatus {

    const Created  = 0;
    const Paid     = 1;
    const Canceled = 2;
    const Failed   = 3;
    const TimeOut  = 4;

}

?>
