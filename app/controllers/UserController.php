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
}

