<?php

class Application_Form_Qco extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {

       $this->addElement('text', 'name', array(
          'label'         => 'Nome',
          'placeholder'   => 'nome da linha',
          'class'         => 'form-control',
          'required'      => true
      ));
       
       $this->addElement('text', 'number_communication', array(
          'label'         => 'Número de comunicação',
          'placeholder'   => 'número de comunicação',
          'class'         => 'form-control',
          'required'      => true,
          'maxlength'     => 6,
          'disabled'      => 'disabled'
      ));

      $consortium = array( 0 => '-- Selecione um grupo consorcio --');
      $consorcio = new Application_Model_DbTable_Consortium();
      $consortiumAll = ($consorcio->select()->order("id"));
      foreach ($consorcio->fetchAll($consortiumAll) as $c) {
          $consortiumArray[$c->id] = $c->name;
      }
      $this->addElement('select','consortium', array(
          'label'             => 'Consórcio',
          'class'             => 'form-control consortium',
          'MultiOptions'      => $consortiumArray,
          'required'          => true
      ));

      $this->addElement('select','consortium_companies', array(
          'label'             => 'Célula Operacional',
          'class'             => 'form-control consortium_company',
          'required'          => true
      ));

      $vehiclePatternArray = array( 0 => '-- Selecione um grupo tarifário --');
      $table = new Application_Model_DbTable_FinanceFare();
      $select = ($table->select()->order("value"));
      foreach ($table->fetchAll($select) as $c) {
          $financeFareArray[$c->id] = $c->name .' - R$ '. number_format($c->value, 2, ',', '.');;
      }
      $this->addElement('select','finance_fare_id', array(
          'label'             => 'Tarifa',
          'class'             => 'form-control',
          'MultiOptions'      => $financeFareArray
      ) );

      $vehiclePatternArray = array( 0 => '-- Não possui tarifa de integração --');
      $table = new Application_Model_DbTable_FinanceFare();
      $select = ($table->select()->order("value"));
      foreach ($table->fetchAll($select) as $c) {
          $integrationFinanceFareArray[$c->id] = $c->name .' - R$ '. number_format($c->value, 2, ',', '.');;
      }
      $this->addElement('select','integrated_finance_fare_id', array(
          'label'             => 'Tarifa de Integração',
          'class'             => 'form-control',
          'MultiOptions'      => $integrationFinanceFareArray
      ) );
       
      $this->addElement('text', 'start_validity_date', array(
          'label'         => 'Data de vigência',
          'placeholder'   => 'Data de vigência',
          'class'         => 'form-control dateMask',
          'required'      => true
      ));

       $this->addElement('textarea', 'historic', array(
          'label'         => 'Histórico',
          'placeholder'   => 'histórico',
          'class'         => 'form-control',
          'required'      => true,
      ));

      $this->addElement('submit', 'submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Salvar',
        'class'           => 'col-lg-offset-5'
      ));
    }


}

