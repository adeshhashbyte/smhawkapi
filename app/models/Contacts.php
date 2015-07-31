<?php

use Phalcon\Mvc\Model\Validator\Email as Email;

class Contacts extends \Phalcon\Mvc\Model
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
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $number;

    /**
     *
     * @var string
     */
    public $deleted;

    /**
     *
     * @var string
     */
    public $updated_at;

    /**
     *
     * @var string
     */
    public $created_at;

    /**
     * Validations and business logic
     */
    public function beforeValidationOnCreate()
    {
        //The account must be confirmed via e-mail
        $this->deleted = 0;
    }

    public function beforeCreate()
    {
        $this->created_at = date("Y-m-d H:i:s");
        $this->updated_at = date("Y-m-d H:i:s");
    }

    public function beforeUpdate()
    {
        $this->updated_at = date("Y-m-d H:i:s");
    }

    
    public function initialize()
    {
        $this->belongsTo("id", "GroupContact", "contact_id");
    }
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'name' => 'name', 
            'email' => 'email', 
            'number' => 'number', 
            'deleted' => 'deleted', 
            'updated_at' => 'updated_at', 
            'created_at' => 'created_at'
        );
    }

}
