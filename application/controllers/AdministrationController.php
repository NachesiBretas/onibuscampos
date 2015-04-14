<?php

class AdministrationController extends Zend_Controller_Action
{

    public function init()
    {
      $this->_helper->layout()->setLayout('dashboard');
      $authNamespace = new Zend_Session_Namespace('userInformation');
      $this->view->institution = $authNamespace->institution;
      $this->view->userId = $authNamespace->user_id; 
      if($this->view->institution != 1)
      {
      	$this->_redirect('/doesntallow');
      }
      $mail = new Application_Model_Mail();
      $this->view->messages = $mail->getUnreadMessages();
    }

    public function indexAction()
    {
        // action body
    }

    public function userAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $user = new Application_Model_User();
      $pagination = new Application_Model_Pagination();
      $field = $this->getRequest()->getParam('field');
      $page = $this->getRequest()->getParam('page');
      if($page == '') $page = 1;
      if($field != "")
      {
        $users = $user->findByName(urldecode($field));
        $this->view->list = $pagination->generatePagination($users,$page,10);
        $this->view->field = $field;
      }
      else
      {
        $user = $user->lists();
        $this->view->list = $pagination->generatePagination($user,$page,10);
      }
    }

    public function userNewAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $this->view->form = new Application_Form_User();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        $inspector = new Application_Model_User();
        if($inspector->newUser($data))
        {
          $this->view->save = 'success';
        }
        else
        {
          $this->view->form->populate($data);
          $this->view->save = 'error';
        }
      }
    }

    public function userEditAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $userId = $this->getRequest()->getParam('id');
      $save = $this->getRequest()->getParam('save');
      $user = new Application_Model_User();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($user->editUser($data,$userId))
        {
           $this->view->save = 'success';
        }
        else
        {
          $this->view->save = 'error';
        }
      }
      $this->view->form = new Application_Form_User();
      $user = $user->returnById($userId);
      unset($user->password);
      $this->view->form->reset();
      $this->view->form->populate($user->toArray());
    }

    public function cityAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $city = new Application_Model_City();
      $pagination = new Application_Model_Pagination();
      $field = $this->getRequest()->getParam('field');
      $page = $this->getRequest()->getParam('page');
      if($page == '') $page = 1;
      if($field != "")
      {
        $citys = $city->findByName(urldecode($field));
        $this->view->list = $pagination->generatePagination($citys,$page,10);
        $this->view->field = $field;
      }
      else
      {
        $city = $city->lists();
        $this->view->list = $pagination->generatePagination($city,$page,10);
      }
    }

    public function cityNewAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $this->view->form = new Application_Form_City();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        $city = new Application_Model_City();
        if($city->newCity($data))
        {
          $this->view->save = 'success';
        }
        else
        {
          $this->view->form->populate($data);
          $this->view->save = 'error';
        }
      }
    }

    public function cityEditAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $cityId = $this->getRequest()->getParam('id');
      $save = $this->getRequest()->getParam('save');
      $city = new Application_Model_City();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($city->editCity($data,$cityId))
        {
           $this->view->save = 'success';
        }
        else
        {
          $this->view->save = 'error';
        }
      }
      $this->view->form = new Application_Form_City();
      $city = $city->returnById($cityId);
      $this->view->form->reset();
      $this->view->form->populate($city->toArray());
    }

    public function patternAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $vehiclePattern = new Application_Model_VehiclePattern();
      $pagination = new Application_Model_Pagination();
      $field = $this->getRequest()->getParam('field');
      $page = $this->getRequest()->getParam('page');
      if($page == '') $page = 1;
      if($field != "")
      {
        $vehiclePatterns = $vehiclePattern->findByName(urldecode($field));
        $this->view->list = $pagination->generatePagination($vehiclePatterns,$page,10);
        $this->view->field = $field;
      }
      else
      {
        $vehiclePattern = $vehiclePattern->lists();
        $this->view->list = $pagination->generatePagination($vehiclePattern,$page,10);
      }
    }

    public function patternNewAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $this->view->form = new Application_Form_VehiclePattern();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        $vehiclePattern = new Application_Model_VehiclePattern();
        if($vehiclePattern->newVehiclePattern($data))
        {
          $this->view->save = 'success';
        }
        else
        {
          $this->view->form->populate($data);
          $this->view->save = 'error';
        }
      }
    }

    public function patternEditAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $vehiclePatternId = $this->getRequest()->getParam('id');
      $save = $this->getRequest()->getParam('save');
      $vehiclePattern = new Application_Model_VehiclePattern();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($vehiclePattern->editVehiclePattern($data,$vehiclePatternId))
        {
           $this->view->save = 'success';
        }
        else
        {
          $this->view->save = 'error';
        }
      }
      $this->view->form = new Application_Form_VehiclePattern();
      $vehiclePattern = $vehiclePattern->returnById($vehiclePatternId);
      $this->view->form->reset();
      $this->view->form->populate($vehiclePattern->toArray());
    }

    public function chassiAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $vehicleChassi = new Application_Model_VehicleChassi();
      $pagination = new Application_Model_Pagination();
      $field = $this->getRequest()->getParam('field');
      $page = $this->getRequest()->getParam('page');
      if($page == '') $page = 1;
      if($field != "")
      {
        $vehicleChassis = $vehicleChassi->findByName(urldecode($field));
        $this->view->list = $pagination->generatePagination($vehicleChassis,$page,10);
        $this->view->field = $field;
      }
      else
      {
        $vehicleChassi = $vehicleChassi->lists();
        $this->view->list = $pagination->generatePagination($vehicleChassi,$page,10);
      }
    }

    public function chassiNewAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $this->view->form = new Application_Form_VehicleChassi();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        $vehiclePattern = new Application_Model_VehicleChassi();
        if($vehiclePattern->newVehicleChassi($data))
        {
          $this->view->save = 'success';
        }
        else
        {
          $this->view->form->populate($data);
          $this->view->save = 'error';
        }
      }
    }

    public function chassiEditAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $vehicleChassiId = $this->getRequest()->getParam('id');
      $save = $this->getRequest()->getParam('save');
      $vehicleChassi = new Application_Model_VehicleChassi();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($vehicleChassi->editVehicleChassi($data,$vehicleChassiId))
        {
           $this->view->save = 'success';
        }
        else
        {
          $this->view->save = 'error';
        }
      }
      $this->view->form = new Application_Form_VehicleChassi();
      $vehicleChassi = $vehicleChassi->returnById($vehicleChassiId);
      $this->view->form->reset();
      $this->view->form->populate($vehicleChassi->toArray());
    }

    public function typeAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $vehicleType = new Application_Model_VehicleType();
      $pagination = new Application_Model_Pagination();
      $field = $this->getRequest()->getParam('field');
      $page = $this->getRequest()->getParam('page');
      if($page == '') $page = 1;
      if($field != "")
      {
        $vehicleTypes = $vehicleType->findByName(urldecode($field));
        $this->view->list = $pagination->generatePagination($vehicleTypes,$page,10);
        $this->view->field = $field;
      }
      else
      {
        $vehicleType = $vehicleType->lists();
        $this->view->list = $pagination->generatePagination($vehicleType,$page,10);
      }
    }

    public function typeNewAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $this->view->form = new Application_Form_VehicleType();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        $vehiclePattern = new Application_Model_VehicleType();
        if($vehiclePattern->newVehicleType($data))
        {
          $this->view->save = 'success';
        }
        else
        {
          $this->view->form->populate($data);
          $this->view->save = 'error';
        }
      }
    }

    public function typeEditAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $vehicleTypeId = $this->getRequest()->getParam('id');
      $save = $this->getRequest()->getParam('save');
      $vehicleType = new Application_Model_VehicleType();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($vehicleType->editVehicleType($data,$vehicleTypeId))
        {
           $this->view->save = 'success';
        }
        else
        {
          $this->view->save = 'error';
        }
      }
      $this->view->form = new Application_Form_VehicleType();
      $vehicleType = $vehicleType->returnById($vehicleTypeId);
      $this->view->form->reset();
      $this->view->form->populate($vehicleType->toArray());
    }

    public function colorAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $vehicleColor = new Application_Model_VehicleColor();
      $pagination = new Application_Model_Pagination();
      $field = $this->getRequest()->getParam('field');
      $page = $this->getRequest()->getParam('page');
      if($page == '') $page = 1;
      if($field != "")
      {
        $vehicleColors = $vehicleColor->findByName(urldecode($field));
        $this->view->list = $pagination->generatePagination($vehicleColors,$page,10);
        $this->view->field = $field;
      }
      else
      {
        $vehicleColor = $vehicleColor->lists();
        $this->view->list = $pagination->generatePagination($vehicleColor,$page,10);
      }
    }

    public function colorNewAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $this->view->form = new Application_Form_VehicleColor();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        $vehiclePattern = new Application_Model_VehicleColor();
        if($vehiclePattern->newVehicleColor($data))
        {
          $this->view->save = 'success';
        }
        else
        {
          $this->view->form->populate($data);
          $this->view->save = 'error';
        }
      }
    }

    public function colorEditAction()
    {
     if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $vehicleColorId = $this->getRequest()->getParam('id');
      $save = $this->getRequest()->getParam('save');
      $vehicleColor = new Application_Model_VehicleColor();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($vehicleColor->editVehicleColor($data,$vehicleColorId))
        {
           $this->view->save = 'success';
        }
        else
        {
          $this->view->save = 'error';
        }
      }
      $this->view->form = new Application_Form_VehicleColor();
      $vehicleColor = $vehicleColor->returnById($vehicleColorId);
      $this->view->form->reset();
      $this->view->form->populate($vehicleColor->toArray());
    }

    public function bodyAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $vehicleBody = new Application_Model_VehicleBody();
      $pagination = new Application_Model_Pagination();
      $field = $this->getRequest()->getParam('field');
      $page = $this->getRequest()->getParam('page');
      if($page == '') $page = 1;
      if($field != "")
      {
        $vehicleBodys = $vehicleBody->findByName(urldecode($field));
        $this->view->list = $pagination->generatePagination($vehicleBodys,$page,10);
        $this->view->field = $field;
      }
      else
      {
        $vehicleBody = $vehicleBody->lists();
        $this->view->list = $pagination->generatePagination($vehicleBody,$page,10);
      }
    }

    public function bodyNewAction()
    {
      if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $this->view->form = new Application_Form_VehicleBody();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        $vehiclePattern = new Application_Model_VehicleBody();
        if($vehiclePattern->newVehicleBody($data))
        {
          $this->view->save = 'success';
        }
        else
        {
          $this->view->form->populate($data);
          $this->view->save = 'error';
        }
      }
    }

    public function bodyEditAction()
    {
     if($this->view->institution != 1)
      {
        $this->_redirect('/doesntallow');
      }
      $vehicleBodyId = $this->getRequest()->getParam('id');
      $save = $this->getRequest()->getParam('save');
      $vehicleBody = new Application_Model_VehicleBody();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        if($vehicleBody->editVehicleBody($data,$vehicleBodyId))
        {
           $this->view->save = 'success';
        }
        else
        {
          $this->view->save = 'error';
        }
      }
      $this->view->form = new Application_Form_VehicleBody();
      $vehicleBody = $vehicleBody->returnById($vehicleBodyId);
      $this->view->form->reset();
      $this->view->form->populate($vehicleBody->toArray());
    }


}











































