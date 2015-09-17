<?php
use Phalcon\Mvc\Model\Behavior\Timestampable;
use Phalcon\Mvc\Model\Resultset\Simple as Resultset;
class SmsHistory extends \Phalcon\Mvc\Model
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
    public $group_id;

    /**
     *
     * @var string
     */
    public $message;

    /**
     *
     * @var string
     */
    public $contact_ids;

     /**
     *
     * @var string
     */
    public $reciever;

    /**
     *
     * @var string
     */
    public $type;

    /**
     *
     * @var string
     */
    public $status;

    /**
     *
     * @var integer
     */
    public $count;

    /**
     *
     * @var integer
     */
    public $billcredit;

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

    // public function beforeValidationOnCreate()
    // {
    //     $this->group_id = 0;
    // }

    public function beforeCreate()
    {
        $this->created_at = date("Y-m-d H:i:s");
        $this->updated_at = date("Y-m-d H:i:s");
    }

    public function beforeUpdate()
    {
        $this->updated_at = date("Y-m-d H:i:s");
    }

    public static function getData($user_id){
        $sql = "SELECT p1.*,p2.counts FROM sms_history p1 INNER JOIN (SELECT max(id) MaxPostDate, reciever,COUNT(*) counts FROM sms_history GROUP BY reciever) p2  ON p1.reciever = p2.reciever AND p1.id = p2.MaxPostDate WHERE user_id = $user_id AND status <> 'PENDING'  order by p1.id desc";
        $smshistory = new SmsHistory();
        return new Resultset(null, $smshistory, $smshistory->getReadConnection()->query($sql));
    }
    
    /**
     * Independent Column Mapping.
     */
    public function columnMap()
    {
        return array(
            'id' => 'id', 
            'user_id' => 'user_id', 
            'group_id' => 'group_id', 
            'message' => 'message', 
            'contact_ids' => 'contact_ids', 
            'reciever' => 'reciever', 
            'type' => 'type', 
            'status' => 'status', 
            'count' => 'count', 
            'billcredit' => 'billcredit', 
            'created_at' => 'created_at', 
            'updated_at' => 'updated_at'
        );
    }

}
