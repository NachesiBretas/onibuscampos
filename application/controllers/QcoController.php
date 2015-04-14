<?php

class QcoController extends Zend_Controller_Action
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
      try{
        $this->view->mainForm = new Application_Form_Qco();
        if ( $this->getRequest()->isPost() ) 
        {
          $data = $this->getRequest()->getPost();
          $qco = new Application_Model_Qco();
          $qcoId = $qco->newQCO($data);
          if($qcoId)
          {
            $this->_redirect('/qco/edit/id/'.$qcoId.'/save/success');
          }
        }
      }catch(Zend_Exception $e){
        $this->view->error = true;
      }
    }

    public function viewAction()
    {
      $qco = new Application_Model_Qco();
      $pagination = new Application_Model_Pagination();
      $page = $this->getRequest()->getParam('page');
      $this->view->field = $this->getRequest()->getParam('field');
      if($page == '') $page = 1;
      if($this->view->field != '')
      {
        $qco = $qco->listByName($this->view->field);
      }
      else
      {
        $qco = $qco->lists();
      }
      $this->view->list = $pagination->generatePagination($qco,$page,10);
    }

    public function reportAction()
    {
      // action body
    }

    public function visAction()
    {
        // action body
    }

    public function editAction()
    {
      try
      {
        $qcoId = $this->getRequest()->getParam('id');
        $save = $this->getRequest()->getParam('save');

        $qco = new Application_Model_Qco();
        if( $this->getRequest()->isPost() ) 
        {
          $data = $this->getRequest()->getPost();
          $this->view->result = $qco->editQco($data,$qcoId);
          if($this->view->result)
          {
            $result = true;
          }
          else
          {
            $result = false;
          }
        }

        if($save == 'success' || (isset($result) && $result) )
        {
          $this->view->save = 'success';
        }
        else if(isset($result) && $result == false)
        {
          $this->view->save = 'error'; 
        }
        $typeDay = new Application_Model_DbTable_QcoTypeDay();
        $this->view->typeDay = $typeDay->fetchAll();
        $typeJourney = new Application_Model_DbTable_QcoTypeJourney();
        $this->view->typeJourney = $typeJourney->fetchAll();
        $consortium = new Application_Model_DbTable_Consortium();
        $this->view->consortiumCompanies = new Application_Model_DbTable_ConsortiumCompanies();
        $this->view->consortiumCompanies = new Application_Model_DbTable_ConsortiumCompanies();
        $this->view->consortiums = $consortium->fetchAll(); 
        $fare = new Application_Model_DbTable_FinanceFare();
        $this->view->fares = $fare->fetchAll();
        $this->view->integration_fares = $qco->returnIntegrationFare();
        $this->view->qcoMain = $qco->returnMainById($qcoId);
        $this->view->qcoRoute = $qco->returnRouteById($qcoId);
        $this->view->qcoFleet = $qco->returnFleetById($qcoId);
        // $this->view->qcoHour = $qco->returnHourById($qcoId);
        $this->view->qcoId = $qcoId;
        $this->view->mainForm = new Application_Form_Qco();
        $this->view->historicForm = new Application_Form_QcoHistoric();
        $this->view->routeForm = new Application_Form_QcoRoute();


      }catch(Zend_Exception $e){
        $this->view->save = 'error';
        echo $e->getMessage();
      }
    }

    public function newShapeAction()
    {
      try
      {
        if( $this->getRequest()->isPost() ) 
        {
          $data = $this->getRequest()->getPost();
          $qco = new Application_Model_Qco();
          $qco->saveShape($_FILES,$data);
        }
      }catch(Zend_Exception $e){
        $this->view->error = true;
        // echo $e->getMessage();
      }
    }

    public function downloadFileAction()
    {
      $qcoId = $this->getRequest()->getParam('id');
      $qco = new Application_Model_Qco();
      $fileDir = APPLICATION_PATH . '/shape/'.$qcoId.'/'.$qcoId.'.zip';
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header("Content-Type: application/force-download");    
      header('Content-Disposition: attachment; filename=' . urlencode(basename($fileDir)));
      header("Content-Length: " . filesize($fileDir));
      header('Expires: 0');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
      readfile($fileDir);
      $this->view->layout()->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);
    }

    public function removeFileAction()
    {
      $qcoId = $this->getRequest()->getParam('id');
      $fileDir = APPLICATION_PATH . '/shape/'.$qcoId;
      $qco = new Application_Model_Qco();
      if($qco->deleteDirectory($fileDir))
      {
        $this->redirect('/qco/edit/id/'.$qcoId.'/save/success');
      }
      else
      {
        $this->redirect('/qco/edit/id/'.$qcoId.'/save/error');
      }
    }

    public function printAction()
    {
      try{
        $qcoId = $this->getRequest()->getParam('id');
        if(!$qcoId)
        {
          $this->redirect('/qco/view');
        }
        $this->_helper->layout()->setLayout('ajax');
        header('Content-Type: application/pdf');
        $printQco = new Application_Model_QcoPdf();
        $pdf = $printQco->createPdf($qcoId);
        echo $pdf->render();
      }catch(Zend_Exception $e){
        echo $e->getMessage();
      }
    }

    public function printHistoricAction()
    {
      try{
        $qcoId = $this->getRequest()->getParam('id');
        if(!$qcoId)
        {
          $this->redirect('/qco/historic');
        }
        $this->_helper->layout()->setLayout('ajax');
        header('Content-Type: application/pdf');
        $printQco = new Application_Model_QcoHistoricPdf();
        $pdf = $printQco->createPdf($qcoId);
        echo $pdf->render();
      }catch(Zend_Exception $e){
        echo $e->getMessage();
      }
    }

    public function removeQhAction()
    {
      $qcoHourId = $this->getRequest()->getParam('id');
      $data = $this->getRequest()->getPost();
      $qco = new Application_Model_Qco();
      if($qco->removeQH($qcoHourId))
      {
        $this->_redirect('/qco/edit/id/'.$data['qcoId'].'/save/success');
      }
      else
      {
        $this->_redirect('/qco/edit/id/'.$data['qcoId'].'/save/failed');
      }
    }

    public function removeLogAction()
    {
      if( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        $qco = new Application_Model_Qco();
        if($qco->removeLog($data['logId']))
        {
          $this->_redirect('/qco/edit/id/'.$data['qcoId'].'/save/success');
        }
        else
        {
          $this->_redirect('/qco/edit/id/'.$data['qcoId'].'/save/failed');
        }
      }
    }

    public function newQhAction()
    {
      if( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        $qco = new Application_Model_Qco();
        if($qco->saveQH($_FILES,$data))
        {
          $this->_redirect('/qco/edit/id/'.$data['qco_id'].'/save/success');
        }
        else
        {
          $this->_redirect('/qco/edit/id/'.$data['qco_id'].'/save/failed');
        }
      }
    }

    public function historicAction()
    {
      $qco = new Application_Model_Qco();
      $pagination = new Application_Model_Pagination();
      $page = $this->getRequest()->getParam('page');
      $field = $this->getRequest()->getParam('field');
      if($page == '') $page = 1;
      if($field != '')
      {
        $qco = $qco->listHistoricByName($field);
      }
      else
      {
        $qco = $qco->listHistoric();
      }
      $this->view->list = $pagination->generatePagination($qco,$page,10);
    }

    public function completeHistoricAction()
    {
      try
      {
        $qcoId = $this->getRequest()->getParam('id');

        $qco = new Application_Model_Qco();
        $this->view->qcoMain = $qco->returnHistoricMainById($qcoId);
        $this->view->qcoRoute = $qco->returnHistoricRouteById($qcoId);
       // print_r($qcoRoute);exit;
        $this->view->qcoFleet = $qco->returnHistoricFleetById($qcoId);
        $this->view->qcoHour = $qco->returnHistoricHourById($qcoId);
        $this->view->qcoId = $qcoId;

        $this->view->mainForm = new Application_Form_QcoMainHistoric();
        $this->view->routeForm = new Application_Form_QcoHistoricRoute();

        $typeDay = new Application_Model_DbTable_QcoTypeDay();
        $this->view->typeDay = $typeDay->fetchAll();

      }catch(Zend_Exception $e){
        $this->view->save = 'error';
        echo $e->getMessage();
      }
    }

    public function calendarAction()
    {
      try
      {
        $qco = new Application_Model_Qco();
        $pagination = new Application_Model_Pagination();
        if ( $this->getRequest()->isPost() ) 
        {
          $data = $this->getRequest()->getPost();
          $qcoId = $qco->newGeneralCalendar($data);
          if($qcoId)
          {
            $this->_redirect('/qco/calendar/save/success');
          }
        }
        $page = $this->getRequest()->getParam('page');
        $qco = $qco->listGeneralCalendar();
        $this->view->list = $pagination->generatePagination($qco,$page,10);

      }catch(Zend_Exception $e){
        $this->view->save = 'error';
        echo $e->getMessage();
      }
    }

    public function deleteGeneralCalendarAction()
    {
      try
      {
        $qco = new Application_Model_Qco();
        $id = $this->getRequest()->getParam('id');
        $calendarId = $qco->deleteGeneralCalendar($id);
        if($calendarId)
          {
            $this->_redirect('/qco/calendar');
          }

      }catch(Zend_Exception $e){
        $this->view->save = 'error';
        echo $e->getMessage();
      }
    }

    public function lineCalendarAction()
    {
      try
      {
        $qco = new Application_Model_Qco();
        $pagination = new Application_Model_Pagination();
        if ( $this->getRequest()->isPost() ) 
        {
          $data = $this->getRequest()->getPost();
          if($data['number_field'] != ''){
            $this->view->number_field = $data['number_field'];
          }
          else{
            $qcoId = $qco->newLineCalendar($data);
            if($qcoId)
            {
              $this->_redirect('/qco/line-calendar/save/success');
            }
          }
        }
        $page = $this->getRequest()->getParam('page');
        $qco = $qco->listLineCalendar();
        $this->view->list = $pagination->generatePagination($qco,$page,10);

      }catch(Zend_Exception $e){
        $this->view->save = 'error';
        echo $e->getMessage();
      }
    }

    public function deleteLineCalendarAction()
    {
      try
      {
        $qco = new Application_Model_Qco();
        $id = $this->getRequest()->getParam('id');
        $calendarId = $qco->deleteLineCalendar($id);
        if($calendarId)
          {
            $this->_redirect('/qco/line-calendar');
          }

      }catch(Zend_Exception $e){
        $this->view->save = 'error';
        echo $e->getMessage();
      }
    }

    public function consortiumCalendarAction()
    {
      try
      {
        $qco = new Application_Model_Qco();
        $pagination = new Application_Model_Pagination();
        if ( $this->getRequest()->isPost() ) 
        {
          $data = $this->getRequest()->getPost();
          if($data['number_field'] != ''){
            $this->view->number_field = $data['number_field'];
          }
          else{
            $qcoId = $qco->newConsortiumCalendar($data);
            if($qcoId)
            {
              $this->_redirect('/qco/consortium-calendar/save/success');
            }
          }
        }
        $page = $this->getRequest()->getParam('page');
        $qco = $qco->listConsortiumCalendar();
        $this->view->list = $pagination->generatePagination($qco,$page,10);

      }catch(Zend_Exception $e){
        $this->view->save = 'error';
        echo $e->getMessage();
      }
    }

    public function deleteConsortiumCalendarAction()
    {
      try
      {
        $qco = new Application_Model_Qco();
        $id = $this->getRequest()->getParam('id');
        $calendarId = $qco->deleteConsortiumCalendar($id);
        if($calendarId)
          {
            $this->_redirect('/qco/consortium-calendar');
          }

      }catch(Zend_Exception $e){
        $this->view->save = 'error';
        echo $e->getMessage();
      }
    }

    public function cellCalendarAction()
    {
      try
      {
        $qco = new Application_Model_Qco();
        $consortium = new Application_Model_Mco();
        $this->view->consortium = $consortium->consortium(); 

        $pagination = new Application_Model_Pagination();
        if ( $this->getRequest()->isPost() ) 
        {
          $data = $this->getRequest()->getPost();
          $qcoId = $qco->newCellCalendar($data);
          if($qcoId)
          {
            $this->_redirect('/qco/cell-calendar/save/success');
          }
        }
        $page = $this->getRequest()->getParam('page');
        $qco = $qco->listCellCalendar();
        $this->view->list = $pagination->generatePagination($qco,$page,10);

      }catch(Zend_Exception $e){
        $this->view->save = 'error';
        echo $e->getMessage();
      }
    }

    public function deleteCellCalendarAction()
    {
      try
      {
        $qco = new Application_Model_Qco();
        $id = $this->getRequest()->getParam('id');
        $calendarId = $qco->deleteCellCalendar($id);
        if($calendarId)
          {
            $this->_redirect('/qco/cell-calendar');
          }

      }catch(Zend_Exception $e){
        $this->view->save = 'error';
        echo $e->getMessage();
      }
    }

    public function exportAction()
    {
      $qco = new Application_Model_Qco();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        $exportedData = $qco->exportQcoData();
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=qco.csv');
        $output = fopen('php://output', 'w');
        fputcsv($output, array('LINHA','TIPO_DIA','PC','HORA','TIPO_VIAGEM'),';');
        foreach($exportedData as $data) 
        {
          fputcsv($output, array($data->number_communication,$data->id_type_day,$data->pc,$data->hour,$data->id_type_journey),';');
        }
        exit;
      }
    }

    public function removeRouteAction()
    {
      $qco = new Application_Model_Qco();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($qco->removeRoute($data['routeId'],$data['qcoId']))
        {
          $this->_redirect('/qco/edit/id/'.$data['qcoId'].'/save/success');
        }
        else
        {
          $this->_redirect('/qco/edit/id/'.$data['qcoId'].'/save/error');
        }
      }
    }


}





























