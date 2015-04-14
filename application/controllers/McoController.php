<?php

class McoController extends Zend_Controller_Action
{

    public function init()
    {
      $authNamespace = new Zend_Session_Namespace('userInformation');
      $institution = $authNamespace->institution;
      if($institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $this->_helper->layout()->setLayout('dashboard');      
      $id = $this->getRequest()->getParam('id');
      $month = $this->getRequest()->getParam('month');
      if($id)
      {
        $this->view->mcoId = $id;
      }
      if($month)
      {
        $this->view->month = $month;
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
          $mco = new Application_Model_Mco();
          $mcoId = $mco->loadFile($_FILES);
          if($mcoId)
          {
            $this->view->save = "success";
          }
          else
          {
            $this->view->save = "error";
          }
        }catch(Zend_Exception $e){
           $this->view->save = true;
        }
      }
    }

    public function editAction()
    {
        // action body
    }

    public function viewAction()
    {
      $mco = new Application_Model_Mco();
      $pagination = new Application_Model_Pagination();
      $page = $this->getRequest()->getParam('page');
      $field = $this->getRequest()->getParam('field');
      if($page == '') $page = 1;
      if($field != ""){      
          $mcos = $mco->returnByDate($field);
          $this->view->field = $field;
          $this->view->list = $pagination->generatePagination($mcos,$page,10);
        }
      else{
        $mco = $mco->lists();
        $this->view->list = $pagination->generatePagination($mco,$page,10);
      }
    }

    public function reportAction()
    {
      $consortium = new Application_Model_Mco();
      $this->view->list = $consortium->consortium(); 
     // $this->view->consortiumCompanies = new Application_Model_DbTable_ConsortiumCompanies();
    }

    public function visAction()
    {
        // action body
    }

    public function analyticsAction()
    {
      $mco = new Application_Model_Mco();
      $pagination = new Application_Model_Pagination();
      $page = $this->getRequest()->getParam('page');
      if($page == '') $page = 1;
      $mco = $mco->returnMonth($this->view->month);
      $this->view->list = $pagination->generatePagination($mco,$page,10);
    }

    public function analyticsResultAction()
    {
      try{
        $pagination = new Application_Model_Pagination();
        $mcoData = new Application_Model_McoData();
        $this->view->save = $this->getRequest()->getParam('save');
        $page = $this->getRequest()->getParam('page');
        $field = $this->getRequest()->getParam('field');
        $option = $this->getRequest()->getParam('option');
        $id = $this->getRequest()->getParam('id');
        if($page == '') $page = 1;
        if($field != ""){
            if($option == 1){
              $data = $mcoData->returnJourneysByLine($id,urldecode($field));
            }
            if($option == 2){
              $data = $mcoData->returnJourneysByVehicle($id,urldecode($field));
            }
        }
        else{
          $data = $mcoData->returnJourneys($id);
        }
        $this->view->field = $field;
        $this->view->option = $option;
        $this->view->list = $pagination->generatePagination($data,$page,10);
      }
      catch(Zend_Exception $e){
        echo $e->getMessage();
        $this->view->error = true;
      }
    }

    public function analyticsDiffAction()
    {
      try{
        $pagination = new Application_Model_Pagination();
        $mcoData = new Application_Model_McoData();
        $this->view->save = $this->getRequest()->getParam('save');
        $page = $this->getRequest()->getParam('page');
        $field = $this->getRequest()->getParam('field');
        $option = $this->getRequest()->getParam('option');
        $id = $this->getRequest()->getParam('id');
        if($page == '') $page = 1;
        if($field != ""){  
            if($option == 1){
              $data = $mcoData->returnByLine($field,$id);
            }
            if($option == 2){
              $data = $mcoData->returnDiffsByVehicle($id,urldecode($field));
            }     
        }
        else{
          $data = $mcoData->returnDiffs($id);
        }
        $this->view->field = $field;
        $this->view->option = $option;
        $this->view->list = $pagination->generatePagination($data,$page,10);
      }
      catch(Zend_Exception $e){
        echo $e->getMessage();
        $this->view->error = true;
      }
    }

