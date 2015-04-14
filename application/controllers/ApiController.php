<?php

class ApiController extends Zend_Controller_Action
{

    public function init()
    {
        $this->_helper->layout()->setLayout('ajax');
    }

    public function indexAction()
    {
        // action body
    }

    public function consortiumCompaniesAction()
    {
      $consortiumId = $this->getRequest()->getParam('id');
      $consortiumCompanies = new Application_Model_DbTable_ConsortiumCompanies();
      $companies = $consortiumCompanies->fetchAll($consortiumCompanies->select()->where('consortium_id = ?',$consortiumId));
      echo json_encode($companies->toArray());
    }

    public function returnUserAction()
    {
      $user= new Application_Model_User(); 
      header("Content-Type: application/json");
      $aux= $user->findAjaxByName($_GET["query"]);
      echo Zend_Json::encode($aux);
    }

    public function returnAccidentsAction()
    {
      $startDate = $this->getRequest()->getParam('startDate');
      $endDate = $this->getRequest()->getParam('endDate');
      $accident = new Application_Model_Accident();
      header("Content-Type: application/json");
      if($startDate == '' && $endDate == '')
      {
        $results = $accident->returnAllAccidents();
      }
      else
      {
        $results = $accident->returnCityQuantById($startDate,$endDate);
      }
      echo Zend_Json::encode($results);
    }

    public function returnAccidentsCityAction()
    {
      $startDate = $this->getRequest()->getParam('startDate');
      $endDate = $this->getRequest()->getParam('endDate');
      $startDate = Application_Model_General::dateToUs($startDate);
      $endDate = Application_Model_General::dateToUs($endDate);
      $accident = new Application_Model_Accident();
      header("Content-Type: application/json");
      if($startDate == '' && $endDate == '')
      {
        $results = $accident->returnAllAccidentsCity();
      }
      else
      {
        $results = $accident->returnCityQuantById($startDate,$endDate);
      }
      echo Zend_Json::encode($results);
    }

    public function consortiumCompaniesNameAction()
    {
      $consortiumId = $this->getRequest()->getParam('id');
      $consortiumCompanies = new Application_Model_DbTable_ConsortiumCompanies();
      $select = $consortiumCompanies->select()->setIntegrityCheck(false);
      $select ->from(array('cc' => 'consortium_companies'),array('id') )
              ->joinInner(array('c' => 'company'),'c.id=cc.company', array('name' => 'company'))
              ->where('cc.consortium_id = ?',$consortiumId);
      $companies = $consortiumCompanies->fetchAll($select);
      echo json_encode($companies->toArray());
    }


}











