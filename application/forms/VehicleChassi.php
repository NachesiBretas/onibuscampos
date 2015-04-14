<?php

class Application_Form_VehicleChassi extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {
      $this->addElement('text', 'name', array(
      		'label'					=> 'Modelo do Chassi',
          'placeholder'   => 'modelo do chassi',
          'class'					=> 'form-control'
      ));

      $this->addElement('submit', 'submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Próximo',
        'class'           => 'col-lg-offset-5'
      ));
    }


}

