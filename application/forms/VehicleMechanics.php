<?php

class Application_Form_VehicleMechanics extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {
      $this->addElement('text', 'chassi_number', array(
          'label'         => 'Número do chassi',
          'placeholder'   => 'número do chassi',
          'class'         => 'form-control',
          'required'      => 'required',
          'maxlength'     => '17'
      ));

      $vehicleChassiArray = array( 0 => '-- Selecione um tipo de chassi --');
      $table = new Application_Model_DbTable_VehicleChassi();
      $select = ($table->select()->order("name ASC"));
      foreach ($table->fetchAll($select) as $c) {
          $vehicleChassiArray[$c->id] = $c->name;
      }
  		$this->addElement('select','chassi_model', array(
      		'label'							=> 'Modelo do chassi',
          'class'							=> 'form-control',
      		'MultiOptions'			=> $vehicleChassiArray
  		) );

      $this->addElement('text', 'chassi_year', array(
          'label'         => 'Ano do chassi',
          'placeholder'   => 'ano do chassi',
          'class'         => 'form-control',
          'maxlength'			=> 4
      ));

      $vehicleBodyArray = array( 0 => '-- Selecione um tipo de carroceria --');
      $table = new Application_Model_DbTable_VehicleBody();
      $select = ($table->select()->order("name ASC"));
      foreach ($table->fetchAll($select) as $c) {
          $vehicleBodyArray[$c->id] = $c->name;
      }
  		$this->addElement('select','body_model', array(
      		'label'							=> 'Modelo da carroceria',
          'class'							=> 'form-control',
      		'MultiOptions'			=> $vehicleBodyArray
  		) );

      $this->addElement('text', 'body_year', array(
          'label'         => 'Ano da carroceria',
          'placeholder'   => 'ano da carroceria',
          'class'         => 'form-control',
          'maxlength'			=> 4
      ));

      $vehicleSuspensionArray = array( 	0 => '-- Selecione um tipo de suspensão --',
      														1	=> 'MOLAS',
      														2 => 'AR',
      														3 => 'MISTA',
      														4 => 'PNEUMÁTICA',
                                  5 => 'AUTOMÁTICA');
  		$this->addElement('select','suspension', array(
      		'label'							=> 'Suspensão',
          'class'							=> 'form-control',
      		'MultiOptions'			=> $vehicleSuspensionArray
  		) );

      $vehicleCambiumArray = array( 	0 => '-- Selecione um tipo de câmbio --',
		      														1	=> 'INDEFINIDO',
		      														2 => 'AUTOMÁTICO',
		      														3 => 'MANUAL');
  		$this->addElement('select','cambium', array(
      		'label'							=> 'Câmbio',
          'class'							=> 'form-control',
      		'MultiOptions'			=> $vehicleCambiumArray
  		) );

      $vehicleMotorLocalizationArray = array( 	0 => '-- Selecione a localização do motor --',
							      														1	=> 'CENTRAL',
							      														2 => 'DIANTEIRO',
							      														3 => 'TRASEIRO',
							      														4 => 'ENTRE EIXOS');
  		$this->addElement('select','motor_localization', array(
      		'label'							=> 'Localização do motor',
          'class'							=> 'form-control',
      		'MultiOptions'			=> $vehicleMotorLocalizationArray
  		) );

      $this->addElement('text', 'motor_power', array(
          'label'         => 'Potência do motor',
          'placeholder'   => 'potência do motor (em cavalos)',
          'class'         => 'form-control',
          'maxlength'			=> 5
      ));

     $vehicleSeatArray = array( 	0 => '-- Selecione a localização do motor --',
							      														1	=> 'PLASTICO',
							      														2 => 'ESTOFADO',
							      														3 => 'FIBRA',
							      														4 => 'POLTRONA',
                                                5 => 'PLASTICO/ESTOFADO');
  		$this->addElement('select','seat_type', array(
      		'label'							=> 'Tipo de assento',
          'class'							=> 'form-control',
      		'MultiOptions'			=> $vehicleSeatArray
  		) );

      $this->addElement('submit', 'submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Próximo',
        'class'           => 'col-lg-offset-5'
      ));
    }


}

