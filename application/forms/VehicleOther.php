<?php

class Application_Form_VehicleOther extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {
      $this->addElement('select', 'elevator', array(
          'label'         => 'Elevador',
          'class'         => 'form-control',
      		'MultiOptions'			=> array(
                                        0 => 'NÃO',
                                        1 => 'SIM',
      																)
      ));

      $this->addElement('text', 'elevator_date', array(
          'label'         => 'Instalação do elevador',
          'placeholder'   => 'data de instalação do elevador',
          'class'         => 'form-control dateMask'
      ));


      $authNamespace = new Zend_Session_Namespace('userInformation');
      //if($authNamespace->institution == 1)
      //{
        $this->addElement('text', 'seal_roulette', array(
            'label'         => 'Lacre da roleta',
            'placeholder'   => 'lacre da roleta',
            'class'         => 'form-control'
        ));

        $this->addElement('text', 'seal_floor', array(
            'label'         => 'Lacre do assoalho',
            'placeholder'   => 'lacre do assoalho',
            'class'         => 'form-control'
        ));

        $this->addElement('text', 'seal_support', array(
            'label'         => 'Lacre do suporte',
            'placeholder'   => 'lacre do suporte',
            'class'         => 'form-control'
        ));

        $this->addElement('text', 'seal_date', array(
            'label'         => 'Data do lacre',
            'placeholder'   => 'data de instalação do lacre',
            'class'         => 'form-control dateMask'
        ));
      //}

      $this->addElement('text', 'insurer', array(
          'label'         => 'Seguradora',
          'placeholder'   => 'seguradora',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'insurer_date', array(
          'label'         => 'Data do seguro',
          'placeholder'   => 'data do seguro',
          'class'         => 'form-control dateMask'
      ));

      $this->addElement('select', 'eletronic_roulette', array(
          'label'         => 'Validador',
          'class'         => 'form-control',
      		'MultiOptions'			=> array(
                                        0 => 'NÃO',
                                        1 => 'SIM',
      																)
      ));

      $this->addElement('select', 'amount_validator', array(
          'label'         => 'Quantidade de Validadores',
          'class'         => 'form-control',
          'MultiOptions'      => array(
                                        0 => '0',
                                        1 => '1',
                                        2 => '2',
                                      )
      ));

      $this->addElement('select', 'collector_area', array(
          'label'         => 'Área para cobrador',
          'class'         => 'form-control',
      		'MultiOptions'			=> array(
                                        0 => 'NÃO',
                                        1 => 'SIM',
      																)
      ));

      $this->addElement('select', 'air_conditioning', array(
          'label'         => 'Ar condicionado',
          'class'         => 'form-control',
          'MultiOptions'      => array(
                                        0 => 'NÃO',
                                        1 => 'SIM',
                                      )
      ));

      $this->addElement('select', 'gps', array(
          'label'         => 'GPS',
          'class'         => 'form-control',
          'MultiOptions'      => array(
                                        0 => 'NÃO',
                                        1 => 'SIM',
                                      )
      ));

      $this->addElement('select', 'wifi', array(
          'label'         => 'Wifi',
          'class'         => 'form-control',
          'MultiOptions'      => array(
                                        0 => 'NÃO',
                                        1 => 'SIM',
                                      )
      ));

      $this->addElement('select', 'bike_support', array(
          'label'         => 'Suporte de bicicleta',
          'class'         => 'form-control',
          'MultiOptions'      => array(
                                        0 => 'NÃO',
                                        1 => 'SIM',
                                      )
      ));

      $this->addElement('select', 'tv', array(
          'label'         => 'TV',
          'class'         => 'form-control',
          'MultiOptions'      => array(
                                        0 => '0',
                                        1 => '1',
                                        2 => '2',
                                        3 => '3',
                                      )
      ));

      $this->addElement('select', 'camera', array(
          'label'         => 'Camera',
          'class'         => 'form-control',
          'MultiOptions'      => array(
                                        0 => '0',
                                        1 => '1',
                                        2 => '2',
                                        3 => '3',
                                        4 => '4',
                                        5 => '5',
                                        6 => '6',
                                        7 => '7',
                                        8 => '8',
                                        9 => '9',
                                        10 => '10',
                                      )
      ));

      $this->addElement('submit', 'submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Próximo',
        'class'           => 'col-lg-offset-5'
      ));

    }


}

