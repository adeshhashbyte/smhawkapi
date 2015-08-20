<?php

class TransactionHistory extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $user_id;

    /**
     *
     * @var integer
     */
    public $amount;

    /**
     *
     * @var integer
     */
    public $sms_credit;

    /**
     *
     * @var integer
     */
    public $new_sms_balance;

    /**
     *
     * @var string
     */
    public $status;

    /**
     *
     * @var string
     */
    public $txnid;

    /**
     *
     * @var string
     */
    public $gateway_txnid;

    /**
     *
     * @var string
     */
    public $created_at;

    /**
     *
     * @var string
     */
    public $updated_at;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'amount' => 'amount', 
            'sms_credit' => 'sms_credit', 
            'new_sms_balance' => 'new_sms_balance', 
            'status' => 'status', 
            'txnid' => 'txnid', 
            'gateway_txnid' => 'gateway_txnid', 
            'created_at' => 'created_at', 
            'updated_at' => 'updated_at'
        );
    }

}
