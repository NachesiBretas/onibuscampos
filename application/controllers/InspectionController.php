<?php

class InspectionController extends Zend_Controller_Action
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
      $inspection = new Application_Model_Inspection();
      $this->view->vehiclesAskedCrv = count($inspection->vehiclesAskedCrv());
    }

    public function indexAction()
    {
    	
    }

    public function vehicleAction()
    {
      try{
    		$vehicleId = $this->getRequest()->getParam('id');
        $this->view->save = $this->getRequest()->getParam('save');
        $vehicle = new Application_Model_Vehicle();
        if(!$vehicle->verifyAccess($vehicleId,$this->view->institution))
          return $this->redirect('doesntallow');
        
        if ( $this->getRequest()->isPost() ) 
        {
          $data = $this->getRequest()->getPost();
          if($data['document'] == 'inspection' || $data['document'] == 'notification'){
            if(isset($_FILES['file']) && !is_null($_FILES['file']))
            {
              if($vehicle->saveDocument($_FILES,$data,$vehicleId) && ($data['date_inspection'] != '' || $data['date_notification'] != '')){
                $result = $vehicle->editVehicle($data,$vehicleId);
                $this->view->save = 'success';
              }
              else{
                $this->view->save = 'error';
              }
            }
            else{
              $this->view->save = 'error';
            }
          }
          else{
            $result = $vehicle->editVehicle($data,$vehicleId);
            $this->view->save = 'success';
          }
        }

        $vehicleRow = $vehicle->returnById($vehicleId);
        $this->view->vehicleRow = $vehicleRow;
        $this->view->vehicleId = $vehicleId;
        $this->view->sealData = $vehicle->returnSealData($vehicleId);
        $this->view->inspectionData = $vehicle->returnInspectionData($vehicleId);
        $this->view->notificationData = $vehicle->returnNotificationData($vehicleId);

        $this->view->inspectionForm = new Application_Form_VehicleInspection();
        $this->view->notificationForm = new Application_Form_VehicleNotification();
        $this->view->seal = new Application_Form_VehicleSeal();
        $this->view->crlvForm = new Application_Form_VehicleDocumentCRLV();
        $this->view->maintenanceForm = new Application_Form_VehicleDocuments();
        $inspection = new Application_Model_Inspection();
        $this->view->completed = $inspection->returnMinimunRequirements($vehicleId);
      }catch(Zend_Exception $e){
        $this->view->save = 'error';
      }
    }

    public function saveAllAction()
    {
     	if ( $this->getRequest()->isPost()) 
      {
        $vehicle = new Application_Model_Vehicle();
        $data = $this->getRequest()->getPost();

        if(!$vehicle->verifyAccess($data['vehicleId'],$this->view->institution))
          return $this->redirect('doesntallow');

        if($data['block'] == 1){
          if($vehicle->changeStatus($data['vehicleId'],8))
          {
            $this->redirect('/inspection/protocol-block');
          }
        }
        else{
          if($vehicle->changeStatus($data['vehicleId'],3))
          {
            $this->redirect('/inspection/protocol');
          }
        }

      }
    }

    public function protocolAction()
    {
        // action body
    }

    public function protocolBlockAction()
    {
        // action body
    }

    public function saveRouletteAction()
    {
      if ( $this->getRequest()->isPost()) 
      {
        $data = $this->getRequest()->getPost();
        $vehicle = new Application_Model_Vehicle();
        if($vehicle->saveRoulette($data))
        {
          return $this->redirect('/inspection/vehicle/id/'.$data['vehicle_id'].'/save/success');
        }
        return $this->redirect('/inspection/vehicle/id/'.$data['vehicle_id'].'/save/error');
      }
        return $this->redirect('/inspection');
    }

    public function viewAction()
    {
      $inspection = new Application_Model_Inspection();
      $pagination = new Application_Model_Pagination();
      $field = $this->getRequest()->getParam('field');
      $page = $this->getRequest()->getParam('page');
      $option = $this->getRequest()->getParam('option');
      if($page == '') $page = 1;
      if($field != "")
      {
        if($option == 1)
        {
          $vehicles = $inspection->returnByPlate(urldecode($field));
        }
        if($option == 2)
        {
          $vehicles = $inspection->returnByRenavam(urldecode($field));
        }
        if($option == 3)
        {
          $vehicles = $inspection->returnByExternalNumber(urldecode($field));
        }
        $this->view->field = $field;
        $this->view->option = $option;
      }
      else
      {
        $vehicles = $inspection->listVehicles();
      }
      $this->view->list = $pagination->generatePagination($vehicles,$page,10);
    }

    public function downAction()
    {
      $inspection = new Application_Model_Inspection();
      $pagination = new Application_Model_Pagination();
      $this->view->save = $this->getRequest()->getParam('save');
      $page = $this->getRequest()->getParam('page');
      if($page == '') $page = 1;
      $vehicles = $inspection->listVehiclesDown();
      $this->view->list = $pagination->generatePagination($vehicles,$page,10);
    }

    public function acceptDownAction()
    {
      if ( $this->getRequest()->isPost()) 
      {
        $data = $this->getRequest()->getPost();
        $inspection = new Application_Model_Inspection();
        if($inspection->acceptDown($data['vehicle_id']))
        {
          $this->_redirect('/inspection/down/save/success');
        }
        else
        {
          $this->_redirect('/inspection/down/save/error');
        }
      }
    }

    public function denyDownAction()
    {
      if ( $this->getRequest()->isPost()) 
      {
        $data = $this->getRequest()->getPost();
        $inspection = new Application_Model_Inspection();
        if($inspection->denyDown($data['vehicle_id']))
        {
          $this->_redirect('/inspection/down/save/success');
        }
        else
        {
          $this->_redirect('/inspection/down/save/error');
        }
      }
    }

    public function reviewCrvAction()
    {
      $this->view->save = $this->getRequest()->getParam('save');
      $inspection = new Application_Model_Inspection();
      $vehiclesAskedCrv = $inspection->vehiclesAskedCrv();
      $pagination = new Application_Model_Pagination();
      $page = $this->getRequest()->getParam('page');
      if($page == '') $page = 1;
      $this->view->list = $pagination->generatePagination($vehiclesAskedCrv,$page,10);
    }

    public function denyCrvAction()
    {
      $inspection = new Application_Model_Inspection();         
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($inspection->denyCrv($data['id']))
        {
          $this->_redirect('/inspection/review-crv/save/success');
        }
        else
        {
          $this->_redirect('/inspection/review-crv/save/error');
        }
      }
    }

    public function acceptCrvAction()
    {
      $inspection = new Application_Model_Inspection();         
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($inspection->acceptCrv($data['id']))
        {
          $this->_redirect('/inspection/review-crv/save/success');
        }
        else
        {
          $this->_redirect('/inspection/review-crv/save/error');
        }
      }
    }


}





















