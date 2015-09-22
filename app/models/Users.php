<?php

use Phalcon\Mvc\Model\Validator\Email as Email;

class Users extends \Phalcon\Mvc\Model
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
    public $sender_id;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $permissions;

    /**
     *
     * @var string
     */
    public $role;

    /**
     *
     * @var string
     */
    public $admin_password;

    /**
     *
     * @var string
     */
    public $activated;

    /**
     *
     * @var string
     */
    public $activation_code;

    /**
     *
     * @var string
     */
    public $activated_at;

    /**
     *
     * @var string
     */
    public $last_login;

    /**
     *
     * @var string
     */
    public $persist_code;

    /**
     *
     * @var string
     */
    public $reset_password_code;

    /**
     *
     * @var string
     */
    public $first_name;

    /**
     *
     * @var string
     */
    public $last_name;

    /**
     *
     * @var string
     */
    public $number;

    /**
     *
     * @var string
     */
    public $company;

    /**
     *
     * @var string
     */
    public $contacts_invisible_mask;

    /**
     *
     * @var string
     */
    public $admin_password_enable;

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
     *
     * @var string
     */
    public $api_token;

    /**
     *
     * @var string
     */
    public $remember_token;

    /**
     *
     * @var string
     */
    public $avatar;

     /**
     * Before create the user assign a password
     */
    public function beforeValidationOnCreate()
    {
        //The account must be confirmed via e-mail
        // $this->activated = 0;
        $this->sender_id ='SMHAWK';
        $this->contacts_invisible_mask = 0;
        $this->admin_password_enable = 0;
    }


    /**
     * Validations and business logic
     */
    public function initialize()
    {
        $this->hasOne("id", "SmsBalance", "user_id");
        $this->hasOne("id", "Profile", "user_id");
        $this->hasMany("id", "EmailConfirmations", "userId");
    }
   
    public function validation()
    {

        $this->validate(
            new Email(
                array(
                    'field'    => 'email',
                    'required' => true,
                    'message' => 'The email is already registered'
                )
            )
        );
        if ($this->validationHasFailed() == true) {
            return false;
        }
    }

    // public function afterSave()
    // {
    //     if ($this->activated == 0) {
    //         $emailConfirmation = new EmailConfirmations();
    //         $emailConfirmation->usersId = $this->id;
    //         if ($emailConfirmation->save()) {
    //            return true;
    //         }
    //     }
    // }

    /**
     * Independent Column Mapping.
     */
    public function columnMap(){
        return array(
            'id' => 'id', 
            'sender_id' => 'sender_id', 
            'email' => 'email', 
            'password' => 'password', 
            'permissions' => 'permissions', 
            'role' => 'role', 
            'admin_password' => 'admin_password', 
            'activated' => 'activated', 
            'activation_code' => 'activation_code', 
            'activated_at' => 'activated_at', 
            'last_login' => 'last_login', 
            'persist_code' => 'persist_code', 
            'reset_password_code' => 'reset_password_code', 
            'first_name' => 'first_name', 
            'last_name' => 'last_name', 
            'number' => 'number', 
            'company' => 'company', 
            'contacts_invisible_mask' => 'contacts_invisible_mask', 
            'admin_password_enable' => 'admin_password_enable', 
            'created_at' => 'created_at', 
            'updated_at' => 'updated_at', 
            'api_token' => 'api_token', 
            'remember_token' => 'remember_token', 
            'avatar' => 'avatar'
        );
    }
    public static function onsocialSignUpSuccess($user){
        $sendmail= new Mail();
        $sendmail->send(
            array(
                $user['email'] => $user['email']
                ),
            'Welcome Email',
            'success',
            array(
                'content' => 'Welcome Email',
                'message' => 'You  have successfully created a account',
                'name' =>$user['first_name'],
                'email'=>$user['email']
                )
            );
    }

}
