<?php

class GsectionController extends Zend_Controller_Action
{

  public function init()
  {
    $this->_helper->layout()->setLayout('dashboard');
    $authNamespace = new Zend_Session_Namespace('userInformation');
    $institution = $authNamespace->institution;
    if($institution != 1)
    {
      $this->_redirect('/doesntallow');
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
    $authNamespace = new Zend_Session_Namespace('userInformation');
      if( $this->getRequest()->isPost() ) 
    { 
      try
      {
        $gsection = new Application_Model_Gsection();
        $fare = $gsection->loadFile($_FILES);
        if($fare !== "exists" || $fare !== ""){
          $this->view->save = "success";
        }
        else{
          $this->view->save = "error";
        }
      }
      catch(Zend_Exception $e){
         $this->view->save = "true";
      }
    }
  }

  public function editAction()
  {
      // action body
  }

  public function deleteAction()
  {
      // action body
  }


  public function viewAction()
  {
    $section_data = new Application_Model_Gsection();
    $pagination = new Application_Model_Pagination();
    $page = $this->getRequest()->getParam('page');
    $field = $this->getRequest()->getParam('field');
    $gsection = $section_data->lastUpload();
    if(isset($_GET["optionSearch"]))
    {
      $optionSearch = $_GET["optionSearch"];
    }
    if($page == ''){
      $page = 1;
    }
    if($field != "" && ($optionSearch <= 4 && $optionSearch >= 1))
    {
     if($optionSearch == 1)
     {
       $section_datas = $section_data->findByNum(urldecode($field),$gsection->last);
      }
     if($optionSearch == 2)
     {
       $section_datas = $section_data->findByName(urldecode($field),$gsection->last);
      }
     if($optionSearch == 3)
     {
       $section_datas = $section_data->findByOld(urldecode($field),$gsection->last);
      }
     if($optionSearch == 4)
     {
       $section_datas = $section_data->findByNew(urldecode($field),$gsection->last);
      }
     $this->view->field = $field;
     $this->view->list = $pagination->generatePagination($section_datas,$page,10);
     $this->view->optionSearch = $optionSearch;
    }
    elseif($gsection->last != '')
    {
      $data = $section_data->findAll($gsection->last);
      $this->view->list = $pagination->generatePagination($data,$page,10);
    }
  }
}
