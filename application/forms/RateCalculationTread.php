<?php 

class Application_Form_RateCalculationTread extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {
      $vehiclePatternArray = array( 0 => '-- Selecione um padrão de veículo --');
      $table = new Application_Model_DbTable_VehiclePattern();
      $select = ($table->select()->order("name ASC"));
      foreach ($table->fetchAll($select) as $c) {
          $vehiclePatternArray[$c->id] = $c->name;
      }
      $this->addElement('select','vehicle_pattern_id', array(
      		'label'							=> 'Padrão do veículo',
          'class'							=> 'form-control',
      		'MultiOptions'			=> $vehiclePatternArray
  		));

      $this->addElement('select','tread_item', array(
          'label'         => 'Ítem de Rodagem',
          'class'         => 'form-control',
          'MultiOptions'  => array(
                                        'Select' => '-- Selecione um ítem de rodagem --',
                                        'Pneu' => 'Pneu', 
                                        'Câmara' => 'Câmara',
                                        'Protetor' => 'Protetor',
                                        'Recapagem' => 'Recapagem'
                                      )
      ));

      $this->addElement('text','coefficient', array(
          'label'         => 'Coeficiente',
          'placeholder'   => 'Coeficiente',
          'class'         => 'form-control'
      ));

      $this->addElement('submit','submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Salvar',
        'class'           => 'col-lg-offset-5'
      ));
    }


}