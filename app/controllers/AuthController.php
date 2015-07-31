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
				$user = new Users();
				$user->assign(array(
					'first_name' => $this->request->getPost('first_name', 'striptags'),
					'last_name' => $this->request->getPost('last_name', 'striptags'),
					'email' => $this->request->getPost('email'),
					// 'password' => $this->security->hash($this->request->getPost('password')),
					));
				if($user->save()){
					$this->response->setContent(json_encode('success'));
				}else{
					$this->flash->error($user->getMessages());
					$this->response->setContent(json_encode('error'));	
				}
				$this->response->send();	
			}

		}
	}

	/**
         * Confirms an e-mail, if the user must change its password then changes it
    */
    public function confirmEmailAction()
    {
            $code = $this->dispatcher->getParam('code');
            $confirmation = EmailConfirmations::findFirstByCode($code);
            if (!$confirmation) {
                    return $this->dispatcher->forward(array(
                            'controller' => 'index',
                            'action' => 'index'
                    ));
            }
            if ($confirmation->confirmed <> 'N') {
                $this->flash->success('The email was successfully confirmed. Now you must change your password');
                return $this->dispatcher->forward(array(
                        'controller' => 'session',
                        'action' => 'login'
                ));
            }
            //confirmation
            $confirmation->confirmed = 'Y';
            $confirmation->user->active = 1;
            /**
                 * Change the confirmation to 'confirmed' and update the user to 'active'
            */
            if ($confirmation->save()) {
                    return $this->dispatcher->forward(array(
                            'controller' => 'session',
                            'action' => 'login'
                    ));
            }
            else{
                foreach ($confirmation->getMessages() as $message) {
                        $this->flash->error($message);
                }
            }
    }

}

