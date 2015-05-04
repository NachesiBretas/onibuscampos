<?php

class ValidatorController extends Zend_Controller_Action
{

    public function init()
    {
      $this->_helper->layout()->setLayout('dashboard');
      $authNamespace = new Zend_Session_Namespace('userInformation');
      $this->view->institution = $authNamespace->institution;
      $this->view->userId = $authNamespace->user_id;
      $mail = new Application_Model_Mail();
      $this->view->messages = $mail->getUnreadMessages();
    }

    public function indexAction()
    {
        // action body
    }

    public function newAction()
    {
      try{
        $this->view->save = $this->getRequest()->getParam('save');
        $validator = new Application_Model_Validator();
        $consortium_company = 1;
        $this->view->percentage = $validator->reservationValidator($consortium_company);
        if ( $this->getRequest()->isPost() ) 
        {
          if($validator->allowReg($consortium_company)){
            $data = $this->getRequest()->getPost();
            $validatorId = $validator->newValidator($data);
            if($validatorId)
            {
              $this->_redirect('/validator/new/save/success');
            }
          }
          else{
            $this->_redirect('/validator/new/save/failed');
          }
        }
      }catch(Zend_Exception $e){
       // $this->view->error = true;
      }
    }

    public function viewAction()
    {
      try
      {
        $validator = new Application_Model_Validator();
        $pagination = new Application_Model_Pagination();
        $page = $this->getRequest()->getParam('page');
        $validator = $validator->listValidators();
        $this->view->list = $pagination->generatePagination($validator,$page,10);

      }catch(Zend_Exception $e){
        $this->view->save = 'error';
        echo $e->getMessage();
      }
    }

    public function deleteValidatorAction()
    {
      try
      {
        $validator = new Application_Model_Validator();
        $id = $this->getRequest()->getParam('id');
        $validatorId = $validator->deleteValidator($id);
        if($validatorId)
          {
            $this->_redirect('/validator/view');
          }

      }catch(Zend_Exception $e){
        $this->view->save = 'error';
        echo $e->getMessage();
      }
    }

}

























































