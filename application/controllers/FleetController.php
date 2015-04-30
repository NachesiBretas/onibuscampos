<?php

class FleetController extends Zend_Controller_Action
{

    public function init()
    {
      $this->_helper->layout()->setLayout('dashboard');
      $authNamespace = new Zend_Session_Namespace('userInformation');
      $this->view->institution = $authNamespace->institution;
      $this->view->userId = $authNamespace->user_id;
      $mail = new Application_Model_Mail();
      $this->view->messages = $mail->getUnreadMessages();
      if($this->view->institution == 1)
      {
        $vehicle = new Application_Model_Vehicle();
        $this->view->vehiclesTransfered = count($vehicle->vehiclesTransfered());
        $this->view->vehiclesAskedCrv = count($vehicle->vehiclesAskedCrv());
        $this->view->vehiclesReviewed = count($reviews = $vehicle->reviews());
        $this->view->vehiclesEdited = count($reviews = $vehicle->reviewsEdited());
      }
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
      try{
        $vehicleId = $this->getRequest()->getParam('id');
        $this->view->save = $this->getRequest()->getParam('save');
        $vehicle = new Application_Model_Vehicle();
        if(!$vehicle->verifyAccess($vehicleId,$this->view->institution))
          return $this->redirect('doesntallow');
        
        if ( $this->getRequest()->isPost() ) 
        {
          $data = $this->getRequest()->getPost();
          if($vehicle->editVehicle($data,$vehicleId))
          {
            $vehicle_new = $vehicle->returnVehicleNew($vehicleId);
            if(!$vehicle_new){
              $vehicle->saveVehicleEdited($vehicleId);
            }
            $this->view->save = 'waiting'; 
          }
          else
          {
            $this->view->save = 'error';
          }
         
          if(isset($_FILES['file']) && !is_null($_FILES['file']))
          { 
            if($vehicle->saveDocument($_FILES,$data,$vehicleId))
            {
              $vehicle_new = $vehicle->returnVehicleNew($vehicleId);
              if(!$vehicle_new){
                $vehicle->saveVehicleEdited($vehicleId);
              }
              $this->view->save = 'success';
            }
            else
            {
              $this->view->save = 'error';
            }
          }

          $this->view->documentsForm = new Application_Form_VehicleDocuments();
          $this->view->inspectionForm = new Application_Form_VehicleInspection();
          $this->view->crlvForm = new Application_Form_VehicleDocumentCRLV();
          $this->view->comodatoForm = new Application_Form_VehicleDocumentComodato();
          $this->view->completed = $vehicle->checkMinimumRequirements($vehicleId);
          if($this->view->institution == 3 && $this->view->completed && $vehicleStatusRow->status != 4 && $vehicleStatusRow->status != 2)
          {
            $this->view->result = 6;
          }

        }
        else if($this->view->save = 'waiting'){
            $this->view->save = 'waiting';
        }

        $this->view->result = $vehicle->returnTab($vehicleId);

        $this->view->vehicleRow = $vehicle->returnById($vehicleId);
        $this->view->vehicleId = $vehicleId;
        $vehicleStatus = new Application_Model_DbTable_VehicleStatus();
        $vehicleStatusRow = $vehicleStatus->fetchRow($vehicleStatus->select()->where('vehicle_id = ?',$vehicleId));
        $this->view->status = $vehicleStatusRow->status;

        $this->view->mainForm = new Application_Form_Vehicle();
        $this->view->mainForm->populate($this->view->vehicleRow->toArray());

        $this->view->bodyForm = new Application_Form_VehicleMechanics();
        $this->view->bodyForm->populate($this->view->vehicleRow->toArray());

        $this->view->measuresForm = new Application_Form_VehicleMeasures();
        $this->view->measuresForm->populate($this->view->vehicleRow->toArray());

        $this->view->otherForm = new Application_Form_VehicleOther();
        $this->view->otherForm->populate($this->view->vehicleRow->toArray());
        
        $this->view->historicForm = new Application_Form_VehicleHistoric();
        $this->view->historic = $vehicle->returnHistoric($vehicleId);

        $consortium = new Application_Model_DbTable_Consortium();
        $this->view->consortiums = $consortium->fetchAll();
        $this->view->consortiumCompanies = new Application_Model_DbTable_ConsortiumCompanies();

      }catch(Zend_Exception $e){
        $this->view->save = 'error';
      }
    }

