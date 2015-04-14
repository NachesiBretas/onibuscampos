<?php

class Application_Form_User extends Twitter_Bootstrap_Form_Horizontal
{

    public function init()
    {
      $this->setIsArray(true);

      $this->addElement('text', 'name', array(
      		'label'					=> 'Nome',
          'placeholder'   => 'nome completo',
          'class'					=> 'form-control'
      ));

      $this->addElement('text', 'phone', array(
      		'label'					=> 'Telefone',
          'placeholder'   => 'telefone',
          'class'					=> 'form-control phone'
      ));

      $this->addElement('text', 'email', array(
      		'label'					=> 'E-mail',
          'placeholder'   => 'email',
          'class'					=> 'form-control'
      ));

      $this->addElement('text','masp', array(
          'label'             => 'MASP',
          'placeholder'       => 'número de matrícula',
          'class'             => 'form-control',
      ) );

      $this->addElement('text', 'username', array(
      		'label'					=> 'Usuário no sistema',
          'placeholder'   => 'usuário para logar no sistema',
          'class'					=> 'form-control'
      ));

      $this->addElement('password', 'password', array(
      		'label'					=> 'Senha',
          'placeholder'   => 'senha',
          'class'					=> 'form-control'
      ));

      $this->addElement('password', 'confirm_password', array(
      		'label'					=> 'Confirme a senha',
          'placeholder'   => 'confirme a senha',
          'class'					=> 'form-control'
      ));

      $this->addElement('select','institution', array(
          'label'             => 'Organização',
          'class'             => 'form-control',
          'MultiOptions'      => array(
                      0 => '-- Selecione uma organização --',
                      1 => 'SETOP',
                      2 => 'DER-MG',
                      3 => 'CONSÓRCIO',
                      4 => 'SINTRAM'
            )
      ) );

      $vehicleConsortiumArray = array( 0 => '-- Selecione um consórcio --');
      $table = new Application_Model_DbTable_Consortium();
      foreach ($table->fetchAll() as $c) {
          $vehicleConsortiumArray[$c->id] = $c->name;
      }
      $this->addElement('select','consortium', array(
          'label'             => 'Consórcio',
          'class'             => 'form-control',
          'MultiOptions'      => $vehicleConsortiumArray
      ) );

      $vehicleConsortiumArray = array( 0 => '-- Selecione uma empresa --');
      $table = new Application_Model_DbTable_Company();
      foreach ($table->fetchAll() as $c) {
          $vehicleConsortiumArray[$c->id] = $c->company;
      }
      $this->addElement('select','company_id', array(
          'label'             => 'Empresa',
          'class'             => 'form-control',
          'MultiOptions'      => $vehicleConsortiumArray
      ) );

      $this->addElement('submit', 'submit', array(
        'buttonType' 			=> Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
        'label'      			=> 'Salvar',
        'class'           => 'col-lg-offset-5'
      ));
    }


}

