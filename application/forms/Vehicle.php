<?php

class Application_Form_Vehicle extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {

      $authNamespace = new Zend_Session_Namespace('userInformation');
      if($authNamespace->institution == 1)
      {
        $this->addElement('text', 'start_date', array(
            'label'         => 'Data de entrada',
            'placeholder'   => 'data de entrada no sistema',
            'class'         => 'form-control dateMask',
            'required'      => true
        ));
      }

    	$this->addElement('select','service', array(
      		'label'							=> 'Serviço',
          'class'							=> 'form-control',
      		'MultiOptions'			=> array(
      																	0 => '-- Selecione um tipo de serviço --',
                                        1 => 'COMERCIAL',
                                        2 => 'COMERCIAL EXECUTIVO',
                                        3 => 'CONVENCIONAL',
                                        4 => 'CONVENCIONAL EXECUTIVO'
      																)
  		) );

      $this->addElement('text', 'plate', array(
      		'label'					=> 'Placa',
          'placeholder'   => 'placa do veículo',
          'class'					=> 'form-control'
      ));

      $this->addElement('text', 'external_number', array(
          'label'         => 'Número de ordem',
          'placeholder'   => 'número de ordem',
          'class'         => 'form-control',
          'disabled'      => 'disabled'
      ));

      $this->addElement('text', 'validator_id', array(
          'label'         => 'Validador principal',
          'placeholder'   => 'validador principal',
          'class'         => 'form-control',
          'disabled'      => 'disabled'
      ));

      $this->addElement('text', 'renavam', array(
      		'label'					=> 'Renavam',
          'placeholder'   => 'número do renavam',
          'class'					=> 'form-control'
      ));

      $vehiclePatternArray = array( 0 => '-- Selecione um padrão --');
      $table = new Application_Model_DbTable_VehiclePattern();
      $select = ($table->select()->order("name ASC"));
      foreach ($table->fetchAll($select) as $c) {
          $vehiclePatternArray[$c->id] = $c->name;
      }
      $this->addElement('select','pattern', array(
      		'label'							=> 'Padrão',
          'class'							=> 'form-control',
      		'MultiOptions'			=> $vehiclePatternArray
  		) );

      $vehicleColorArray = array( 0 => '-- Selecione uma cor --');
      $table = new Application_Model_DbTable_VehicleColor();
      $select = ($table->select()->order("name ASC"));
      foreach ($table->fetchAll($select) as $c) {
          $vehicleColorArray[$c->id] = $c->name;
      }
  		$this->addElement('select','color', array(
      		'label'							=> 'Cor',
          'class'							=> 'form-control',
      		'MultiOptions'			=> $vehicleColorArray
  		) );

      $vehicleTypeArray = array( 0 => '-- Selecione um tipo de veículo --');
      $table = new Application_Model_DbTable_VehicleType();
      $select = ($table->select()->order("name ASC"));
      foreach ($table->fetchAll($select) as $c) {
          $vehicleTypeArray[$c->id] = $c->name;
      }
  		$this->addElement('select','type', array(
      		'label'							=> 'Tipo do Veículo',
          'class'							=> 'form-control',
      		'MultiOptions'			=> $vehicleTypeArray
  		) );

		// $this->addElement('select','floor', array(
    //   		'label'							=> 'Tipo de Piso',
    //       'class'							=> 'form-control',
    //   		'MultiOptions'			=> array(
    //   																	0 => '-- Selecione um tipo de piso --',
    //                                     1 => 'NORMAL'
    //   																)
		// ) );

      $this->addElement('submit', 'submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Próximo',
        'class'           => 'col-lg-offset-5'
      ));
    }


}

