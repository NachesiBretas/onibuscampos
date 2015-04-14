 <?php

class RateCalculationController extends Zend_Controller_Action
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
        // action body
    }

    public function editAction()
    {
        // action body
    }

    public function viewAction()
    {
        // action body
    }

    public function deleteAction()
    {
        // action body
    }

    public function coefficientAction()
    {
        // action body
    }

    public function costAction()
    {
        // action body
    }

    public function fixCostAction()
    {
        // action body
    }

    public function variableCostAction()
    {
        // action body
    }

    public function fuelAction()
    {
      $fuel = new Application_Model_RateCalculation();
      if ( $this->getRequest()->isPost() ){
          $data = $this->getRequest()->getPost();
          $fuelId = $fuel->newRateCalculationFuel($data);
      }
      $this->view->list = $fuel->listRateCalculateFuel();
      $this->view->mainForm = new Application_Form_RateCalculationFuel();
      $this->view->mainForm->populate($this->view->list->toArray());
    }

    public function accessoriesAction()
    {
      $acessories = new Application_Model_RateCalculation();
      if ( $this->getRequest()->isPost() ){
          $data = $this->getRequest()->getPost();
          $acessoriesId = $acessories->newRateCalculationAcessories($data);
      }
      $this->view->list = $acessories->listRateCalculateAcessories();
      $this->view->mainForm = new Application_Form_RateCalculationAcessories();
      $this->view->mainForm->populate($this->view->list->toArray());
    }

    public function treadAction()
    {
        $tread = new Application_Model_RateCalculation();
      if ( $this->getRequest()->isPost() ){
          $data = $this->getRequest()->getPost();
          $treadId = $tread->newRateCalculationTread($data);
      }
      $this->view->list = $tread->listRateCalculateTread();
      $this->view->mainForm = new Application_Form_RateCalculationTread();
      $this->view->mainForm->populate($this->view->list->toArray());
    }

    public function lubricantAction()
    {
      $lubricant = new Application_Model_RateCalculation();
      if ( $this->getRequest()->isPost() ){
          $data = $this->getRequest()->getPost();
          $lubricantId = $lubricant->newRateCalculationLubricant($data);
      }
      $this->view->list = $lubricant->listRateCalculateLubricant();
      $this->view->mainForm = new Application_Form_RateCalculationLubricant();
      $this->view->mainForm->populate($this->view->list->toArray());
    }

    public function kmProductionAction()
    {
      $kmproduction = new Application_Model_RateCalculation();
      if ( $this->getRequest()->isPost() ){
          $data = $this->getRequest()->getPost();
          $kmproductionId = $kmproduction->newRateCalculationKmProduction($data);
      }
      $this->view->list = $kmproduction->listRateCalculateKmProduction();
      $this->view->mainForm = new Application_Form_RateCalculationKmProduction();
      $this->view->mainForm->populate($this->view->list->toArray());
    }

    public function vehicleDepreciationAction()
    {
      $vehicleDepreciation = new Application_Model_RateCalculation();
      if ( $this->getRequest()->isPost() ){
          $data = $this->getRequest()->getPost();
          $vehicleDepreciationId = $vehicleDepreciation->newRateCalculationVehicleDepreciation($data);
      }
      $this->view->list = $vehicleDepreciation->listRateCalculateVehicleDepreciation();
      $this->view->mainForm = new Application_Form_RateCalculationVehicleDepreciation();
      $this->view->mainForm->populate($this->view->list->toArray());
    }

    public function vehicleRemunerationAction()
    {
      $vehicleRemuneration = new Application_Model_RateCalculation();
      if ( $this->getRequest()->isPost() ){
          $data = $this->getRequest()->getPost();
          $vehicleRemunerationId = $vehicleRemuneration->newRateCalculationVehicleRemuneration($data);
      }
      $this->view->list = $vehicleRemuneration->listRateCalculateVehicleRemuneration();
      $this->view->mainForm = new Application_Form_RateCalculationVehicleRemuneration();
      $this->view->mainForm->populate($this->view->list->toArray());
    }

    public function liftRemunerationAction()
    {
      $liftRemuneration = new Application_Model_RateCalculation();
      if ( $this->getRequest()->isPost() ){
          $data = $this->getRequest()->getPost();
          $liftRemunerationId = $liftRemuneration->newRateCalculationLiftRemuneration($data);
      }
      $this->view->list = $liftRemuneration->listRateCalculateLiftRemuneration();
      $this->view->mainForm = new Application_Form_RateCalculationLiftRemuneration();
      $this->view->mainForm->populate($this->view->list->toArray());
    }

    public function equipmentRemunerationAction()
    {
      $equipmentRemuneration = new Application_Model_RateCalculation();
      if ( $this->getRequest()->isPost() ){
          $data = $this->getRequest()->getPost();
          $equipmentRemunerationId = $equipmentRemuneration->newRateCalculationEquipmentRemuneration($data);
      }
      $this->view->list = $equipmentRemuneration->listRateCalculateEquipmentRemuneration();
      $this->view->mainForm = new Application_Form_RateCalculationEquipmentRemuneration();
      $this->view->mainForm->populate($this->view->list->toArray());
    }

    public function warehouseRemunerationAction()
    {
      $warehouseRemuneration = new Application_Model_RateCalculation();
      if ( $this->getRequest()->isPost() ){
          $data = $this->getRequest()->getPost();
          $warehouseRemunerationId = $warehouseRemuneration->newRateCalculationWarehouseRemuneration($data);
      }
      $this->view->list = $warehouseRemuneration->listRateCalculateWarehouseRemuneration();
      $this->view->mainForm = new Application_Form_RateCalculationWarehouseRemuneration();
      $this->view->mainForm->populate($this->view->list->toArray());
    }

    public function eletronicTicketingAction()
    {
      $eletronicTicketing = new Application_Model_RateCalculation();
      if ( $this->getRequest()->isPost() ){
          $data = $this->getRequest()->getPost();
          $eletronicTicketingId = $eletronicTicketing->newRateCalculationEletronicTicketing($data);
      }
      $this->view->list = $eletronicTicketing->listRateCalculateEletronicTicketing();
      $this->view->mainForm = new Application_Form_RateCalculationEletronicTicketing();
      $this->view->mainForm->populate($this->view->list->toArray());
    }

    public function operationCrewAction()
    {
      $operationCrew = new Application_Model_RateCalculation();
      if ( $this->getRequest()->isPost() ){
          $data = $this->getRequest()->getPost();
          $operationCrewId = $operationCrew->newRateCalculationOperationCrew($data);
      }
      $this->view->list = $operationCrew->listRateCalculateOperationCrew();
      $this->view->mainForm = new Application_Form_RateCalculationOperationCrew();
      $this->view->mainForm->populate($this->view->list->toArray());
    }

    public function fixedExpensesAction()
    {
      $fixedExpenses = new Application_Model_RateCalculation();
      if ( $this->getRequest()->isPost() ){
          $data = $this->getRequest()->getPost();
          $fixedExpensesId = $fixedExpenses->newRateCalculationFixedExpenses($data);
      }
      $this->view->list = $fixedExpenses->listRateCalculateFixedExpenses();
      $this->view->mainForm = new Application_Form_RateCalculationFixedExpenses();
      $this->view->mainForm->populate($this->view->list->toArray());
    }

    public function socialChargesAction()
    {
      $socialCharges = new Application_Model_RateCalculation();
      if ( $this->getRequest()->isPost() ){
          $data = $this->getRequest()->getPost();
          $socialChargesId = $socialCharges->newRateCalculationSocialCharges($data);
      }
      $this->view->list = $socialCharges->listRateCalculateSocialCharges();
      $this->view->mainForm = new Application_Form_RateCalculationSocialCharges();
      $this->view->mainForm->populate($this->view->list->toArray());
    }

}









