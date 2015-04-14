<?php

class Application_Form_VehicleInspection extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {
      $this->setAttrib('enctype', 'multipart/form-data');
      $element = new Zend_Form_Element_File('file');
			$element->setLabel('Carregue o laudo da vistoria:')
			        ->setDestination(APPLICATION_PATH . '/vehicle');
			$this->addElement($element, 'file');

      $this->addElement('text', 'date_inspection', array(
            'label'         => 'Data',
            'placeholder'   => 'Data do laudo',
            'class'         => 'form-control dateMask'
        )); 

      $this->addElement('text', 'hour_inspection', array(
            'label'         => 'Hora',
            'placeholder'   => 'Hora do laudo',
            'class'         => 'form-control hour'
        )); 

      $this->addElement('text', 'roulette_number', array(
          'label'         => 'Número da roleta',
          'placeholder'   => 'Número da roleta',
          'class'         => 'form-control'
      ));

      $this->addElement('textarea', 'observation', array(
          'label'         => 'Observação',
          'placeholder'   => 'Observação',
          'class'         => 'form-control',
          'rows'          => '10',
          'cols'          => '20'
      ));

      $this->addElement('hidden','document', array('value' => 'inspection') );

      $this->addElement('submit', 'submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Próximo',
      ));
    }


}

