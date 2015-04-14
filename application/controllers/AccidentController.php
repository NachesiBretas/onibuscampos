<?php

class AccidentController extends Zend_Controller_Action
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

    public function newAction()
    {
      if( $this->getRequest()->isPost() ) 
      { 
        try
        {
          $accident = new Application_Model_Accident();
          $reds = $accident->loadFile($_FILES);
          if($reds !== "exists" || $reds !== "")
          {
            $this->view->save = "success";
          }
          else
          {
            $this->view->save = "error";
          }
        }catch(Zend_Exception $e){
           $this->view->save = "true";
        }
      }
    }

    public function editAction()
    {
      $accident = new Application_Model_Accident();
      $id = $this->getRequest()->getParam('id');
      if($id != '')
      {
        $pagination = new Application_Model_Pagination();
        $page = $this->getRequest()->getParam('page');
        $accidentRow = $accident->returnById($id);
        $this->view->list = $pagination->generatePagination($accidentRow,$page,10);
      }
      else
      {
        $this->_redirect('/accident/view');
      }
    }

    public function viewAction()
    {
      $accident = new Application_Model_Accident();
      $pagination = new Application_Model_Pagination();
      $page = $this->getRequest()->getParam('page');
      $ini_date = $this->getRequest()->getParam('ini_date');
      $end_date = $this->getRequest()->getParam('end_date');
      if($page == '') $page = 1;
      if(($ini_date != "") && ($end_date != "")){      
          $accident = $accident->returnByDate(Application_Model_General::dateToUs($ini_date), Application_Model_General::dateToUs($end_date));
          $this->view->field = $field;
          $this->view->list = $pagination->generatePagination($accidents,$page,10);
        }
      else{
        $accidents = $accident->lists();
        $this->view->list = $pagination->generatePagination($accidents,$page,10);
      }
    }

    public function deleteAction()
    {
      try{
        $ini_date = $this->getRequest()->getParam('ini_date');
        $end_date = $this->getRequest()->getParam('end_date');
        $accidentId = $this->getRequest()->getParam('id');
        if ($accidentId) 
        {
          $accidentData = new Application_Model_AccidentData(); 
          $accident = new Application_Model_Accident();
          $accidentData->deleteFile($accidentId);
          if($accident->deleteFile($accidentId))
          {
            $this->_redirect('/accident/view/save/success');
          }
        }
      }catch(Zend_Exception $e){
        $this->view->error = true;
      }
    }

    public function reportAction()
    {
        // action body
    }

    public function visAction()
    {
     $this->_helper->layout()->setLayout('dashboard');
     $this->view->list = $this->getRequest()->getParam('id');
     $this->view->accident = true;
    }

    public function reportAccidentByDateAction()
    {
        $this->_helper->layout()->setLayout('report');
        $ini_date = Application_Model_General::dateToUs($this->getRequest()->getParam('ini_date'));
        $end_date = Application_Model_General::dateToUs($this->getRequest()->getParam('end_date'));
        $accident = new Application_Model_Accident();
        $this->view->listRit = $accident->returnCityQuantById($ini_date, $end_date);
        $this->view->listCity = $accident->returnAccidentsCity($ini_date, $end_date);
        $this->view->list2 = $ini_date;
        $this->view->list3 = $end_date;
    }


}

