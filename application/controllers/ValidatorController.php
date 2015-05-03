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
        $vehicleService = new Application_Model_DbTable_VehicleService();
        $vehicleServiceSelect = $vehicleService->select()->order("name ASC");
        $this->view->vehicleService = $vehicleService->fetchAll($vehicleServiceSelect);

        $vehiclePattern = new Application_Model_DbTable_VehiclePattern();
        $vehiclePatternSelect = $vehiclePattern->select()->order("name ASC");
        $this->view->vehiclePattern = $vehiclePattern->fetchAll($vehiclePatternSelect);

        $vehicleColor = new Application_Model_DbTable_VehicleColor();
        $vehicleColorSelect = $vehicleColor->select()->order("name ASC");
        $this->view->vehicleColor = $vehicleColor->fetchAll($vehicleColorSelect);

        $vehicleType = new Application_Model_DbTable_VehicleType();
        $vehicleTypeSelect = $vehicleType->select()->order("name ASC");
        $this->view->vehicleType = $vehicleType->fetchAll($vehicleTypeSelect);

        $consortium = new Application_Model_DbTable_Consortium();
        $consortiumRow = $consortium->select()->order("name ASC");
        $this->view->consortium = $vehicleType->fetchAll($consortiumRow);

        if ( $this->getRequest()->isPost() ) 
        {
          $data = $this->getRequest()->getPost();
          $vehicle = new Application_Model_Vehicle();
          $vehicleId = $vehicle->newVehicle($data);
          $vehicle->newStatus($vehicleId,$this->view->userId);
          $data['vehicle_id'] = $vehicleId;
          if($this->view->institution == 3) 
          {
            $vehicle->saveHistoric($data,$vehicleId);
            $this->_redirect('/fleet/edit/id/'.$vehicleId.'/save/waiting');
          }
          $this->_redirect('/fleet/edit/id/'.$vehicleId.'/save/success');
        }
      }catch(Zend_Exception $e){
        $this->view->save = 'error';
      }
    }

    public function viewAction()
    {
      try
      {
        $this->view->save = $this->getRequest()->getParam('save');
        $vehicle = new Application_Model_Vehicle();
        $pagination = new Application_Model_Pagination();
        $field = $this->getRequest()->getParam('field');
        $page = $this->getRequest()->getParam('page');
        $option = $this->getRequest()->getParam('option');
        if($page == '') $page = 1;
        if($field != "")
        {
          if($option == 1)
          {
            $vehicles = $vehicle->returnByPlate(urldecode($field));
          }
          if($option == 2)
          {
            $vehicles = $vehicle->returnByRenavam(urldecode($field));
          }
          if($option == 3)
          {
            $vehicles = $vehicle->returnByExternalNumber(urldecode($field));
          }
          if(isset($vehicles) && count($vehicles))
            $this->view->list = $pagination->generatePagination($vehicles,$page,10);
          $this->view->field = $field;
          $this->view->option = $option;
        }
        else
        {
          $vehicles = $vehicle->lists();
          if(isset($vehicles) && count($vehicles))
            $this->view->list = $pagination->generatePagination($vehicles,$page,10);
        }
      }catch(Zend_Exception $e){
        // $this->view->save = 'error';
      }
    }

    public function reportAction()
    {
        // action body
    }

    public function visAction()
    {
      $historicId = $this->getRequest()->getParam('id');
      if(!$historicId)
      {
        $this->_redirect('/fleet/view');
        return;
      }
      $vehicle = new Application_Model_Vehicle();
      $this->view->vehicleRow = $vehicle->returnByHistoricId($historicId);
    }

    public function editAction()
    {

    }

}

























































