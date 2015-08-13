<?php

class GroupContact extends \Phalcon\Mvc\Model
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
    public $group_id;

    /**
     *
     * @var integer
     */
    public $contact_id;

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
    
    public function beforeCreate()
    {
        $this->created_at = date("Y-m-d H:i:s");
        $this->updated_at = date("Y-m-d H:i:s");
    }

    public function beforeUpdate()
    {
        $this->updated_at = date("Y-m-d H:i:s");
    }

    /**
     * Independent Column Mapping.
     */
    public function initialize()
    {
        $this->belongsTo("group_id", "Group", "id");
        $this->hasOne("contact_id", "Contacts", "id");
    }
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'group_id' => 'group_id', 
            'contact_id' => 'contact_id', 
            'created_at' => 'created_at', 
            'updated_at' => 'updated_at'
            );
    }

    public static function getGroupContacts($group_ids){
        foreach ($group_ids as $group_id) {
            $groucontact = GroupContact::find("group_id=$group_id");
            $groucontactlist = array();
            foreach ($groucontact as $group_data) {
                $groucontactlist[]=$group_data->contacts->number;
            }
        }
        return $groucontactlist;

    }
}
