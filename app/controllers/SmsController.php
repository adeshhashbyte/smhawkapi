<?php

class SmsController extends \Phalcon\Mvc\Controller
{
	public function initialize() {
		$this->view->disable();
	}

	public function indexAction()
	{

	}

	public function sendSmsContactsAction(){
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$contact_ids = $this->request->getPost('contact_ids');
			$message = $this->request->getPost('message');
			$user_id = $this->request->getPost('user_id');
			$contacts_ids = explode(',', $contact_ids);
			$contacts = Contacts::find('id IN ('.$contact_ids.')');
			$numbers = array();
			$contact_id = array();
			foreach($contacts as $contact){
				$numbers[] =$contact->number;
				$contact_id[]=$contact->id;
			}
			$data = $this->sendSmsProcessData(array(
				'message' => $message,
				'user_id' => $user_id,
				'ids'=> $contact_id,
				'type'=>"CONTACTID",
				'contacts'=>$numbers
				));
			$this->response->setContent(json_encode($data));
			$this->response->send();
		}
	}

	public function sendGroupSmsAction(){
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$group_id = $this->request->getPost('group_id');
			$message = $this->request->getPost('message');
			$user_id = $this->request->getPost('user_id');
			$sms_length = strlen($message);
			$sms_credit = ($sms_length - $sms_length % 160) / 160 + 1;
			$user = Users::findFirst("id = '$user_id'");
			$groucontact = GroupContact::find("group_id=$group_id");
			$groucontactlist = array();
			$groupid = array();
			foreach ($groucontact as $group_data) {
				$groucontactlist['number'][]=$group_data->contacts->number;
			}
			$data = $this->sendSmsProcessData(array(
				'message' => $message,
				'user_id' => $user_id,
				'ids'=> explode(',',$group_id),
				'type'=>"GROUPID",
				'contacts'=>$groucontactlist['number']
				));
			$this->response->setContent(json_encode($data));
			$this->response->send();
		}
	}

	public function quicksmsAction(){
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$contact_number = $this->request->getPost('contact_number');
			$contact_number = explode(',', $contact_number);
			$message = $this->request->getPost('message');
			$user_id = $this->request->getPost('user_id');
			$data = $this->sendSmsProcessData(array(
				'message' => $message,
				'user_id' => $user_id,
				'ids'=> $contact_number,
				'type'=>"NUMBER",
				'contacts'=>$contact_number
				));
			$this->response->setContent(json_encode($data));
			$this->response->send();
		}
	}

	private function sendSmsProcessData($smsdata) {
		$sms_length = strlen($smsdata['message']);
		$sms_credit = ($sms_length - $sms_length % 160) / 160 + 1;
		$user_id = $smsdata['user_id'];
		$user = Users::findFirst("id = '$user_id'");
		$count = count($smsdata['contacts']);
		$billcredit_sms = $count * $sms_credit;
		if (empty($user->sender_id)) {
			$sender_id = 'SMHAWK';
		}else{
			$sender_id = $user->sender_id;
		}
		$group_id = 0;
		if($smsdata['type']=="GROUPID"){
			$group_id = implode(',', $smsdata['ids']);
		}
		if ($user->smsbalance->balance >= $billcredit_sms) {
			$request_info = $this->sendSMSRequest(array(
				'contact_list' => $smsdata['contacts'],
				'sms' => $smsdata['message'],
				'sender_id'=> $sender_id
				));
			$sms_history = new SmsHistory();
			$sms_history->assign(array(
				'user_id' => $user_id,
				'group_id'=> $group_id,
				'reciever' => json_encode($smsdata['ids']),
				'contact_ids' => json_encode($smsdata['ids']),
				'message' => urlencode($smsdata['message']),
				'billcredit' => $billcredit_sms,
				'count' => $count,
				'type' =>$smsdata['type'],
				'status' => "SUCCESS",
				'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
				'updated_at' =>(new \DateTime())->format('Y-m-d H:i:s')
				));
			$sms_history->save();
			$user->smsbalance->balance = $user->smsbalance->balance - $billcredit_sms;
			$user->smsbalance->used = $user->smsbalance->used + $billcredit_sms;
			$user->smsbalance->save();
			$data = array();
			$data = array(
				'status'=>'success',
				'id'=>$sms_history->id,
				'code'=>2,
				'ids' => json_decode($sms_history->reciever),
				'type' => $sms_history->type
				);
			$data['history']=array(
				'used'=>$user->smsbalance->used,
				'balance'=>$user->smsbalance->balance,
				);
		}else{
			$data = array(
				'status'=>'error',
				'code'=>1,
				'message'=>'Insufficient Balance'
				);
		}
		return $data;
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
		foreach ($data as $send_sms_data) {
			$response = $sendmsgservice->sendSMS($send_sms_data);
		}
		$return = array('do_send_sms' => $do_send_sms);
		return $return;
	}

	public function sendSmsBytypeAction() {
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$ids = explode(',', $this->request->getPost("ids"));
			$user_id = $this->request->getPost('user_id');
			$message = $this->request->getPost('message');
			$type = $this->request->getPost('type');
			switch($type){	
				case 'GROUPID':
				$contacts =GroupContact::getGroupContacts($ids);
				break;
				case 'NUMBER':
				$contacts = $ids;
				break;
				case 'CONTACTID':
				$contacts =Contacts::getContactNumber($ids);
				break;
			}
			$data = $this->sendSmsProcessData(array(
				'message' => $message,
				'user_id' => $user_id,
				'ids'=> $ids,
				'type'=>$type,
				'contacts'=>$contacts
				));
			$this->response->setContent(json_encode($data));
			$this->response->send();
		}
	}

}

