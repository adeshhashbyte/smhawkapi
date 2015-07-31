<?php 
class SMSGateway {
	public static function sendSMS($smsdata) {
		$smppcredentail = Smppclient::findFirst("status = 'ACTIVE'");
		$params = array();
		if($smppcredentail->providername =='alotsolution_api'){
			$params['username']=$smppcredentail->username;
			$params['password']=$smppcredentail->password;
			$params['reqid'] = 1;
			$params['to'] = $smsdata['to'];
			$params['sender'] = $smsdata['sender_id'];
			$params['message'] = $smsdata['message'];
			$params['format'] = 'json';
			$url=$smppcredentail->url;
		}else{
			$params['user']=$smppcredentail->username;
			$params['password']=$smppcredentail->password;
			$params['text'] = $smsdata['message'];
			$params['to'] = $smsdata['to'];
			$params['from'] = $smsdata['sender_id'];
			$url=$smppcredentail->url.'/sendsms?';
		}
		$request = self::makeRequest($url,$params);
		return $request;
	}
	public static function makeRequest($api_url,$params) {
		$url = $api_url. http_build_query($params);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		$data = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($status == 200) {
			$return = array('status'=>$status,'data',$data);
		} else {
			$return = array('status'=>$status,'data',$data);
		}
		return $return;
	}

}