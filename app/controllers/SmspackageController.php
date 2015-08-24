<?php

class SmspackageController extends \Phalcon\Mvc\Controller
{
	public function initialize() {
		$this->view->disable();
	}

	public function indexAction()
	{

	}

	public function smspackageAction() {
		$this->response->setContentType('application/json');
		$smspackages = SmsPackages::find();
		$packages = array();
		foreach($smspackages as $smspackage){
			$packages[] = array(
				"name" => $smspackage->name,
				"sms_credit" => $smspackage->sms_credit,
				"price" => $smspackage->price
				);
		}
		$this->response->setContent(json_encode(array('packages'=>$packages)));
		$this->response->send();
	}

	public function bonusPlansAction(){
		$this->response->setContentType('application/json');
		$bonus = array(
			'5000'=>'5',
			'10000'=>'10',
			'20000'=>'20',
			'50000'=>'25'
			);
		$this->response->setContent(json_encode(array('bonus'=>$bonus)));
		$this->response->send();
	}

	public function postPaymentProccessAction(){
		$this->response->setContentType('application/json');
		$amount = $this->request->getPost('amount');
		$packagename ='custom';
		$smscredit = $this->request->getPost('sms_credit');
		$user_id = $this->request->getPost('user_id');
		$user = Users::findFirst("id = '$user_id'");
		// $MERCHANT_KEY = "JBZaLc";
		$MERCHANT_KEY = "6cna40";
		$name = $user->first_name;
         // $salt = "GQs7yium";
		$salt = "FL37RYx9";
		$PAYU_BASE_URL = "https://secure.payu.in";
         // $PAYU_BASE_URL = "https://test.payu.in";
		$txnid = substr(hash('sha256', mt_rand() . microtime()), 0, 20);
		$hash_string = "$MERCHANT_KEY|$txnid|$amount|$packagename|$name|$user->email|$smscredit||||||||||$salt";
		$hash = strtolower(hash('sha512', $hash_string));
		$payment_data = array(
			'action' => $PAYU_BASE_URL . '/_payment',
			'key' => $MERCHANT_KEY,
			'hash' => $hash,
			'txnid' => $txnid,
			'amount' => $amount,
			'phone' => $user->number,
			'packagename' =>'custom',
			'name' => $name,
			'email' => $user->email,
			'sms_credit' => $smscredit
			);
		$this->response->setContent(json_encode(array('payment_proccess'=>$payment_data)));
		$this->response->send();
	}


}

