<?php 

class Application_Form_RateCalculationVehicleDepreciation extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {
      
      $this->addElement('text', 'age', array(
          'label'         => 'Faixa Etária',
          'placeholder'   => 'Faixa Etária',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'coefficient', array(
          'label'         => '100 - V.R.',
          'placeholder'   => '100 - V.R.',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'digits', array(
          'label'         => 'Dígitos',
          'placeholder'   => 'Dígitos',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'sum_digits', array(
          'label'         => 'Soma Dígitos',
          'placeholder'   => 'Soma Dígitos',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'annual_percentage_depreciation', array(
          'label'         => 'Percentuais Anuais de Depreciação',
          'placeholder'   => 'Percentuais Anuais de Depreciação',
          'class'         => 'form-control'
      ));

      $this->addElement('submit', 'submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Salvar',
        'class'           => 'col-lg-offset-5'
      ));
    }


}

