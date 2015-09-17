<?php

class MainTask extends \Phalcon\CLI\Task
{
	public function mainAction(){
		echo "\nThis is the default task and the default action \n";
	}
	
	public function testAction(){
		$str = date('Y-m-d H:i:s');
		$sheduled_sms = SheduleSms::find(array('conditions' => "shedule_date <= '$str' AND status ='SHEDULED' ",'columns' => 'id, sms_id,shedule_date'));
		if ($sheduled_sms->count() > 0) {
			foreach ($sheduled_sms as  $shedule_sms) {
				$cmd ='php '.__DIR__.'/../../app/cli.php publish publish '.$shedule_sms->sms_id;
				exec('nohup '.$cmd.' > /dev/null 2>&1 &');
			}
		}else{
			echo 'Shedule SMS not found';
		}
	}
}