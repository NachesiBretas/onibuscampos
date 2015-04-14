<?php

class Application_Form_LostLog extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {

      $authNamespace = new Zend_Session_Namespace('userInformation');

      $this->addElement('text', 'line', array(
      		'label'					=> 'Linha',
          'placeholder'   => 'Linha',
          'class'					=> 'form-control'
      ));

      $this->addElement('text', 'craft', array(
          'label'         => 'Nº do ofício',
          'placeholder'   => 'Nº do ofício',
          'class'         => 'form-control'
      ));

      $this->addElement('select','type', array(
          'label'             => 'Tipo',
          'class'             => 'form-control',
          'MultiOptions'      => array(
                                        0 => '-- Selecione um tipo de viagem --',
                                        1 => 'PD',
                                        2 => 'PI',
                                        3 => 'RI',
                                        4 => 'NT'
                                      )
      ) );

      $this->addElement('text', 'vehicle_number', array(
      		'label'					=> 'Veículo',
          'placeholder'   => 'número do veículo',
          'class'					=> 'form-control'
      ));

      $this->addElement('text', 'start_roulette', array(
          'label'         => 'Roleta inicial',
          'placeholder'   => 'número da roleta inicial',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'mid_roulette', array(
          'label'         => 'Roleta retorno',
          'placeholder'   => 'número da roleta de retorno',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'end_roulette', array(
          'label'         => 'Roleta final',
          'placeholder'   => 'número da roleta final',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'start_hour', array(
          'label'         => 'Hora inicial',
          'placeholder'   => 'hora inicial',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'mid_hour', array(
          'label'         => 'Hora retorno',
          'placeholder'   => 'hora de retorno',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'end_hour', array(
          'label'         => 'Hora final',
          'placeholder'   => 'hora final',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'start_date', array(
          'label'         => 'Data inicial',
          'placeholder'   => 'data inicial da viagem',
          'class'         => 'form-control dateMask'
      ));

      $this->addElement('text', 'mid_date', array(
          'label'         => 'Data retorno',
          'placeholder'   => 'data retorno da viagem',
          'class'         => 'form-control dateMask'
      ));

      $this->addElement('text', 'end_date', array(
          'label'         => 'Data final',
          'placeholder'   => 'data final da viagem',
          'class'         => 'form-control dateMask'
      ));

      $this->addElement('submit', 'submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Salvar',
        'class'           => 'col-lg-offset-5'
      ));
    }


}

