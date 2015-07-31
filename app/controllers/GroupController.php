<?php
use Phalcon\Http\Response;
class GroupController extends \Phalcon\Mvc\Controller

{
	public function initialize() {
		$this->view->disable();
	}
	public function indexAction()
	{

	}
	public function groupcontactAction(){
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
			$group_id = $this->request->getPost('group_id');
			$groucontact = GroupContact::find("group_id=$group_id");
			$groucontactlist = array();
			foreach ($groucontact as $group_data) {
				$groucontactlist[]=array(
					'contact_id'=>$group_data->contact_id,
					'name'=>$group_data->contacts->name,
					'number'=>$group_data->contacts->number,
					);
			}
			$this->response->setContent(json_encode(array('groucontactlist'=>$groucontactlist)));
			$this->response->send();	
		}
	}
	public function grouphistoryAction(){
		if ($this->request->isPost() == true) {
			$this->response->setContentType('application/json');
		 	$group_id = $this->request->getPost('group_id');
			$grouphistory = SmsHistory::find("group_id='$group_id'");
			$groupsmshistory = array();
			foreach ($grouphistory as $history) {
				$groupsmshistory[]=array(
					'group_id'=>$history->group_id,
					'msg'=>urldecode($history->message),
					);
			}
			$this->response->setContent(json_encode(array('groupsmshistory'=>$groupsmshistory)));
			$this->response->send();	
		}
	}

	public function creategroupAction(){
		if ($this->request->isPost() == true) {
			try{
			$this->response->setContentType('application/json');
		 	$groupname = $this->request->getPost('groupname', 'striptags');
		 	$user_id = $this->request->getPost('user_id');
		 	$group = new Groups();
		 	$group->assign(array(
		 		'user_id' => $user_id,
		 		'name' => $groupname,
		 		'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
		 		'updated_at' =>(new \DateTime())->format('Y-m-d H:i:s')
		 		));
		 	if($group->save()){
		 		$data = array(
						'status'=>'success',
						'msg'=>'group created',
						'code'=>2,
						'group_name'=>$group->name,
						);
		 	}
			$this->response->setContent(json_encode($data));
			$this->response->send();	
			}catch(Exception $ex){
				$this->response->setContent(json_encode(array('error'=>$ex->getMessage())));
				$this->response->send();
			}
		}
	}
}