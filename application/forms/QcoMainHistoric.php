<?php

class Application_Form_QcoMainHistoric extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {

       $this->addElement('textarea', 'name', array(
          'label'         => 'Nome',
          'placeholder'   => 'nome da linha',
          'class'         => 'form-control',
          'disabled'          => 'disabled',
          'required'      => true,
          'cols'          => 5,
          'rows'          => 2
      ));
       
       $this->addElement('text', 'number_communication', array(
          'label'         => 'Número de comunicação',
          'placeholder'   => 'número de comunicação',
          'class'         => 'form-control',
          'disabled'          => 'disabled',
          'required'      => true,
          'maxlength'     => 6,
          'disabled'      => 'disabled'
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
          'disabled'          => 'disabled',
          'MultiOptions'      => $financeFareArray
      ) );
       
      $this->addElement('text', 'start_validity_date', array(
          'label'         => 'Data de vigência',
          'placeholder'   => 'Data de vigência',
          'class'         => 'form-control dateMask',
          'disabled'          => 'disabled',
          'required'      => true
      ));

       $this->addElement('textarea', 'historic', array(
          'label'         => 'Histórico',
          'placeholder'   => 'histórico',
          'class'         => 'form-control',
          'disabled'          => 'disabled',
          'required'      => true,
      ));

    }


}

