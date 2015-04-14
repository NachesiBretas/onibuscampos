<?php

class Application_Form_VehicleMeasures extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {

      $this->addElement('text', 'weight', array(
          'label'         => 'Peso',
          'placeholder'   => 'peso do veículo (em kg)',
          'class'         => 'form-control',
          'maxlength'			=> 5
      ));

      $this->addElement('text', 'length_before_roulette', array(
          'label'         => 'Comprimento antes da roleta',
          'placeholder'   => 'comprimento antes da roleta (em cm)',
          'class'         => 'form-control',
          'maxlength'			=> 5
      ));

			$this->addElement('text', 'length_after_roulette', array(
          'label'         => 'Comprimento após roleta',
          'placeholder'   => 'comprimento após roleta (em cm)',
          'class'         => 'form-control',
          'maxlength'			=> 5
      ));

			$this->addElement('text', 'width_before_roulette', array(
          'label'         => 'Largura antes da roleta',
          'placeholder'   => 'largura antes da roleta (em cm)',
          'class'         => 'form-control',
          'maxlength'			=> 5
      ));

			$this->addElement('text', 'width_after_roulette', array(
          'label'         => 'Largura após da roleta',
          'placeholder'   => 'largura após da roleta (em cm)',
          'class'         => 'form-control',
          'maxlength'			=> 5
      ));

			$this->addElement('text', 'width_door_front_right', array(
          'label'         => 'Largura da porta dianteira (lado direito)',
          'placeholder'   => 'largura da porta dianteira do lado direito (em cm)',
          'class'         => 'form-control',
          'maxlength'			=> 5
      ));

			$this->addElement('text', 'width_door_middle1_right', array(
          'label'         => 'Largura da porta central 1 (lado direito)',
          'placeholder'   => 'largura da porta central 1 do lado direito (em cm)',
          'class'         => 'form-control',
          'maxlength'			=> 5
      ));

      $this->addElement('text', 'width_door_middle2_right', array(
          'label'         => 'Largura da porta central 2 (lado direito)',
          'placeholder'   => 'largura da porta central 2 do lado direito (em cm)',
          'class'         => 'form-control',
          'maxlength'     => 5
      ));

			$this->addElement('text', 'width_door_back_right', array(
          'label'         => 'Largura da porta traseira (lado direito)',
          'placeholder'   => 'largura da porta traseira do lado direito (em cm)',
          'class'         => 'form-control',
          'maxlength'			=> 5
      ));


      $this->addElement('text', 'width_door_front_left', array(
          'label'         => 'Largura da porta dianteira (lado esquerdo)',
          'placeholder'   => 'largura da porta dianteira do lado esquerdo (em cm)',
          'class'         => 'form-control',
          'maxlength'     => 5
      ));

      $this->addElement('text', 'width_door_middle1_left', array(
          'label'         => 'Largura da porta central 1 (lado esquerdo)',
          'placeholder'   => 'largura da porta central 1 do lado esquerdo (em cm)',
          'class'         => 'form-control',
          'maxlength'     => 5
      ));

      $this->addElement('text', 'width_door_middle2_left', array(
          'label'         => 'Largura da porta central 2 (lado esquerdo)',
          'placeholder'   => 'largura da porta central 2 do lado esquerdo (em cm)',
          'class'         => 'form-control',
          'maxlength'     => 5
      ));

      $this->addElement('text', 'width_door_back_left', array(
          'label'         => 'Largura da porta traseira (lado esquerdo)',
          'placeholder'   => 'largura da porta traseira do lado esquerdo (em cm)',
          'class'         => 'form-control',
          'maxlength'     => 5
      ));

      $this->addElement('text', 'length_deficient', array(
          'label'         => 'Comprimento da área de deficientes',
          'placeholder'   => 'comprimento da área de deficientes (em cm)',
          'class'         => 'form-control',
          'maxlength'     => 5
      ));

      $this->addElement('text', 'width_deficient', array(
          'label'         => 'Largura da área de deficientes',
          'placeholder'   => 'largura da área de deficientes (em cm)',
          'class'         => 'form-control',
          'maxlength'     => 5
      ));

			$this->addElement('text', 'tank1', array(
          'label'         => 'Tanque 1',
          'placeholder'   => 'capacidade do tanque 1 (em litros)',
          'class'         => 'form-control',
          'maxlength'			=> 4
      ));

      $this->addElement('text', 'tank2', array(
          'label'         => 'Tanque 2',
          'placeholder'   => 'capacidade do tanque 2 (em litros)',
          'class'         => 'form-control',
          'maxlength'     => 4
      ));

			$this->addElement('text', 'amount_seats', array(
          'label'         => 'Número de assentos',
          'placeholder'   => 'número de assentos',
          'class'         => 'form-control',
          'maxlength'			=> 4
      ));
      
      $this->addElement('submit', 'submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Próximo',
        'class'           => 'col-lg-offset-5'
      ));

    }


}

