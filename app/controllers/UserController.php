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

	public function passwordResetViaEmailAction()
	{
		$this->response->setContentType('application/json');
		$email = $this->request->getPost('email');
		$user = Users::findFirstByEmail($email);
		if (!$user) {
			$data = array(
				'code'=>1,
				'status'=>'error',
				'msg'=>'Email Not Found',
				);
		}else{
			$this->getDI()->getMail()->send(
				array(
					$user->email => $user->email
					),
				"Reset Your Password",
				'confirmation',
				array(
					'confirmUrl' => '/change-password/'.$user->email,
					'content' => 'You are Almost There! Just Reset Your Password',
					'message' => 'please click below to reset your password',
					'action' => 'Reset Password',
					)
				);
			$data = array(
				'code'=>2,
				'status'=>'success',
				'msg'=>'Mail has been sent to your email please reset your password',
				);
		}
		$this->response->setContent(json_encode($data));
		$this->response->send();
	}


	public function contactListAction(){
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$user_id = $this->request->getPost('user_id');
			$contact_list = Contacts::find("user_id='$user_id' AND deleted=0");
			$contact_data = array();
			foreach ($contact_list as $contact) {
				$contact_data[]=array(
					'id'=> $contact->id,
					'name'=> $contact->name,
					'number'=> $contact->number,
					'email'=> $contact->email,
					'address'=> $contact->address,
					'id'=> $contact->id
					);
			}
			$this->response->setContent(json_encode(array('contact_list' => $contact_data)));
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
			$last_name = $this->request->getPost('last_name');
			$password = $this->request->getPost('password');
			$user = Users::findFirst("id = '$user_id'");
			$user->first_name = $firstname;
			$user->last_name = $last_name;
			$user->company = $company;
			if($password!='none'){
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
					'number'=>$user->number,
					'admin_password_enable'=>$user->admin_password_enable,
					'contacts_invisible_mask'=>$user->contacts_invisible_mask
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
			// $user = Users::findFirst("id = '$user_id'");
			$user = Users::findFirstByEmail($email);
			if($user){
				if($user->id!=$user_id){
					$msg ="Already Exist";	
				}else{
					$msg = "kuch ni";
					if($user->number!=$number){
					$user->number = $number;
					}
				}
			}else{
				$msg ="emil bhejni h";
			}
			// if($email==$anotheruser->id){
			// $user->number = $number;
			// }else{

			// }
			// $user->email = $email;
			// if($user->save()){
			// 	$data = array(
			// 		'status'=>'success',
			// 		'email'=>$user->email,
			// 		'number'=>$user->number,
			// 		);
			// }
			$this->response->setContent(json_encode($msg));
			$this->response->send();	
		}
	}

	public function userSecurityAction(){
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$user_id = $this->request->getPost('user_id');
			$contactmask = $this->request->getPost('contactsecurity');
			$admin_status = $this->request->getPost('adminsecurity');
			$user = Users::findFirst("id = '$user_id'");
			$user->contacts_invisible_mask = $contactmask;
			$user->admin_password_enable = $admin_status;
			if($user->save()){
				$data = array(
					'status'=>'success',
					'msg' =>'Security Updted',
					'contacts_invisible_mask'=>$user->contacts_invisible_mask,
					'admin_password_enable'=>$user->admin_password_enable
					);
				$this->response->setContent(json_encode($data));
				$this->response->send();	
			}
		}
	}
	public function createContactAction(){
		if ($this->request->isPost() == true) {
			try{
				$this->response->setContentType('application/json');
				$user_id = $this->request->getPost('user_id');
				$email = $this->request->getPost('email');
				$number = $this->request->getPost('number','striptags');
				$contact_name = $this->request->getPost('contact_name');
				$address = $this->request->getPost('address');
				if($email==''||$email==null){
					$email = null;
				}
				$contact = new Contacts();
				$msg = 'contact created';
				$contact_data = array(
					'contact_name'=>$contact_name,
					'email'=>$email,
					'number'=>$number,
					'address'=>$address,
					'user_id'=>$user_id,
					'msg'=>$msg,
					);
				$contact->assign(array(
					'name' => $contact_name,
					'number' => $number,
					'user_id' => $user_id,
					'email' => $email,
					'address' => $address,
					));
				if($contact->save()){
					$data = array(
						'status'=>'success',
						'msg'=>$msg,
						'code'=>2
						);
				}else{
					$data = $this->updateContact($contact_data);
				}
			}catch(Exception $ex){
				$data = $this->updateContact($contact_data);
			}
			$this->response->setContent(json_encode($data));
			$this->response->send();	
		}
	}

	public function updateContactDataAction(){
		if ($this->request->isPost() == true) {
			try{
				$this->response->setContentType('application/json');
				$user_id = $this->request->getPost('user_id');
				$email = $this->request->getPost('email');
				$number = $this->request->getPost('number','striptags');
				$contact_name = $this->request->getPost('contact_name');
				$address = $this->request->getPost('address');
				$id = $this->request->getPost('contact_id');
				$contact =Contacts::findFirst("id='$id'");
				if($email==''||$email==null){
					$email = null;
				}
				$msg = 'contact updated';
				$contact_data = array(
					'contact_name'=>$contact_name,
					'email'=>$email,
					'number'=>$number,
					'address'=>$address,
					'user_id'=>$user_id,
					'msg'=>$msg,
					);
				$contact->assign(array(
					'name' => $contact_name,
					'number' => $number,
					'user_id' => $user_id,
					'email' => $email,
					'address' => $address,
					));
				$contact->save();
				$data = array(
					'status'=>'success',
					'msg'=>$msg,
					'code'=>2
					);
			}catch(Exception $ex){
				$data = $this->updateContact($contact_data);
			}
			$this->response->setContent(json_encode($data));
			$this->response->send();	
		}	
	}

	private function updateContact($contact_data){
		$number = $contact_data['number'];
		$user_id = $contact_data['user_id'];
		$contact =Contacts::findFirst("user_id=$user_id AND number LIKE '%$number%'");
		if($contact->deleted==1){
			$contact->name = $contact_data['contact_name'];
			$contact->address =$contact_data['address'];
			$contact->email =$contact_data['email'];
			$contact->deleted = 0;
			$contact->save();
			$data = array(
				'status'=>'success',
				'msg'=>$contact_data['msg'],
				'code'=>2
				);
		}else{
			$data = array(
				'status'=>'error',
				'msg'=>'Already Exist',
				'code'=>1
				);
		}
		return $data;
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

