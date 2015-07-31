<?php

class SmsController extends \Phalcon\Mvc\Controller
{
	public function initialize() {
		$this->view->disable();
	}

	public function indexAction()
	{

	}

	public function quicksmsAction(){
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$contact_number = $this->request->getPost('contact_ids');
			$contact_number = explode(',', $contact_number);
			// print_r($contact_ids);die;
			$message = $this->request->getPost('message');
			$sms_length = strlen($message);
			$sms_credit = ($sms_length - $sms_length % 160) / 160 + 1;
			$user_id = $this->request->getPost('user_id');
			$user = Users::findFirst("id = '$user_id'");
			//$contacts = Contacts::find('id IN ('.$contact_ids.')');
			$count = count($contact_number);
			$billcredit_sms = $count * $sms_credit;
			$numbers = array();
			// $contact_id = array();
			// foreach($contacts as $contact){
			// 	$numbers[] =$contact->number;
			// 	$contact_id[]=$contact->id;
			// }
			if (empty($user->sender_id)) {
				$sender_id = 'SMHAWK';
			}else{
				$sender_id = $user->sender_id;
			}
			if ($user->smsbalance->balance >= $billcredit_sms) {
				$request_info = $this->sendSMSRequest(array(
					'contact_list' => $contact_number,
					'sms' => $message,
					'sender_id'=> $sender_id
					));
				$sms_history = new SmsHistory();
				$sms_history->assign(array(
					'user_id' => $user_id,
					'reciever' => json_encode($contact_number),
					'message' => urlencode($message),
					'billcredit' => $billcredit_sms,
					'count' => $count,
					'type' =>"NUMBER",
					'status' => "SUCCESS",
					'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
					'updated_at' =>(new \DateTime())->format('Y-m-d H:i:s')
					));
				$sms_history->save();
				$user->smsbalance->balance = $user->smsbalance->balance - $billcredit_sms;
				$user->smsbalance->used = $user->smsbalance->used + $billcredit_sms;
				$user->smsbalance->save();
			}
			else{

			}
			$this->response->setContent(json_encode('success'));
			$this->response->send();
		}
	}

	public function groupsmsAction(){

	}

	private function sendSMSRequest($sms_data) {
		$do_send_sms = 1;
		$sendmsgservice = new SMSGateway();
		foreach (array_chunk($sms_data['contact_list'], 50) as $value) {
			$number_list = implode(' ', $value);
			$data[] = array(
				'to' => $number_list,
				'message' => $sms_data['sms'],
				'sender_id' => $sms_data['sender_id'],
				);
		}
		// foreach ($data as $send_sms_data) {
		// 	$response = $sendmsgservice->sendSMS($send_sms_data);
		// }
		$return = array('do_send_sms' => $do_send_sms);
		return $return;
	}

}

