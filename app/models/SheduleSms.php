<?php

class SheduleSms extends \Phalcon\Mvc\Model
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
    public $sms_id;

    /**
     *
     * @var string
     */
    public $shedule_date;

    /**
     *
     * @var string
     */
    public $status;

    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'sms_id' => 'sms_id', 
            'shedule_date' => 'shedule_date', 
            'status' => 'status'
        );
    }

}
