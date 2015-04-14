<?php 

class Application_Form_RateCalculationVehicleRemuneration extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {
      
      $this->addElement('text', 'age', array(
          'label'         => 'Faixa Etária',
          'placeholder'   => 'Faixa Etária',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'initial', array(
          'label'         => 'Inicial',
          'placeholder'   => 'Inicial',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'depreciation_portion', array(
          'label'         => 'Parcela Depreciada',
          'placeholder'   => 'Parcela Depreciada',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'final', array(
          'label'         => 'Final',
          'placeholder'   => 'Final',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'average', array(
          'label'         => 'Médio',
          'placeholder'   => 'Médio',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'annual_remuneration', array(
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

