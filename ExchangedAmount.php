<?php

class ExchangedAmount
{
    private $from;
    private $to;
    private $amount;
    function __construct($from, $to, $amount)
    {
        $this->from = $from;
        $this->to = $to;
        $this->amount = $amount;
    }

    public function toDecimal(){

    }
}