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
			$history = SmsHistory::find("user_id = '$user_id' ORDER BY created_at DESC");
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
						"message" =>urldecode($value->message),
						"time" =>$this->humanTiming($value->created_at),
						"count" =>$value->count,
						"billcredit" =>$value->billcredit,
						"name" =>$result,
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
        	60 => 'minute',
        	1 => 'second'
        	);
        foreach ($tokens as $unit => $text) {
        	if ($time < $unit) continue;
        	$numberOfUnits = floor($time / $unit);
        	return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s ago':' ago');
        }
    }

}

