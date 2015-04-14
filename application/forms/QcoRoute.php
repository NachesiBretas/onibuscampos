<?php

class Application_Form_QcoRoute extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {

      $this->addElement('hidden','id');

      $this->addElement('textarea', 'name_route', array(
          'label'         => 'Nome',
          'placeholder'   => 'nome do itinerário',
          'class'         => 'form-control',
          'required'      => true,
          'cols'          => 5,
          'rows'          => 2
      ));

      $this->addElement('select','pc', array(
          'label'             => 'PC',
          'class'             => 'form-control',
          'MultiOptions'      => array(
          						1 => 'PC 1',
          						2 => 'PC 2'
          	)
      ) );

      $this->addElement('textarea', 'pc_location', array(
          'label'         => 'Localização do PC',
          'class'         => 'form-control',
          'required'      => true,
          'placeholder'   => 'endereço do pc',
          'cols'          => 5,
          'rows'          => 2
      ));

      $this->addElement('select','type_journey', array(
          'label'             => 'Tipo de viagem',
          'class'             => 'form-control',
          'MultiOptions'      => array(
          						0 => '-- Selecione um tipo de viagem --',
          						1 => 'PADRÃO',
          						2 => 'REC PONTO INTERMEDIÁRIO',
          						3 => 'PART PONTO INTERMEDIÁRIO',
          						4 => 'NOTURNO',
          						5 => 'OUTROS',
          						6 => 'ALTERNATIVOS'
          	)
      ) );

      $this->addElement('text', 'ext_asphalt', array(
          'label'         => 'Extensão em asfalto',
          'class'         => 'form-control',
          'required'      => true,
          'placeholder'   => 'extensão em asfalto (em metros)',
      ));

      $this->addElement('text', 'ext_poli', array(
          'label'         => 'Extensão em poliédrico',
          'class'         => 'form-control',
          'required'      => true,
          'placeholder'   => 'extensão em poliédrico (em metros)',
      ));

      $this->addElement('text', 'ext_land', array( 
          'label'         => 'Extesão de terra',
          'class'         => 'form-control',
          'required'      => true,
          'placeholder'   => 'extensão de terra (em metros)',
      ));

      $this->addElement('textarea', 'route', array(
          'label'         => 'Itinerário',
          'placeholder'   => 'itinerários',
          'class'         => 'form-control',
          'required'      => true,
          'cols'					=> 5,
          'rows'					=> 5
      ));

      $this->addElement('textarea', 'ped', array(
          'label'         => 'Pontos de parada',
          'placeholder'   => 'pontos de parada',
          'class'         => 'form-control',
          'required'      => true,
          'cols'					=> 5,
          'rows'					=> 5
      ));

      $this->addElement('submit', 'submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Salvar',
        'class'           => 'btn col-lg-offset-3',
        'decorators'    =>   array('ViewHelper')
      ));

    }


}

