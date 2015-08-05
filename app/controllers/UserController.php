<?php
use Phalcon\Http\Response;
class UserController extends \Phalcon\Mvc\Controller

{
	public function initialize() {
		$this->view->disable();
	}
	public function indexAction()
	{

	}
	public function registerAction()
	{
		$this->response->setContentType('application/json');
		$email = $this->request->getPost('email');
		$first_name = $this->request->getPost('first_name');
		$last_name = $this->request->getPost('last_name');
		$user = Users::findFirst("email = '$email'");
		if($user){
			// $user->save();
			$data ="already exist";
		}else{
			$data ="success";
		}
		$this->response->setContent(json_encode($data));
		$this->response->send();
	}
	public function contactlistAction(){
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$user_id = $this->request->getPost('user_id');
			$contact_list = Contacts::find("user_id='$user_id' AND deleted=0");
			$contact_data = array();
			foreach ($contact_list as $contact) {
				$contact_data[]=array(
					'id'=>$contact->id,
					'name'=>$contact->name,
					'number'=>$contact->number,
					'email'=>$contact->email,
					'id'=>$contact->id,
					);
			}
			$this->response->setContent(json_encode(array('contact_list'=>$contact_data)));
			$this->response->send();	
		}
	}
	public function grouplistAction(){
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$user_id = $this->request->getPost('user_id');
			$group_list = Groups::find("user_id=$user_id");
			$groups = array();
			foreach ($group_list as $group_data) {
				$groups[]=array(
					'id'=>$group_data->id,
					'name'=>$group_data->name,
					'user_id'=>$group_data->user_id,
					'count'=>count($group_data->groupcontact)
					);
			}
			$this->response->setContent(json_encode(array('group_list'=>$groups)));
			$this->response->send();	
		}
	}

	public function updateGeneralSettingAction(){
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$user_id = $this->request->getPost('user_id');
			$company = $this->request->getPost('company');
			$firstname = $this->request->getPost('first_name');
			$password = $this->request->getPost('password');
			$user = Users::findFirst("id = '$user_id'");
			$user->first_name = $firstname;
			$user->company = $company;
			if($password!=undefined){
				$user->password = $this->security->hash($password);
			}
			if($user->save()){
				$data = array(
					'status'=>'success',
					'user_id'=>$user->id,
					'first_name'=>$user->first_name,
					'last_name'=>$user->last_name,
					'sender_id'=>$user->sender_id,
					'company'=>$user->company,
					'email'=>$user->email,
					'number'=>$user->number
					);
				$data['history']=array(
					'used'=>$user->smsbalance->used,
					'balance'=>$user->smsbalance->balance,
					);
				$this->response->setContent(json_encode($data));
				$this->response->send();	
			}
		}
	}

	public function contactSettingAction(){
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$user_id = $this->request->getPost('user_id');
			$email = $this->request->getPost('email');
			$number = $this->request->getPost('number');
			$user = Users::findFirst("id = '$user_id'");
			$user->email = $email;
			$user->number = $number;
			if($user->save()){
				$data = array(
					'status'=>'success',
					'email'=>$user->email,
					'number'=>$user->number,
					);
			}
			$this->response->setContent(json_encode($data));
			$this->response->send();	
		}
	}

	public function createContactAction(){
		if ($this->request->isPost() == true) {
			try{
				$this->response->setContentType('application/json');
				$user_id = $this->request->getPost('user_id');
				$email = $this->request->getPost('email');
				if($email==undefined){
					$email=null;
				}
				$number = $this->request->getPost('number');
				$contact_name = $this->request->getPost('contact_name');
				$contact = new Contacts();
				$contact->assign(array(
					'name' => $this->request->getPost('contact_name'),
					'number' => $this->request->getPost('number', 'striptags'),
					'user_id' => $this->request->getPost('user_id'),
					'email' => $email,
					'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
					'updated_at' =>(new \DateTime())->format('Y-m-d H:i:s')
					));
				if($contact->save()){
					$data = array(
						'status'=>'success',
						'msg'=>'contact created',
						'code'=>2
						);
					$this->response->setContent(json_encode($data));
				}
			}catch(Exception $ex){
				$this->response->setContent(json_encode(array('error'=>$ex->getMessage())));	
			}
			$this->response->send();	
		}
	}

	public function deleteContactAction(){
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$user_id = $this->request->getPost('user_id');
			$contact_ids = $this->request->getPost('contact_ids');
			$contacts = Contacts::find('id IN ('.$contact_ids.')');
			foreach($contacts as $contact){
				$group_contacts = GroupContact::find('contact_id ='.$contact->id);
				if($group_contacts != false){
					foreach ($group_contacts as $group_contact) {
						$group_contact->delete();
					}
				}
				$contact->deleted =1;
				$contact->save();
			}
			$data = array(
				'status'=>'success',
				'msg'=>'contact deleted',
				'code'=>2
				);
			$this->response->setContent(json_encode($data));
			$this->response->send();	
		}
	}
}

