<?php

class SmsBalance extends \Phalcon\Mvc\Model
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
    public $used;

    /**
     *
     * @var integer
     */
    public $balance;

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

    public function initialize()
    {
        $this->belongsTo("user_id", "Users", "id");
    }

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'used' => 'used', 
            'balance' => 'balance', 
            'created_at' => 'created_at', 
            'updated_at' => 'updated_at'
        );
    }


}
