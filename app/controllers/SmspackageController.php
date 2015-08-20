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


}

