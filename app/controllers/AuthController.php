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
	public function getSocialDataAction() {
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$auth = $this->request->getPost("auth");
			$auth_data = array();
			$auth_data = Networks::findFirst("platform = '$auth'")->toArray();
			$this->response->setContent(json_encode(array("auth_data"=>$auth_data)));
			$this->response->send();
		}
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
						'number'=>$user->number,
						'admin_password_enable'=>$user->admin_password_enable,
						'contacts_invisible_mask'=>$user->contacts_invisible_mask
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
					'number'=>$user->number,
					'admin_password_enable'=>$user->admin_password_enable,
					'contacts_invisible_mask'=>$user->contacts_invisible_mask
					);
				$data['history']=array(
					'used'=>$user->smsbalance->used,
					'balance'=>$user->smsbalance->balance,
					);
			}else{
				$user = new Users();
				$user->email=$email;
				$user->activated=1;
				$user->password=$this->security->hash('changeme');
				$user->avatar=$image_url;
				$user->api_token=$authtoken;
				$user->first_name=$name;
				if($user->save()){
					$smsbalance = new SmsBalance();
					$smsbalance->assign(array(
						'user_id' => $user->id,
						'balance' => 0,
						'used' => 0,
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' => date("Y-m-d H:i:s"),
						));
					$smsbalance->save();
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
						'number'=>$user->number,
						'admin_password_enable'=>$user->admin_password_enable,
						'contacts_invisible_mask'=>$user->contacts_invisible_mask
						);
					$data['history']=array(
						'used'=>$user->smsbalance->used,
						'balance'=>$user->smsbalance->balance,
						);
					$emilsend = Users::onsocialSignUpSuccess($data);
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
						'password' => $this->security->hash('changeme'),
						'activated' => 0,
						));
					if($user->save()){
						$emailConfirmation = new EmailConfirmations();
						$emailConfirmation->usersId = $user->id;
						$emailConfirmation->save();
						$smsbalance = new SmsBalance();
						$smsbalance->assign(array(
							'user_id' => $user->id,
							'balance' => 0,
							'used' => 0,
							'created_at' => date("Y-m-d H:i:s"),
							'updated_at' => date("Y-m-d H:i:s"),
							));
						$smsbalance->save();
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
	public function confirmEmail2Action(){
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
				$this->response->setContent(json_encode($data));
				$this->response->send();
			}
		}
	}

	public function changePasswordAction(){
		if ($this->request->isPost()) {
			if ($this->request->getPost()) {
				$this->response->setContentType('application/json');
				$email =  $this->request->getPost('email');
				$password =  $this->request->getPost('password');
				$user = Users::findFirstByEmail($email);
				if (!$user) {
					$data = array(
						'code'=>1,
						'status'=>'error',
						'msg'=>'invalid email',
						);
				}else{
					$user->password = $this->security->hash($password);
					$user->save();
					$data = array(
						'code'=>2,
						'status'=>'success',
						'msg'=>'Password change successfully now you can login',
						);
				}
				$this->response->setContent(json_encode($data));
				$this->response->send();
			}
		}
	}

}

