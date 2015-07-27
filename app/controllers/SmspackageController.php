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


}

