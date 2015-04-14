<?php

class Application_Form_Absence extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {

      $this->addElement('text', 'date', array(
      		'label'					=> 'Data',
          'placeholder'   => 'data do recesso',
          'class'					=> 'form-control dateMask'
      ));

      $this->addElement('text', 'hour_start', array(
          'label'         => 'Hora início',
          'placeholder'   => 'hora de início do recesso',
          'class'         => 'form-control hour'
      ));

      $this->addElement('text', 'hour_end', array(
          'label'         => 'Hora fim',
          'placeholder'   => 'hora de fim do recesso',
          'class'         => 'form-control hour'
      ));

      $this->addElement('hidden', 'name', array(
        'value'           => 'Recesso'
      ));

      $this->addElement('submit', 'submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Salvar',
        'class'           => 'col-lg-offset-5'
      ));
    }


}

