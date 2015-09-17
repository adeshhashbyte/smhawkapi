<?php

class PublishTask extends \Phalcon\CLI\Task
{
	public function mainAction(){
		echo "\nThis is the default task and the default action \n";
	}
	
	public function publishAction(array $params){
		if(isset($params[0])){
			$id = $params[0];
			$sms_data = SmsHistory::findFirst("id ='$id' AND status ='PENDING'");
			if($sms_data){
				$user_id = $sms_data->user_id;
				$user = Users::findFirst("id = '$user_id'");
				if($user->smsbalance->balance >= $sms_data->billcredit){
					switch($sms_data->type){	
						case 'GROUPID':
						$result =Groups::getGroupNumber(json_decode($sms_data->reciever));
						break;
						case 'NUMBER':
						$result = implode(',',json_decode($sms_data->reciever));
						break;
						case 'CONTACTID':
						$result =Contacts::getNumbers(json_decode($sms_data->reciever));
						break;
					}
					$data = $this->sendSMSRequest(array(
						"message" =>urldecode($sms_data->message),
						'sender_id' => $user->sender_id,
						'contacts'=>explode(',', $result)
						));
					$sms_data->status="SUCCESS";
					$user->smsbalance->balance = $user->smsbalance->balance - $sms_data->billcredit;
					$user->smsbalance->used = $user->smsbalance->used + $sms_data->billcredit;
					$user->smsbalance->save();
				}else{
					$sms_data->status = "FAILED";
				}
				$sheduled_sms = SheduleSms::findFirst("sms_id = '$id'");
				if($sheduled_sms->id){
					$sheduled_sms->delete();
				}
				$sms_data->created_at = date("Y-m-d H:i:s");
				$sms_data->updated_at = date("Y-m-d H:i:s");
				$sms_data->save();
			}else{
				echo "\n Task Not Found \n";
			}
		}
	}

	private function sendSMSRequest($sms_data) {
		// print_r($sms_data);
		$sendmsgservice = new SMSGateway();
		foreach (array_chunk($sms_data['contacts'], 50) as $value) {
			$number_list = implode(' ', $value);
			$data[] = array(
				'to' => $number_list,
				'message' => $sms_data['message'],
				'sender_id' => $sms_data['sender_id'],
				);
		}
		foreach ($data as $send_sms_data) {
			$response = $sendmsgservice->sendSMS($send_sms_data);
		}
		$return = array("do_send_sms" => "SUCCESS");
		return $return;
	}


}