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
}