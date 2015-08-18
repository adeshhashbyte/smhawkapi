<?php

class EmailConfirmations extends \Phalcon\Mvc\Model
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
    public $usersId;

    /**
     *
     * @var string
     */
    public $code;

    /**
     *
     * @var integer
     */
    public $createdAt;

    /**
     *
     * @var integer
     */
    public $modifiedAt;

    /**
     *
     * @var string
     */
    public $confirmed;
    
    public function beforeValidationOnCreate()
    {
        //Timestamp the confirmaton
        $this->createdAt = time();
        //Generate a random confirmation code
        $this->code = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(24)));
        //Set status to non-confirmed
        $this->confirmed = 'N';
    }
    /**
     * Sets the timestamp before update the confirmation
     */
    public function beforeValidationOnUpdate()
    {
        //Timestamp the confirmaton
        $this->modifiedAt = time();
    }
    /**
     * Send a confirmation e-mail to the user after create the account
     */
    public function afterCreate()
    {
        $this->getDI()->getMail()->send(
            array(
                $this->user->email => $this->user->email
            ),
            "Please confirm your email",
            'confirmation',
            array(
                'confirmUrl' => '/confirm/' . $this->code . '/' . $this->user->email,
                'content' => 'You are Almost There! Just Confirm Your Email',
                'message' => 'You have successfully created a Phalcon account. To activate it, please click below to verify your email address',
                'action' => 'Confirm',
            )
        );
    }
    public function initialize(){
        $this->belongsTo('usersId', 'Users', 'id', array(
            'alias' => 'user'
            ));
    }
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'usersId' => 'usersId', 
            'code' => 'code', 
            'createdAt' => 'createdAt', 
            'modifiedAt' => 'modifiedAt', 
            'confirmed' => 'confirmed'
            );
    }

}