    public function analyticsAdjustmentsAction()
    {
      $pagination = new Application_Model_Pagination();
      $page = $this->getRequest()->getParam('page');
      if($page == '') $page = 1;
      $id = $this->getRequest()->getParam('id');
      $mcoData = new Application_Model_McoData();
      $data = $mcoData->returnAdjustments($id);
      $this->view->list = $pagination->generatePagination($data,$page,10);
    }

    public function analyticsFinanceAction()
    {
      $pagination = new Application_Model_Pagination();
      $mcoData = new Application_Model_McoData();
      $page = $this->getRequest()->getParam('page');
      if($page == '') $page = 1;
      $id = $this->getRequest()->getParam('id');
      $field = $this->getRequest()->getParam('field');
      if($field != ""){
        $data = $mcoData->returnFinanceByLine($id,urldecode($field));
      }
      else{
        $data = $mcoData->returnFinance($id);
      }
      $this->view->field = $field;
      $this->view->list = $pagination->generatePagination($data,$page,10);
    }

    public function analyticsByDayAction()
    {
      
    }

    public function analyticsFinanceMonthAction()
    {
      ini_set('memory_limit', '-1');
      ini_set('max_execution_time', 700);
      $mcoFinance = new Application_Model_McoFinance();
      $this->view->finance = $mcoFinance->calculateMonth($this->view->month);
    }

    public function accreditPassengerAction()
    {
      $id = $this->getRequest()->getParam('id');
      $mcoId = $this->getRequest()->getParam('mco_id');
      $mcoData = new Application_Model_McoData();

      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($mcoData->editAccreditPassenger($data,$id))
        {
           $this->_redirect('/mco/analytics-diff/id/'.$mcoId.'/save/success');
        }
        else
        {
          $this->_redirect('/mco/analytics-diff/id/'.$mcoId.'/save/error');
        }
      }

