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

    public static function getGroupName($group_ids){
        $name = array();
        foreach ($group_ids as $group_id) {
            $result = Groups::findFirst("id= '$group_id'");
            if($result->id){
                $name[] = $result->name;
            }
        }
        return implode(',',$name);
    }
    public static function getGroupNumber($group_ids){
        $number = array();
        foreach ($group_ids as $group_id) {
            $result = Groups::findFirst("id= '$group_id'");
            if($result->id){
                $groucontact = GroupContact::find("group_id='$group_id'");
                foreach ($groucontact as $group_data) {
                    $number[]=$group_data->contacts->number;
                }
            }
        }
        return implode(',',$number);
    }

}
