<?php
use Phalcon\Db\Column;
use Phalcon\Session\Adapter;
class AuthController extends \Phalcon\Mvc\Controller
{

	
	public function initialize() {
		$this->view->disable();
	}

	public function indexAction() {
	}
	public function loginAction() {
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$email = $this->request->getPost("email");
			$password = $this->request->getPost("password");
			$user = Users::findFirst("email = '$email'");
			if ($user) {
				if ($this->security->checkHash($password, $user->password)) {
                //The password is valid
					$data = array(
						'status'=>'success',
						'msg'=>'login success',
						'code'=>2,
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
				}else{
					$data = array(
						'status'=>'failed',
						'msg'=>'worng password',
						'code'=>1,
						);
				}
			}else{
				$data = array(
					'status'=>'failed',
					'msg'=>'user not found',
					'code'=>1,
					);
			}
			$this->response->setContent(json_encode($data));
			$this->response->send();
		}
	}
	public function gloginAction() {
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$email = $this->request->getPost("email");
			$image_url = $this->request->getPost("image_url");
			$name = $this->request->getPost("name");
			$authtoken = $this->request->getPost("authtoken");
			$user = Users::findFirst("email='$email'");
			if ($user) {
				$user->api_token=$authtoken;
				$user->avatar=$image_url;
				$user->first_name=$name;
				$user->save();
                //The password is valid
				$data = array(
					'status'=>'success',
					'msg'=>'login success',
					'code'=>2,
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
			}else{
				$user = new User();
				$user->email=$email;
				$user->activated=1;
				$user->password='changeme';
				$user->avatar=$image_url;
				$user->api_token=$authtoken;
				$user->sender_id='SMHAWK';
				$user->first_name=$name;
				if($user->save()){
					$data = array(
						'status'=>'success',
						'msg'=>'user created',
						'code'=>2,
						'user_id'=>$user->id,
						'first_name'=>$user->first_name,
						'last_name'=>$user->last_name,
						'sender_id'=>$user->sender_id,
						'company'=>$user->company,
						'email'=>$user->email,
						'number'=>$user->number
						);
				}
			}
			$this->response->setContent(json_encode($data));
			$this->response->send();
		}
	}

	public function signupAction(){
		if ($this->request->isPost()) {
			if ($this->request->getPost()) {
				$this->response->setContentType('application/json');
				$email = $this->request->getPost('email', 'striptags');
				if(!Users::findFirst("email = '$email'")){
					$user = new Users();
					$user->assign(array(
						'first_name' => $this->request->getPost('firstname', 'striptags'),
						'last_name' => $this->request->getPost('lastname', 'striptags'),
						'email' => $this->request->getPost('email', 'striptags'),
						'password' => $this->security->hash('changeme')
						));
					if($user->save()){
						$data = array(
							'code'=>1,
							'msg'=>'A confirmation mail has been sent to' . $user->email,
							'status'=>'success',
							);

					}else{
						$data = array(
							'code'=>2,
							'msg'=>'something went wrong',
							'status'=>'error',
							);
					}
				}else{
					$data = array(
						'code'=>2,
						'msg'=>'Already Exist',
						'status'=>'error',
						);
				}
				$this->response->setContent(json_encode($data));
				$this->response->send();	
			}

		}
	}

	/**
         * Confirms an e-mail, if the user must change its password then changes it
    */
	public function confirmEmail2Action()
	{
		if ($this->request->isPost()) {
			if ($this->request->getPost()) {
				$this->response->setContentType('application/json');
				$code =  $this->request->getPost('code');
				$confirmation = EmailConfirmations::findFirstByCode($code);
				if (!$confirmation) {
					$data = array(
						'code'=>1,
						'status'=>'error',
						'msg'=>'invalid code',
						);
				}else{
					if ($confirmation->confirmed <> 'N') {
						$confirmation->confirmed = 'Y';
						$confirmation->user->activated = 1;
						$confirmation->code = 'ffghfghfhf';
						$confirmation->save();
						$data = array(
							'code'=>2,
							'status'=>'success',
							'msg'=>'The email was successfully confirmed. Now you must change your password',
							);
					}
				}
				$this->response->setContent(json_encode($data));
				$this->response->send();
			}
		}
	}

}