      $this->view->list = $mcoData->returnPassengerDiff($id);
    }

    public function editLostLogAction()
    {
      $id = $this->getRequest()->getParam('id');
      $mcoId = $this->getRequest()->getParam('mco_id');
      $mcoData = new Application_Model_McoData();

      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($mcoData->editLostLog($data,$id))
        {         
            $this->_redirect('/mco/analytics-result/id/'.$mcoId.'/save/success');
        }
        else
        {    
            $this->_redirect('/mco/analytics-result/id/'.$mcoId.'/save/success');
        }
      }

      $this->view->list = $mcoData->returnJourneysId($id);
    }

    public function newLostLogAction()
    {
      try{
        $mcoData = new Application_Model_McoData();
        $mcoId = $this->getRequest()->getParam('id');
        $vehicle_number = $this->getRequest()->getParam('vehicle_number');
        $craft = $this->getRequest()->getParam('craft');
        $mco = $mcoData->mcoDate($mcoId);
        $this->view->date = $mco->date_operation;
        if ( $this->getRequest()->isPost() ) 
        {
          $data = $this->getRequest()->getPost();
          $mcoId = $mcoData->newLostLog($vehicle_number,$craft,$data,$mcoId);
          if($mcoId){
            $this->view->save = 'success';
          }
          else{
            $this->view->save = 'error';
          }
        }
      }catch(Zend_Exception $e){
        echo $e->getMessage();
        $this->view->error = true;
      }
    }

    public function deleteLostLogAction()
    {
      try{
        $mcoId = $this->getRequest()->getParam('mco_id');
        $mcoDataId = $this->getRequest()->getParam('id');
        if ( $mcoDataId ) 
        {
          $mcoData = new Application_Model_McoData(); 
          if($mcoData->deleteLostLog($mcoDataId)){
            $this->_redirect('/mco/analytics-result/id/'.$mcoId.'/save/success');
          }
          else{
            $this->_redirect('/mco/analytics-result/id/'.$mcoId.'/save/error');
          }
        }
      }catch(Zend_Exception $e){
        echo $e->getMessage();
        $this->view->error = true;
      }
    }

  public function reportRevenueAction(){
     try{
      if ($_GET["report"] == 0)
      {
        $this->_helper->layout()->setLayout('report');
        $this->view->startDate = $this->getRequest()->getParam('data_ini');
        $this->view->endDate = $this->getRequest()->getParam('data_fim');
        $mco = new Application_Model_Mco();
        $this->view->listFares = $mco->returnCountFares($this->view->startDate,$this->view->endDate);
        $this->view->fareRevenue = $mco->calculateRevenue($this->view->listFares);
        $this->view->groupFares = $mco->returnGroupFares($this->view->startDate,$this->view->endDate);
      }
      else{
        $inicio = $_GET["data_ini"];
        $fim = $_GET["data_fim"];
        $csv = new Application_Model_Csv();
        $cash = new Application_Model_Mco();
        $diff = $cash->reportCashPassengerDiff($inicio, $fim);
        $total_passenger = $cash->reportCashTotalPassenger($diff->total_diff, $inicio, $fim);
        $total = $cash->reportCashSystemTotal($inicio, $fim);
        $system = $csv->createCsvSystem($inicio, $fim, $total_passenger->total_passenger,$total->total_revenue,$total->total_cbtu_transfer,$total->total_liquid_revenue);
      }
    }catch(Zend_Exception $e) {
        echo $e->getMessage();
    }
  }

  public function reportOvercrowdedAction(){
     try{
      if ($_GET["report"] == 0){
        $start_date = $_GET["data_ini"];
        $end_date = $_GET["data_fim"];
        $this->_helper->layout()->setLayout('report');
        $aux[0] = $start_date;  
        $aux[1] = $end_date;
        $this->view->date = $aux;
        $mco = new Application_Model_Mco();
        $this->view->report = $mco->reportOvercrowded($start_date,$end_date);
      }
    }catch(Zend_Exception $e) {
        echo $e->getMessage();
    }
  }

    public function reportCellRevenueAction()
    {   
      try{
      if ($_GET["report"] == 0){
        $inicio = $_GET["data_ini"];
        $fim = $_GET["data_fim"];
        $consortium_name = $_GET["consotiumName"];
        $consortium = $_GET["consotiumOption"];
        $cell_name = $_GET["cellOptionName"];
        $cell =$_GET["cellOption"]; 
        $aux[0] = $inicio;  
        $aux[1] = $fim;  
        $aux[2] = $cell_name;  
        $aux[3] = $consortium_name;
        $this->_helper->layout()->setLayout('report');
        $cash = new Application_Model_Mco();
        $diff = $cash->reportCashCellPassengerDiff($inicio, $fim, $cell);
        $total_passenger = $cash->reportCashCellTotalPassenger($diff->total_diff, $inicio, $fim, $cell);
        $total = $cash->reportCashCellTotal($inicio,$fim, $cell);
        $type = $cash->reportCashCellType($cell, $inicio, $fim, $total_passenger->total_passenger,$total->total_revenue,$total->total_cbtu_transfer,$total->total_liquid_revenue);
        $value = $cash->reportCashCellValue($cell, $inicio, $fim, $total_passenger->total_passenger,$total->total_revenue,$total->total_cbtu_transfer,$total->total_liquid_revenue);
        $this->view->list = $total;
        $this->view->list2 = $type;
        $this->view->list3 = $value;
        $this->view->list4 = $total_passenger;
        $this->view->list5 = $aux; 
      }else{
        $inicio = $_GET["data_ini"];
        $fim = $_GET["data_fim"];
        $consortium_name = $_GET["consotiumName"];
        $consortium = $_GET["consotiumOption"];
        $cell_name = $_GET["cellOptionName"];
        $cell =$_GET["cellOption"];
        $aux[0] = $inicio;  
        $aux[1] = $fim;  
        $aux[2] = $cell_name;  
        $aux[3] = $consortium_name;
        $csv = new Application_Model_Csv();
        $cash = new Application_Model_Mco();
        $diff = $cash->reportCashCellPassengerDiff($inicio, $fim, $cell);
        $total_passenger = $cash->reportCashCellTotalPassenger($diff->total_diff, $inicio, $fim, $cell);
        $total = $cash->reportCashCellTotal($inicio,$fim, $cell);
        $system = $csv->createCsvCell($aux, $cell, $inicio, $fim, $total_passenger->total_passenger,$total->total_revenue,$total->total_cbtu_transfer,$total->total_liquid_revenue);
      }
    }catch(Zend_Exception $e) {
        echo $e->getMessage();
    }
    }

    public function deleteByDayAction()
    {
      try{
        $month = $this->getRequest()->getParam('month');
        $mcoId = $this->getRequest()->getParam('id');
        if ($mcoId) 
        {
          $mcoData = new Application_Model_McoData(); 
          if($mcoData->deleteDay($mcoId)){
            $this->_redirect('/mco/analytics/month/'.$month.'/save/success');
          }
          else{
            $this->_redirect('/mco/analytics/month/'.$month.'/save/error');
          }
        }
      }catch(Zend_Exception $e){
        echo $e->getMessage();
        $this->view->error = true;
      }
    }

    public function lockDayAction()
    {
      try{
        $month = $this->getRequest()->getParam('month');
        $mcoId = $this->getRequest()->getParam('id');
        if ($mcoId) 
        {
          $mcoData = new Application_Model_McoData(); 
          if($mcoData->lockDay($mcoId)){
            $this->_redirect('/mco/analytics/month/'.$month.'/save/success');
          }
          else{
            $this->_redirect('/mco/analytics/month/'.$month.'/save/error');
          }
        }
      }catch(Zend_Exception $e){
        echo $e->getMessage();
        $this->view->error = true;
      }
      }

    public function reportTravelAction(){
      try{
        if ($_GET["report"] == 0){
          $start_date = $_GET["data_ini"];
          $end_date = $_GET["data_fim"];
          $this->_helper->layout()->setLayout('report');
          $aux[0] = $start_date;  
          $aux[1] = $end_date;
          $this->view->date = $aux;
          $mco = new Application_Model_Mco();
          $this->view->report = $mco->reportTravel($start_date,$end_date);
        }
      }catch(Zend_Exception $e) {
          echo $e->getMessage();
      }
    }

    public function reportHourProductionAction(){
      try{
        if ($_GET["report"] == 0){
          $start_date = $_GET["data_ini"];
          $end_date = $_GET["data_fim"];
          $this->_helper->layout()->setLayout('report');
          $aux[0] = $start_date;  
          $aux[1] = $end_date;
          $this->view->date = $aux;
          $mco = new Application_Model_Mco();
          $hour1='00:00';
          $hour2='00:59';
          for ($i=0; $i < 24; $i++) { 
            $totals[] = $mco->reportHourProductionTotal($start_date,$end_date,$hour1,$hour2);
            $hour1= Application_Model_General::convertToHour(Application_Model_General::convertToMinute($hour1) + 60).':00';
            $hour2= Application_Model_General::convertToHour(Application_Model_General::convertToMinute($hour1)).':59';
          }
          $this->view->totals = $totals;
        }
      }catch(Zend_Exception $e) {
          echo $e->getMessage();
      }
    }

    public function mainNewLostLogAction()
    {
      try{
        $mcoData = new Application_Model_McoData();
        $mcoId = $this->getRequest()->getParam('id');
        $mco = $mcoData->mcoDate($mcoId);
        $this->view->date = $mco->date_operation;
        if ($this->getRequest()->isPost()) 
        {
          $mcoId = $this->getRequest()->getParam('id');
          $vehicle_number = $this->getRequest()->getParam('vehicle_number');
          $craft = $this->getRequest()->getParam('craft');
          $this->_redirect('/mco/new-lost-log/id/'.$mcoId.'/vehicle_number/'.$vehicle_number.'/craft/'.$craft);
        }
      }catch(Zend_Exception $e){
        echo $e->getMessage();
        $this->view->error = true;
      }
    }

}





























