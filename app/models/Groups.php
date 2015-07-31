<?php

class Groups extends \Phalcon\Mvc\Model
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
    public $user_id;

    /**
     *
     * @var string
     */
    public $permissions;

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
        $this->hasMany("id", "GroupContact", "group_id");
    }

    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'name' => 'name', 
            'user_id' => 'user_id', 
            'permissions' => 'permissions', 
            'created_at' => 'created_at', 
            'updated_at' => 'updated_at'
        );
    }

}
