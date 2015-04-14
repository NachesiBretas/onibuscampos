<?php

class Application_Form_FinanceFare extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {
      $this->addElement('text', 'name', array(
      		'label'					=> 'Nome',
          'placeholder'   => 'nome do grupo tarifário',
          'class'					=> 'form-control'
      ));

      $this->addElement('text', 'value', array(
      		'label'					=> 'Valor',
          'placeholder'   => 'valor',
          'class'					=> 'form-control'
      ));

      $this->addElement('submit', 'submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Salvar',
        'class'           => 'col-lg-offset-5'
      ));
    }


}

