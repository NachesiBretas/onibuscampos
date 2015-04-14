<?php

class Application_Form_VehicleBody extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {
      $this->addElement('text', 'name', array(
      		'label'					=> 'Carroceria',
          'placeholder'   => 'modelo da carroceria',
          'class'					=> 'form-control'
      ));

      $this->addElement('submit', 'submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Próximo',
        'class'           => 'col-lg-offset-5'
      ));
    }


}

