<?php

class AccountController extends Zend_Controller_Action
{

    public function init()
    {
      $this->_helper->layout()->setLayout('dashboard');
      $user = new Application_Model_DbTable_User();
			$authNamespace = new Zend_Session_Namespace('userInformation');
    	$this->view->user = $user->fetchRow($user->select()->where('id = ?',$authNamespace->user_id));
      $mail = new Application_Model_Mail();
      $this->view->messages = $mail->getUnreadMessages();
    }

    public function indexAction()
    {
        // action body
    }

    public function personalAction()
    {
      if ( $this->getRequest()->isPost() ) 
      {
      	try{
	        $data = $this->getRequest()->getPost();
	        $user = new Application_Model_DbTable_User();
					$authNamespace = new Zend_Session_Namespace('userInformation');
	      	$userRow = $user->fetchRow($user->select()->where('id = ?',$authNamespace->user_id));
	      	$userRow->name = $data['name'];
	      	$userRow->phone = $data['phone'];
	      	$userRow->email = $data['email'];
	      	$userRow->save();
	      	$this->view->user = $userRow;
	      	$this->view->success = true;
	      }catch(Zend_Exception $e){
	      	$this->view->error = true;
	      }
      }
    }

    public function photoAction()
    {
        // action body
    }

    public function passwordAction()
    {
      if ( $this->getRequest()->isPost() ) 
      {
      	$data = $this->getRequest()->getPost();
        $user = new Application_Model_DbTable_User();
				$authNamespace = new Zend_Session_Namespace('userInformation');
      	$userRow = $user->fetchRow($user->select()->where('id = ?',$authNamespace->user_id));
      	if($userRow->password == sha1($data['old_password']) && $data['confirm_password'] == $data['password'])
      	{
      		$userRow->password = sha1($data['password']);
      		$userRow->save();
	      	$this->view->success = true;
      	}
      	else
      	{
      		$this->view->password_error = true;
      	}
      }
    }


}