    public function removeAction()
    {
      $historicId = $this->getRequest()->getParam('id');
      $vehicle = new Application_Model_Vehicle();
      if ( $this->getRequest()->isPost() && (isset($historicId) && $historicId != '') ) 
      {
        $data = $this->getRequest()->getPost();
        if($vehicle->removeHistoric($historicId,$data['vehicleId']))
        {
          $this->_redirect('/fleet/edit/id/'.$data['vehicleId'].'/save/success');
        }
        else
        {
          $this->_redirect('/fleet/edit/id/'.$data['vehicleId'].'/save/failed');
        }
      }
    }

    public function reviewAction()
    {
      $authNamespace = new Zend_Session_Namespace('userInformation');
      $vehicle = new Application_Model_Vehicle();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($data['action'])
        {
          $status = 4;
        }
        else
        {
          $status = 7;
        }
        $vehicle->changeStatus($data['vehicle_id'],$status);
        $vehicle->saveLog($data['vehicle_id'], $status);
      }
      $save = $this->getRequest()->getParam('save');
      if($save == 'success')
      {
        $this->view->success = true;
      }
      if($save == 'failure')
      {
        $this->view->error = true;
      }
      $institution = $authNamespace->institution;
      if($institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $pagination = new Application_Model_Pagination();
      $page = $this->getRequest()->getParam('page');
      $reviews = $vehicle->reviews();
      if($page == '') $page = 1;
      $this->view->list = $pagination->generatePagination($reviews,$page,10);
    }

    public function refreshAction()
    {
      if ( $this->getRequest()->isPost()) 
      {
        $vehicle = new Application_Model_Vehicle();
        $data = $this->getRequest()->getPost();
        if(!$data['action'])
        {
          if($vehicle->removeVehicle($data['vehicleId']))
          {
            return $this->redirect('/fleet/view/save/success');
          }
          else
          {
            return $this->redirect('/fleet/view/save/failure');
          }
        }
      }
    }

