<?php

class SmsPackages extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var integer
     */
    public $sms_credit;

    /**
     *
     * @var integer
     */
    public $price;

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
            'name' => 'name', 
            'sms_credit' => 'sms_credit', 
            'price' => 'price', 
            'created_at' => 'created_at', 
            'updated_at' => 'updated_at'
        );
    }

}
