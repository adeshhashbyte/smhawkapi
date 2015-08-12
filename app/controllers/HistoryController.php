<?php
use Phalcon\Http\Response;
class HistoryController extends \Phalcon\Mvc\Controller
{
	public function initialize() {
		$this->view->disable();
	}
	public function indexAction()
	{

	}

	public function userhistoryAction() {
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$user_id = $this->request->getPost("user_id");
			$history = SmsHistory::find("user_id = '$user_id'");
			$user_history = array();
			foreach ($history as $value) {
				$user_history[] = array(
					"message" =>urldecode($value->message)
					);
			}
			$this->response->setContent(json_encode(array('user_history'=>$user_history)));
			$this->response->send();
		}
	}

	public function userHistoryByDateAction(){ 
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$user_id = $this->request->getPost("user_id");
			$history = SmsHistory::find(array(
				'conditions' => "user_id = '$user_id' GROUP BY reciever ORDER BY updated_at DESC",
				'columns' => 'id, message, reciever,type,status,billcredit,created_at,updated_at,COUNT(*) counts'
				));
			//$history = SmsHistory::find("user_id = '$user_id' GROUP BY reciever ORDER BY created_at DESC");
			$user_history = array();
			foreach ($history as $value) {
				switch($value->type)
				{	
					case 'GROUPID':
					$result =Groups::getGroupName(json_decode($value->reciever));
					break;
					case 'NUMBER':
					$result = implode(',',json_decode($value->reciever));
					break;
					case 'CONTACTID':
					$result =Contacts::getContactName(json_decode($value->reciever));
					break;
				}
				if($result !=''){
					$user_history[] = array(
						"id"=>$value->id,
						"message" =>urldecode($value->message),
						"time" =>$this->humanTiming($value->updated_at),
						"count" =>$value->counts,
						"billcredit" =>$value->billcredit,
						"name" =>$result,
						"type" => $value->type,
						"ids" => json_decode($value->reciever),
						);
				}
			}
			$this->response->setContent(json_encode(array('user_history'=>$user_history)));
			$this->response->send();
		}
	}

	public function historyByGroupAction(){ 
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$user_id = $this->request->getPost("user_id");
			$type = $this->request->getPost("type");
			$ids = explode(',', $this->request->getPost("ids"));
			$history = SmsHistory::find(array(
				'conditions' => "user_id = '$user_id' AND type = '$type' ORDER BY updated_at DESC",
				'columns' => 'id, message, reciever,type,status,count,billcredit,created_at,updated_at',
				));
			$user_history = array();
			foreach ($history as $value) {
				switch($value->type)
				{	
					case 'GROUPID':
					$result =Groups::getGroupName(json_decode($value->reciever));
					break;
					case 'NUMBER':
					$result = implode(',',json_decode($value->reciever));
					break;
					case 'CONTACTID':
					$result =Contacts::getContactName(json_decode($value->reciever));
					break;
				}
				if($ids ==json_decode($value->reciever)){
					$user_history[] = array(
						"id"=>$value->id,
						"message" =>urldecode($value->message),
						// "time" =>$this->humanTiming($value->updated_at),
						"count" =>$value->count,
						"billcredit" =>$value->billcredit,
						"name" =>$result,
						$value->type => json_decode($value->reciever),
						'date' => date('M d,Y, H:i A',strtotime($value->updated_at)),
						);
				}
			}
			$this->response->setContent(json_encode(array('user_history'=>$user_history)));
			$this->response->send();
		}
	}

	private function humanTiming($created_at)
	{
		$time = strtotime($created_at);
        $time = time() - $time; // to get the time since that moment
        if($time < 5){
        	return 'Just now';
        }
        $tokens = array (
        	31536000 => 'year',
        	2592000 => 'month',
        	604800 => 'week',
        	86400 => 'day',
        	3600 => 'hour',
        	60 => 'min',
        	1 => 'sec'
        	);
        foreach ($tokens as $unit => $text) {
        	if ($time < $unit) continue;
        	$numberOfUnits = floor($time / $unit);
        	return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s ago':' ago');
        }
    }

}

