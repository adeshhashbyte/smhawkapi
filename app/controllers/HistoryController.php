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

}

