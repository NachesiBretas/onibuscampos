<?php

class Application_Form_VehicleHistoric extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {
      $authNamespace = new Zend_Session_Namespace('userInformation');

      $this->addElement('hidden','consortium_companies_hidden');
      $this->addElement('hidden','id');
      $this->addElement('hidden','vehicle_id');

      $this->addElement('text', 'external_number', array(
          'label'         => 'Número externo',
          'placeholder'   => 'número externo do veículo',
          'class'         => 'form-control',
          'maxlength'			=> '8'
      ));

      if($authNamespace->institution == 1)
      {
        
        $vehicleConsortiumArray = array( 0 => '-- Selecione o consórcio --');
        $table = new Application_Model_DbTable_Consortium();
        foreach ($table->fetchAll() as $c) {
            $vehicleConsortiumArray[$c->id] = $c->name;
        }
        $this->addElement('select','consortium', array(
            'label'             => 'Consórcio',
            'class'             => 'form-control consortium',
            'MultiOptions'      => $vehicleConsortiumArray
        ) );

        $vehicleConsortiumCellArray = array( 0 => '-- Selecione um consórcio --');
        $this->addElement('select','consortium_company', array(
            'label'             => 'Célula Operacional',
            'class'             => 'form-control consortium_company',
            'MultiOptions'      => $vehicleConsortiumCellArray
        ) );

        // $this->addElement('text', 'authorization', array(
        //     'label'         => 'Documento de autorização',
        //     'placeholder'   => 'documento de autorização',
        //     'class'         => 'form-control'
        // ));

        $this->addElement('text', 'start_historic_date', array(
            'label'         => 'Data de início',
            'placeholder'   => 'data do início do veículo',
            'class'         => 'form-control dateMask'
        ));      

        $this->addElement('text', 'end_historic_date', array(
            'label'         => 'Data de baixa',
            'placeholder'   => 'data de baixa do veículo',
            'class'         => 'form-control dateMask'
        ));
      }

      $this->addElement('submit', 'submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Próximo',
        'class'           => 'col-lg-offset-5'
      ));
    }


}

