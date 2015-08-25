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

	public function getPaymentSuccessAction(){
		if ($this->request->isPost()) {
			$amount = $this->request->getPost('amount');
			$status = $this->request->getPost('status');
			$txnid = $this->request->getPost('txnid');
			$smscredit = $this->request->getPost('udf1');
			$gateway_txnid = $this->request->getPost('payuMoneyId');
			$user = Users::findFirstByEmail($this->request->getPost('email'));
			if($status == 'success'){
				$user->smsbalance->balance =$user->smsbalance->balance + $smscredit;
				$transactionhistory = new TransactionHistory();
				$transactionhistory->amount = $amount;
				$transactionhistory->user_id = $user->id;
				$transactionhistory->sms_credit = $smscredit;
				$transactionhistory->txnid = $txnid;
				$transactionhistory->gateway_txnid = $gateway_txnid;
				$transactionhistory->new_sms_balance = $user->smsbalance->balance;
				$transactionhistory->status = 'SUCCESS';
				$transactionhistory->created_at = date("Y-m-d H:i:s");
				$transactionhistory->updated_at = date("Y-m-d H:i:s");
				$transactionhistory->save();
				$user->smsbalance->save();
				$this->response->redirect($this->config->application->apiUri.'/payment-success/'.$txnid);
			}else{
				$this->response->redirect($this->config->application->apiUri.'/failed/'.$txnid);
			}
		}
	}
	public function getPaymentSuccessApiAction(){
		if ($this->request->isPost()) {
			$this->response->setContentType('application/json');
			$txnid = $this->request->getPost('order_id');
			$transaction = TransactionHistory::findFirstByTxnid($txnid);
			if($transaction){
				$order = array(
					'invoice_id' => $transaction->id,
					'code' => 1,
					'sms_credit' => $transaction->sms_credit,
					'amount' => $transaction->amount,
					'transation_id' => $transaction->txnid,
					'new_sms_balance' => $transaction->new_sms_balance,
					'invoice_txnid' => $transaction->gateway_txnid,
					'date' => date('M d, Y',strtotime($transaction->updated_at)),
					);
				$this->response->setContent(json_encode($order));
			}else{
				$data = array(
					'code'=>2,
					'msg'=>'Invalid Id',
					);
				$this->response->setContent(json_encode($data));
			}
			$this->response->send();
		}
	}
	public function getPaymentFailerAction(){
		$amount = $this->request->getPost('amount');
		$status = $this->request->getPost('status');
		$txnid = $this->request->getPost('txnid');
		$smscredit = $this->request->getPost('udf1');
		$gateway_txnid = $this->request->getPost('payuMoneyId');
		$user = Users::findFirstByEmail($this->request->getPost('email'));
		$transation = TransactionHistory::findFirstByGatewayTxnid($gateway_txnid);
		if(!$transation){
			$transactionhistory = new TransactionHistory();
			$transactionhistory->amount = $amount;
			$transactionhistory->user_id = $user->id;
			$transactionhistory->sms_credit = $smscredit;
			$transactionhistory->txnid = $txnid;
			$transactionhistory->gateway_txnid = $gateway_txnid;
			$transactionhistory->new_sms_balance = $user->smsbalance->balance;
			$transactionhistory->status = 'FAILED';
			$transactionhistory->created_at = date("Y-m-d H:i:s");
			$transactionhistory->updated_at = date("Y-m-d H:i:s");
			$transactionhistory->save();
			// $this->flash->error($transactionhistory->getMessages());
			$this->response->redirect($this->config->application->apiUri.'/payment-fail/'.$txnid);
		}else{
			$this->response->redirect($this->config->application->apiUri.'/payment-fail/'.$txnid);
		}
	}


}