    public function downloadFileAction()
    {
      $vehicleId = $this->getRequest()->getParam('id');
      $file = $this->getRequest()->getParam('file');
      $vehicle = new Application_Model_Vehicle();

      $fileDir = APPLICATION_PATH.'/vehicle/'.$vehicleId.'/'.$file;
      if(file_exists($fileDir))
      {
        $fd = fopen($fileDir, "r");
        $fsize = filesize($fileDir);
        $path_parts = pathinfo($fileDir);
        $ext = strtolower($path_parts["extension"]);

        header("Content-Type: application/download");
        header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\"");
        header("Content-length: $fsize");
        header("Cache-control: private");

        while(!feof($fd)) {
          $buffer = fread($fd, 2048);
          echo $buffer;
        }

        fclose($fd);
        exit;

      }
    }

    public function removeFileAction()
    {
      try{
        $vehicleId = $this->getRequest()->getParam('id');
        $file = $this->getRequest()->getParam('file');
        $vehicle = new Application_Model_Vehicle();
        if(!$vehicle->verifyAccess($vehicleId,$this->view->institution))
          return $this->redirect('doesntallow');

        $document = $vehicle->returnDocument($vehicleId,$file);

        $fileDir = APPLICATION_PATH . '/vehicle/'.$vehicleId.'/'.$file;
        if(unlink($fileDir))
        {
          if($vehicle->deleteDocument($document->id)){
            $this->redirect('/fleet/edit/id/'.$vehicleId.'/save/success');
          }
        }
        else
        {
          $this->redirect('/fleet/edit/id/'.$vehicleId.'/save/error');
        }
      }
      catch(Zend_Exception $e){
          $this->redirect('/fleet/edit/id/'.$vehicleId.'/save/error');
      }
    }

    public function saveAllAction()
    {
     if ( $this->getRequest()->isPost()) 
      {
        $vehicle = new Application_Model_Vehicle();
        $data = $this->getRequest()->getPost();

        $vehicle = new Application_Model_Vehicle();
        if(!$vehicle->verifyAccess($data['vehicleId'],$this->view->institution))
          return $this->redirect('doesntallow');

        if($vehicle->changeStatus($data['vehicleId'],2))
        {
          $vehicle->changeStatusVehicleNew($data['vehicleId']);
          $this->redirect('/fleet/protocol/id/'.$data['vehicleId']);
        }
      }
    }

    public function protocolAction()
    {
      $this->view->id = $this->getRequest()->getParam('id');
    }

    public function printCertificateAction()
    {
      try{
        if($this->view->institution != 1)
        {
          $this->_redirect('/doesntallow');
        }
        $id = $this->getRequest()->getParam('vehicle_id');
        $period = $this->getRequest()->getParam('period');
        $validity = $this->getRequest()->getParam('validity');
        $this->_helper->layout()->setLayout('ajax');
        header('Content-Type: application/pdf');
        $printCertificate = new Application_Model_VehiclePdfCertificate();
        $pdf = $printCertificate->createPdf($id, $period, $validity);
        echo $pdf->render();
      }catch(Zend_Exception $e){
        $this->view->error = true;
      }
    }

    public function processAction()
    {
      try{
        $vehicle = new Application_Model_Vehicle();
        $pagination = new Application_Model_Pagination();
        $page = $this->getRequest()->getParam('page');
        $list = $vehicle->returnVehicleInProcess();
        $this->view->list = $pagination->generatePagination($list,$page,10);
      }catch(Zend_Exception $e){
        $this->view->error = true;
      }
    }

    public function acceptReviewAction()
    {
      try{ 
        $vehicleId = $this->getRequest()->getParam('id');
        $period = $this->getRequest()->getParam('period');
        $validity = $this->getRequest()->getParam('validity');
        $this->_helper->layout()->setLayout('ajax');
        $vehicle = new Application_Model_Vehicle();        
        if ( $this->getRequest()->isPost() ) 
        {
          $result = $vehicle->acceptReview($vehicleId);
          $this->_redirect('/fleet/print-certificate/vehicle_id/'.$vehicleId.'/period/'.$period.'/validity/'.$validity);
        }

        $this->redirect('/fleet/review');
      }
      catch(Zend_Exception $e){
        $this->view->save = 'error';
      }
    }

    public function denyReviewAction()
    {
      try{ 
        $vehicleId = $this->getRequest()->getParam('id');
        $vehicle = new Application_Model_Vehicle();         
        if ( $this->getRequest()->isPost() ) 
        {
          $result = $vehicle->denyReview($vehicleId);
        }
        $this->redirect('/fleet/review');
      }catch(Zend_Exception $e){
        $this->view->save = 'error';
      }
    }

    public function transferAction()
    {
      try{
        $vehicle = new Application_Model_Vehicle();    
        $consortium = new Application_Model_DbTable_Consortium();
        $this->view->consortiums = $consortium->fetchAll();
        $this->view->consortiumCompanies = new Application_Model_DbTable_ConsortiumCompanies();
        $this->view->vehicleId = $this->getRequest()->getParam('id');    
        $this->view->vehicleRow = $vehicle->returnById($this->view->vehicleId);    
        if ( $this->getRequest()->isPost() ) 
        {
          $data = $this->getRequest()->getPost();
          if($vehicle->transfer($data['vehicle_id'],$data['consortium'],$data['consortium_company']))
          {
            $vehicle->changeStatus($data['vehicle_id'],9);
            $this->_redirect('/fleet/process');
          }
        }
      }catch(Zend_Exception $e){
        $this->view->save = 'error';
      }
    }

    public function reviewTransferAction()
    {
      $this->view->save = $this->getRequest()->getParam('save');
      $vehicle = new Application_Model_Vehicle();
      $transfered = $vehicle->vehiclesTransfered();
      $pagination = new Application_Model_Pagination();
      $page = $this->getRequest()->getParam('page');
      if($page == '') $page = 1;
      $this->view->list = $pagination->generatePagination($transfered,$page,10);
    }

    public function acceptTransferAction()
    {
      $vehicle = new Application_Model_Vehicle();         
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($vehicle->acceptTransfer($data['id']))
        {
          $this->_redirect('/fleet/print-certificate/id/'.$data['vehicle_id']);
        }
        else
        {
          $this->_redirect('/fleet/review-transfer/save/error');
        }
      }
    }

    public function denyTransferAction()
    {
      $vehicle = new Application_Model_Vehicle();         
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($vehicle->denyTransfer($data['id']))
        {
          $this->_redirect('/fleet/review-transfer/save/success');
        }
        else
        {
          $this->_redirect('/fleet/review-transfer/save/error');
        }
      }
      else
      {
        $this->_redirect('/fleet/view');
      }
    }

    public function downAction()
    {
      $vehicle = new Application_Model_Vehicle();         
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($vehicle->down($data['vehicle_id']))
        {
          // Removido provisoriamente para os consórcios regularizarem as baixas
          // $vehicle->changeStatus($data['vehicle_id'],10);
          // $this->_redirect('/agendamento/index/id/'.$data['vehicle_id']);
          if($vehicle->downVehicle($data['vehicle_id']))
          {
            $this->_redirect('/fleet/view/save/success');
          }
          else
          {
            $this->_redirect('/fleet/view/save/error');
          }
        }
        else
        {
          $this->_redirect('/fleet/view/save/error');
        }
      }
      else
      {
        $this->_redirect('/fleet/view');
      }
    }

    public function askCrvAction()
    {
      $vehicle = new Application_Model_Vehicle();         
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($vehicle->askCrv($data['vehicle_id'], $data['body']))
        {
          $this->_redirect('/fleet/view/save/success');
        }
        else
        {
          $this->_redirect('/fleet/view/save/error');
        }
      }
      else
      {
        $this->_redirect('/fleet/view');
      }
    }

    public function reviewCrvAction()
    {
      $this->view->save = $this->getRequest()->getParam('save');
      $vehicle = new Application_Model_Vehicle();
      $asked = $vehicle->vehiclesAskedCrv();
      $pagination = new Application_Model_Pagination();
      $page = $this->getRequest()->getParam('page');
      if($page == '') $page = 1;
      $this->view->list = $pagination->generatePagination($asked,$page,10);
    }

    public function acceptCrvAction()
    {
      try{ 
        $vehicleId = $this->getRequest()->getParam('id');
        $period = $this->getRequest()->getParam('period');
        $validity = $this->getRequest()->getParam('validity');
        $this->_helper->layout()->setLayout('ajax');
        $vehicle = new Application_Model_Vehicle();        
        if ( $this->getRequest()->isPost() ) 
        {
          $result = $vehicle->acceptCrv($vehicleId);
          $this->_redirect('/fleet/print-certificate/vehicle_id/'.$vehicleId.'/period/'.$period.'/validity/'.$validity);
        }

        $this->redirect('/fleet/review-crv');
      }
      catch(Zend_Exception $e){
        $this->view->save = 'error';
      }
    }

    public function denyCrvAction()
    {
      $vehicle = new Application_Model_Vehicle();         
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($vehicle->denyCrv($data['id']))
        {
          $this->_redirect('/fleet/review-crv/save/success');
        }
        else
        {
          $this->_redirect('/fleet/review-crv/save/error');
        }
      }
      else
      {
        $this->_redirect('/fleet/review-crv');
      }
    }

    public function reportActiveAction()
    {
      $vehicle = new Application_Model_Vehicle(); 
      $fleet = $vehicle->returnVehicleActive();
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename=Veiculos.csv');
      header('Pragma: no-cache');
      header('Expires: 0');
      echo "NUMERO;PLACA;RENAVAM;ANO CARROCERIA;ANO CHASSI;DATA ENTRADA;LARGURA PORTA DIANTEIRA;LARGURA PORTA TRASEIRA;LARGURA PORTA CENTRAL;CHASSI;POTENCIA MOTOR;ASSENTOS;ELEVADOR;TANQUE;VALIDADOR;LACRE ROLETA;DATA LACRE;SEGURADORA;DATA SEGURO;PESO;LARGURA ANTES ROLETA;COMPRIMENTO ANTES ROLETA;COMPRIMENTO DEF;LARGURA DEF;LARGURA APOS ROLETA;COMPRIMENTO APOS ROLETA;COR;PISO;PADRAO;MODELO CHASSI;LOCAL MOTOR;MODELO CARROCERIA;SUSPENSAO;CAMBIO;TIPO ASSENTO;TIPO VEICULO;CONSORCIO;CELULA OPERACIONAL;COBRADOR;SERVICO;\n";
      foreach ($fleet as $vehicle) 
      {
        $external_number = str_pad($vehicle->external_number, 5, "0", STR_PAD_LEFT);
        if($vehicle->elevator)
        {
          $vehicle->elevator = 'S';
        }
        else
        {
          $vehicle->elevator = 'N';
        }
        if($vehicle->eletronic_roulette)
        {
          $vehicle->eletronic_roulette = 'S';
        }
        else
        {
          $vehicle->eletronic_roulette = 'N';
        }
        if($vehicle->collector_area)
        {
          $vehicle->collector_area = 'S';
        }
        else
        {
          $vehicle->collector_area = 'N';
        }
        echo "$external_number;$vehicle->plate;$vehicle->renavam;$vehicle->body_year;$vehicle->chassi_year;".Application_Model_General::dateToBr($vehicle->start_historic_date).";$vehicle->width_door_front_right;$vehicle->width_door_middle1_right;$vehicle->width_door_back_right;$vehicle->chassi_number;$vehicle->motor_power;$vehicle->amount_seats;$vehicle->elevator;$vehicle->tank1;$vehicle->eletronic_roulette;$vehicle->seal_roulette;$vehicle->seal_date;$vehicle->insurer;$vehicle->insurer_date;$vehicle->weight;$vehicle->width_before_roulette;$vehicle->length_before_roulette;$vehicle->width_deficient;$vehicle->length_deficient;$vehicle->width_after_roulette;$vehicle->length_after_roulette;$vehicle->color_name;NORMAL;$vehicle->pattern_name;$vehicle->model_chassi_name;$vehicle->motor_localization_name;$vehicle->body_model_name;$vehicle->suspension_name;$vehicle->cambium_name;$vehicle->seat_name;$vehicle->type_name;".($vehicle->consortium_name).";".($vehicle->company_name).";$vehicle->collector_area;$vehicle->service_name;\n";
      }
      exit;
    }

    public function deleteAction()
    {
      if ( $this->getRequest()->isPost()) 
      {
        $vehicle = new Application_Model_Vehicle();
        $data = $this->getRequest()->getPost();
        if(isset($data['vehicle_id']) && $data['vehicle_id'] != '' && $this->view->institution == 1)
        {
          if($vehicle->delete($data['vehicle_id']))
          {
            $this->_redirect('/fleet/view/save/success');
          }
          else
          {
            $this->_redirect('/fleet/view/save/error');
          }
        }
        else
        {
          $this->_redirect('/fleet/view');
        }
      }
    }

    public function reviewEditedAction()
    {
      $authNamespace = new Zend_Session_Namespace('userInformation');
      $vehicle = new Application_Model_Vehicle();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($data['action'])
        {
          $status = 4;
        }
        else
        {
          $status = 7;
        }
        $vehicle->changeStatus($data['vehicle_id'],$status);
        $vehicle->saveLog($data['vehicle_id'], $status);
      }
      $save = $this->getRequest()->getParam('save');
      if($save == 'success')
      {
        $this->view->success = true;
      }
      if($save == 'failure')
      {
        $this->view->error = true;
      }
      $institution = $authNamespace->institution;
      if($institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $pagination = new Application_Model_Pagination();
      $page = $this->getRequest()->getParam('page');
      $reviews = $vehicle->reviewsEdited();
      if($page == '') $page = 1;
      $this->view->list = $pagination->generatePagination($reviews,$page,10);
    }

    public function reportAllAction()
    {
      $vehicle = new Application_Model_Vehicle(); 
      $fleet = $vehicle->returnAllVehicle();
      header('Content-Type: text/csv');
      header('Content-Disposition: attachment; filename=Veiculos.csv');
      header('Pragma: no-cache');
      header('Expires: 0');
      echo "NUMERO;PLACA;RENAVAM;ANO CARROCERIA;ANO CHASSI;DATA ENTRADA;DATA SAÍDA;LARGURA PORTA DIANTEIRA;LARGURA PORTA TRASEIRA;LARGURA PORTA CENTRAL;CHASSI;POTENCIA MOTOR;ASSENTOS;ELEVADOR;TANQUE;VALIDADOR;LACRE ROLETA;DATA LACRE;SEGURADORA;DATA SEGURO;PESO;LARGURA ANTES ROLETA;COMPRIMENTO ANTES ROLETA;COMPRIMENTO DEF;LARGURA DEF;LARGURA APOS ROLETA;COMPRIMENTO APOS ROLETA;COR;PISO;PADRAO;MODELO CHASSI;LOCAL MOTOR;MODELO CARROCERIA;SUSPENSAO;CAMBIO;TIPO ASSENTO;TIPO VEICULO;CONSORCIO;CELULA OPERACIONAL;COBRADOR;SERVICO;\n";
      foreach ($fleet as $vehicle) 
      {
        // $external_number = str_pad($vehicle->external_number, 5, "0", STR_PAD_LEFT);  doesnt work
        $external_number = $vehicle->external_number;
        if($vehicle->elevator)
        {
          $vehicle->elevator = 'S';
        }
        else
        {
          $vehicle->elevator = 'N';
        }
        if($vehicle->eletronic_roulette)
        {
          $vehicle->eletronic_roulette = 'S';
        }
        else
        {
          $vehicle->eletronic_roulette = 'N';
        }
        if($vehicle->collector_area)
        {
          $vehicle->collector_area = 'S';
        }
        else
        {
          $vehicle->collector_area = 'N';
        }
        echo "$external_number;$vehicle->plate;$vehicle->renavam;$vehicle->body_year;$vehicle->chassi_year;".Application_Model_General::dateToBr($vehicle->start_historic_date).";".Application_Model_General::dateToBr($vehicle->end_historic_date).";$vehicle->width_door_front_right;$vehicle->width_door_middle1_right;$vehicle->width_door_back_right;$vehicle->chassi_number;$vehicle->motor_power;$vehicle->amount_seats;$vehicle->elevator;$vehicle->tank1;$vehicle->eletronic_roulette;$vehicle->seal_roulette;$vehicle->seal_date;$vehicle->insurer;$vehicle->insurer_date;$vehicle->weight;$vehicle->width_before_roulette;$vehicle->length_before_roulette;$vehicle->width_deficient;$vehicle->length_deficient;$vehicle->width_after_roulette;$vehicle->length_after_roulette;$vehicle->color_name;NORMAL;$vehicle->pattern_name;$vehicle->model_chassi_name;$vehicle->motor_localization_name;$vehicle->body_model_name;$vehicle->suspension_name;$vehicle->cambium_name;$vehicle->seat_name;$vehicle->type_name;".($vehicle->consortium_name).";".($vehicle->company_name).";$vehicle->collector_area;$vehicle->service_name;\n";
      }
      exit;
    }

    public function acceptReviewEditedAction()
    {
      try{ 
        $this->_helper->layout()->setLayout('ajax');
        $vehicle = new Application_Model_Vehicle();        
        if ( $this->getRequest()->isPost() ) 
        {
          $vehicleId = $this->getRequest()->getParam('id');
          $result = $vehicle->reviewEditedStatus($vehicleId);
          $this->redirect('/fleet/review-edited');
        }
      }
      catch(Zend_Exception $e){
        $this->view->save = 'error';
      }
    }

    public function addDocumentsAction()
    {
      $this->view->vehicleId = $this->getRequest()->getParam('id');
      $this->view->documentsForm = new Application_Form_VehicleDocuments();
      $this->view->inspectionForm = new Application_Form_VehicleInspection();
      $this->view->crlvForm = new Application_Form_VehicleDocumentCRLV();
      $this->view->comodatoForm = new Application_Form_VehicleDocumentComodato();
      if ( $this->getRequest()->isPost() ) 
      {
        $vehicle = new Application_Model_Vehicle(); 
        $data = $this->getRequest()->getPost();
        if(isset($_FILES['file']) && !is_null($_FILES['file']))
        { 
          if($vehicle->saveDocument($_FILES,$data,$this->view->vehicleId))
          {
            $this->view->save = 'success';
          }
          else
          {
            $this->view->save = 'error';
          }
        }
      }
    }

}

























































