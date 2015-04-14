<?php

class Application_Form_VehicleDocumentComodato extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {
      $this->setAttrib('enctype', 'multipart/form-data');
      $element = new Zend_Form_Element_File('file');
			$element->setLabel('Carregue o seu arquivo:')
			        ->setDestination(APPLICATION_PATH . '/vehicle');
			$this->addElement($element, 'file');

      $this->addElement('hidden','document', array('value' => 'comodato') );

      $this->addElement('submit', 'submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Próximo',
      ));
    }


}

