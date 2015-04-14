<?php

class Application_Form_QcoHistoric extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {
      $this->addElement('hidden','id');

      $this->addElement('text', 'subject', array(
          'label'         => 'Tipo de alteração',
          'placeholder'   => 'título da alteração',
          'class'         => 'form-control'
      ));

      $this->addElement('textarea', 'description', array(
          'label'         => 'Descrição',
          'placeholder'   => 'descrição da alteração',
          'class'         => 'form-control',
          'required'      => true,
          'cols'					=> 5,
          'rows'					=> 5
      ));

      $this->addElement('submit', 'submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Salvar',
        'class'           => 'col-lg-offset-5'
      ));
    }


}

