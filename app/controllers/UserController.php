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
	
	public function helloAction(){
	
	//$data = $this->request->getJsonRawBody();
	$email = $this->request->getPost('email');
	//$response = new Response();
	$this->response->setContentType('application/json');
	//$this->$response->setJsonContent(array('status' => 'OK', 'data' => $email));
	$this->response->setContent(json_encode(array('hello')));
			$this->response->send();
	}
	


}

