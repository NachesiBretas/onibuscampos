<?php 

class Application_Form_RateCalculationOperationCrew extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {
      
      $this->addElement('text', 'categorie', array(
          'label'         => 'Categoria',
          'placeholder'   => 'Categoria',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'charges_salaries_scaling', array(
          'label'         => 'Dimensionamento de Salários e Encargos',
          'placeholder'   => 'Dimensionamento de Salários e Encargos',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'benefits_scaling', array(
          'label'         => 'Dimensionamento de Benefícios',
          'placeholder'   => 'Dimensionamento de Benefícios',
          'class'         => 'form-control'
      ));

      $this->addElement('submit', 'submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Salvar',
        'class'           => 'col-lg-offset-5'
      ));
    }


}

