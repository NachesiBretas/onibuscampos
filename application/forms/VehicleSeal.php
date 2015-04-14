<?php

class Application_Form_VehicleSeal extends Twitter_Bootstrap_Form_Horizontal
{
    public function init()
    {
      $this->addElement('hidden','vehicleId');

      $this->addElement('text', 'change_date', array(
          'label'         => 'Data da troca',
          'placeholder'   => 'data da troca do lacre',
          'class'         => 'form-control dateMask'
      ));

      $this->addElement('text', 'seal_roulette_old', array(
          'label'         => 'Lacre do pescoço retirado',
          'placeholder'   => 'Lacre do pescoço retirado',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'seal_roulette_new', array(
          'label'         => 'Lacre do pescoço inserido',
          'placeholder'   => 'Lacre do pescoço inserido',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'seal_floor_old', array(
          'label'         => 'Lacre do assoalho retirado',
          'placeholder'   => 'Lacre do assoalho retirado',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'seal_floor_new', array(
          'label'         => 'Lacre do assoalho inserido',
          'placeholder'   => 'Lacre do assoalho inserido',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'seal_support_old', array(
          'label'         => 'Lacre do suporte retirado',
          'placeholder'   => 'Lacre do suporte retirado',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'seal_support_new', array(
          'label'         => 'Lacre do suporte inserido',
          'placeholder'   => 'Lacre do suporte inserido',
          'class'         => 'form-control'
      ));

      $this->addElement('text', 'old_roulette_number', array(
          'label'         => 'Número da roleta final',
          'placeholder'   => 'Número da roleta final',
          'class'         => 'form-control',
          //'required'      => 'required',
      ));

      $this->addElement('text', 'new_roulette_number', array(
          'label'         => 'Número da roleta inicial',
          'placeholder'   => 'Número da roleta inicial',
          'class'         => 'form-control',
          //'required'      => 'required',
      ));


      $sealChangeArray = array(  0 => '-- Selecione a origem da troca do lacre --',
                                  1 => 'Notificação de irregularidade',
                                  2 => 'Alteração de frota',
                                  3 => 'Solicitação da empresa',);
      $this->addElement('select','seal_change_origin', array(
          'label'             => 'Origem da troca do lacre',
          'class'             => 'form-control',
          'MultiOptions'      => $sealChangeArray
      ) );


      $sealChangeReasonArray = array(  0 => '-- Selecione o motivo da troca do lacre --',
                                  1 => 'Defeito na roleta',
                                  2 => 'Defeito no pescoço',
                                  3 => 'Defeito no assoalho',
                                  4 => 'Defeito no suporte',
                                  5 => 'Lacre violado',
                                  6 => 'Lacre danificado',
                                  7 => 'Falta do lacre');
      $this->addElement('select','seal_change_reason', array(
          'label'             => 'Motivo',
          'class'             => 'form-control',
          'MultiOptions'      => $sealChangeReasonArray
      ) );


      $this->addElement('text', 'dae_number', array(
          'label'         => 'Número da DAE',
          'placeholder'   => 'Número da DAE',
          'class'         => 'form-control',
          //'required'      => 'required',
      ));

     $this->addElement('submit', 'submit', array(
        'buttonType'      => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'           => 'Salvar',
        'class'           => 'col-lg-offset-5'
      ));
    }


}