<?php

class SchedulingController extends Zend_Controller_Action
{

    public function init()
    {
      $this->_helper->layout()->setLayout('dashboard');
      $authNamespace = new Zend_Session_Namespace('userInformation');
      $this->view->institution = $authNamespace->institution;
      $mail = new Application_Model_Mail();
      $this->view->messages = $mail->getUnreadMessages();
    }

    public function indexAction()
    {
        // action body
    }

    public function treatmentAction()
    {
      $this->view->treatment = true;
    }

    public function calendarAction()
    {
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        $scheduling = new Application_Model_Scheduling();
        if($scheduling->newAbsence($data))
        {
          $this->view->save = 'success';
        }
        else
        {
          $this->view->save = 'error';
        }
      }
      $this->view->form = new Application_Form_Absence();
    }

    public function returnEventsAction()
    {
      header('Content-type: text/json');
      $this->_helper->layout()->setLayout('ajax');
      $data = $this->getRequest()->getPost();
      if($data['date'] != '')
      {
        $scheduling = new Application_Model_Scheduling();
        $events = $scheduling->returnEvents($data['date']);
        if($data['date'] == date('Y-n-d'))
        {
          $events = $scheduling->addHoursPassed($events);
        }
      }
      else
      {
        $events = array();
      }
      echo Zend_Json::encode($events);
    }

    public function scheduleAction()
    {
      $this->_helper->layout()->setLayout('ajax');
    }

    public function returnAllEventsAction()
    {
      $this->_helper->layout()->setLayout('ajax');
      $scheduling = new Application_Model_Scheduling();
      $events = $scheduling->returnAllEvents();
      echo Zend_Json::encode($events);
    }

    public function returnHourAction()
    {
      $this->_helper->layout()->setLayout('ajax');
      $scheduling = new Application_Model_Scheduling();
      $hour = $scheduling->returnHour();
      echo Zend_Json::encode($hour->toArray());
    }

    public function returnSchedulingsAction()
    {
      $this->_helper->layout()->setLayout('ajax');
      $scheduling = new Application_Model_Scheduling();
      $events = $scheduling->returnEventsCalendar();
      echo Zend_Json::encode($events);
    }

    public function hourAction()
    {
      $scheduling = new Application_Model_Scheduling();
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        $scheduling->newHour($data);
      }
      $this->view->schedulingHourForm = new Application_Form_SchedulingHour();
      $this->view->schedulingHour = $scheduling->returnHour();
    }

    public function reportAction()
    {
        // action body
    }

    public function deleteHourAction()
    {
      $id = $this->getRequest()->getParam('id');
      if( isset($id) )
      {
        $scheduling = new Application_Model_Scheduling();
        $scheduling->deleteHour($id);
        return $this->_redirect('/scheduling/hour/save/success');
      }
      return $this->_redirect('/scheduling/hour/save/failed');
    }

    public function graphicAction()
    {
        // action body
    }

    public function returnSchedulingsVisAction()
    {
      $this->_helper->layout()->setLayout('ajax');
      $arr = array(
                    array('date' => '2014-01-24', 'total' => '4'),
                    array('date' => '2014-01-27', 'total' => '3'),
                    array('date' => '2014-01-28', 'total' => '4'),
                    array('date' => '2014-01-29', 'total' => '2'),
                    array('date' => '2014-01-30', 'total' => '1'),
                    array('date' => '2014-01-31', 'total' => '6'),
                  );
      echo Zend_Json::encode($arr);
    }

    public function reportSchedulingAction()
    {
      $this->_helper->layout()->setLayout('report');
      $startDate = $this->getRequest()->getParam('start_date');
      $endDate = $this->getRequest()->getParam('end_date');
      $scheduling = new Application_Model_Scheduling();
      $this->view->data = $scheduling->returnReport(Application_Model_General::dateToUs($startDate),Application_Model_General::dateToUs($endDate));
    }

    public function removeAction()
    {
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        $id = base64_decode($data['id']);
        $scheduling = new Application_Model_Scheduling();
        if( $scheduling->remove($id) ) 
        {
          return $this->_redirect('/agendamento/consulta/save/success');
        }
      }
      return $this->_redirect('/agendamento/consulta/save/failed');
    }

    public function rescheduleAction()
    {
      if ( $this->getRequest()->isPost() ) 
      {
        $data = $this->getRequest()->getPost();
        $scheduling = new Application_Model_Scheduling();
        $newScheduling = $scheduling->reschedule($data['id'],$data['hour'],$data['date']);
        if($newScheduling)
        {
          $this->redirect('/agendamento/completo/id/'.base64_encode($newScheduling));
        }
        else
        {
          $this->redirect('/agendamento/consulta/save/failed');
        }
      }
    }

    public function printAction()
    {

    }

    public function printCalendarAction()
    {
      $date = Application_Model_General::dateToUs($_GET["date"]);
      if ($date) 
      {
        $scheduling = new Application_Model_Scheduling();
        $this->view->list = $scheduling->returnPrintCalendar($date);
        $this->view->date = $date;
      }
    }


}































