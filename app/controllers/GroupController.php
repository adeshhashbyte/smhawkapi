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

	public function addMoreContactAction(){
		if ($this->request->isPost() == true) {
			$group_id = $this->request->getPost('group_id');
			$user_id = $this->request->getPost('user_id');
			$groucontact = GroupContact::find("group_id=$group_id");
			$contact_ids = array();
			foreach($groucontact as $contact){
				$contact_ids[] = $contact->contact_id;
			}
			$contacts ='';
			$contacts =implode(',', $contact_ids);
			if(count($contact_ids)>0){
				$other_contacts = Contacts::find(array(
					'conditions' => 'id NOT IN ('.$contacts.') AND user_id='.$user_id.' AND deleted=0',
					'columns' => 'id,name,number,email'
					));
			}else{
				$other_contacts = Contacts::find(array(
					'conditions' => 'user_id='.$user_id.' AND deleted=0',
					'columns' => 'id,name,number,email'
					));
			}
			$morecontacts = array();
			foreach($other_contacts as $other_contact){
				$morecontacts[]=array(
					'id'=> $other_contact->id,
					'name'=> $other_contact->name,
					'number'=> $other_contact->number,
					'email'=> $other_contact->email,
					);
			}
			$this->response->setContent(json_encode(array('morecontacts'=>$morecontacts)));
			// $this->flash->error($other_contacts->getMessages());
			$this->response->send();
		}else{

		}
	}

	public function saveMoreContactgroupAction(){
		if ($this->request->isPost() == true) {
			$group_id = $this->request->getPost('group_id');
			$user_id = $this->request->getPost('user_id');
			$contact_ids = $this->request->getPost('contact_ids');
			$contacts =explode(',',$contact_ids);
			foreach ($contacts as $contact_id) {
				$groucontact = new GroupContact();
				$groucontact->assign(array(
					'group_id' => $group_id,
					'contact_id' => $contact_id,
					'created_at' => (new \DateTime())->format('Y-m-d H:i:s'),
					'updated_at' =>(new \DateTime())->format('Y-m-d H:i:s')
					));
				$groucontact->save();
			}
			$this->response->setContent(json_encode(array('success'=>'success','code'=>'2','count'=>count($contacts))));
			 // $this->flash->error($groucontact->getMessages());
			$this->response->send();
		}else{
		}
	}

	public function removeGroupContactAction(){
		if ($this->request->isPost() == true) {
			$group_id = $this->request->getPost('group_id');
			$user_id = $this->request->getPost('user_id');
			$contact_ids = $this->request->getPost('contact_ids');
			$contact_ids =explode(',',$contact_ids);
			$group_contacts = array();
			foreach ($contact_ids as $contact_id) {
				$group_contacts= GroupContact::find('contact_id ='.$contact_id);
				foreach ($group_contacts as $group) {
					if($group_id==$group->group_id){
						$group->delete();
					}
				}
			}
			$this->response->setContent(json_encode(array('success'=>'success','code'=>'2')));
			$this->response->send();
		}else{
		}
	}

	public function moveToGroupContactAction(){
		if ($this->request->isPost() == true) {
			$group_id = $this->request->getPost('group_id');
			$contact_ids = $this->request->getPost('contact_ids');
			$contacts =explode(',',$contact_ids);
			$i=0;
			foreach ($contacts as $contact_id) {
				try {
					$groucontact = new GroupContact();
					$groucontact->assign(array(
						'group_id' => $group_id,
						'contact_id' => $contact_id,
						'created_at' => date("Y-m-d H:i:s"),
						'updated_at' =>date("Y-m-d H:i:s")
						));
					$groucontact->save();
					$i++;
					
				} catch (Exception $ex) {
					continue;
				}
			}
			if($i==0){
				$this->response->setContent(json_encode(array('success'=>'error','code'=>'1','msg'=>'Already Exist')));	
			}else{
				$this->response->setContent(json_encode(array('success'=>'success','code'=>'2','msg'=>$i.' Contacts Move Successfully')));
			}
			 // $this->flash->error($groucontact->getMessages());
			$this->response->send();
		}
	}
}