<?php

class FinanceController extends Zend_Controller_Action
{

    public function init()
    {
      $this->_helper->layout()->setLayout('dashboard');
      $authNamespace = new Zend_Session_Namespace('userInformation');
      $this->view->institution = $authNamespace->institution;
      if($this->view->institution != 1)
      {
        return $this->_redirect('/doesntallow');
      }
      $mail = new Application_Model_Mail();
      $this->view->messages = $mail->getUnreadMessages();
    }

    public function indexAction()
    {
        // action body
    }

    public function groupFareAction()
    {
      $finance = new Application_Model_Finance();
      $pagination = new Application_Model_Pagination();
      $page = $this->getRequest()->getParam('page');
      if($page == '') $page = 1;
      $finance = $finance->listGroupFare();
      $this->view->list = $pagination->generatePagination($finance,$page,10);
    }

    public function groupFareNewAction()
    {
      $this->view->groupFareForm = new Application_Form_FinanceFare();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        $finance = new Application_Model_Finance();
        if($finance->newGroupFare($data))
        {
           $this->view->save = 'success';
        }
        else
        {
          $this->view->groupFareForm->populate($data);
          $this->view->save = 'error';
        }
      }
    }

    public function groupFareEditAction()
    {
      $save = $this->getRequest()->getParam('save');
      $groupFareId = $this->getRequest()->getParam('id');
      $this->view->groupFareForm = new Application_Form_FinanceFare();
      $finance = new Application_Model_Finance();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($finance->editGroupFare($data,$groupFareId))
        {
           $this->view->save = 'success';
        }
        else
        {
          $this->view->save = 'error';
        }
      }
      $this->view->groupFare = $finance->returnGroupFareById($groupFareId);
      $this->view->groupFareForm->reset();
      $this->view->groupFareForm->populate($this->view->groupFare->toArray());
      $this->view->historicGroupFare = $finance->returnHistoricGroupFareById($groupFareId);
    }

    public function groupFareRemoveAction()
    {
        // action body
    }

    public function groupSectionAction()
    {
        // action body
    }

    public function groupSectionNewAction()
    {
        // action body
    }

    public function groupSectionEditAction()
    {
        // action body
    }

    public function groupSectionRemoveAction()
    {
        // action body
    }


}

















