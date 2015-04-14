<?php

class MailController extends Zend_Controller_Action
{

    public function init()
    {
    	$this->_helper->layout()->setLayout('dashboard');
      $mail = new Application_Model_Mail();
      $this->view->messages = $mail->getUnreadMessages();
    }

    public function indexAction()
    {
        // action body
    }

    public function inboxAction()
    {
      try{
        $inbox = new Application_Model_Mail();
        $pagination = new Application_Model_Pagination();
        $optionSearch = $this->getRequest()->getParam('optionSearch');
        $field = $this->getRequest()->getParam('field');
        $page = $this->getRequest()->getParam('page');
        if($page == '') $page = 1;
        if($field != "" && ($optionSearch <= 3 && $optionSearch >= 1) )
        {
          if($optionSearch == 1)
          {
            $msgs = $inbox->findBySender(urldecode($field));
          }
          if($optionSearch == 2)
          {
            $msgs = $inbox->findByTitle(urldecode($field));
          }
          if($optionSearch == 3)
          {
            $msgs = $inbox->findByDate(urldecode($field));
          }
          $this->view->list = $pagination->generatePagination($msgs,$page,10);
          $this->view->field = $field;
          $this->view->optionSearch = $optionSearch;
        }    
        else
        {
          $msgs = $inbox->listInboxMessage();
          $this->view->list = $pagination->generatePagination($msgs,$page,10);
        }
      }catch(Zend_Exception $e) {
        echo $e->getMessage();
      }
    }

    public function outboxAction()
    {
        $outbox = new Application_Model_Mail();
        $pagination = new Application_Model_Pagination();
        $optionSearch = $this->getRequest()->getParam('optionSearch');
        $field = $this->getRequest()->getParam('field');
        $page = $this->getRequest()->getParam('page');
        if($page == '') $page = 1;
        if($field != "" && ($optionSearch <= 3 && $optionSearch >= 1) )
        {
          if($optionSearch == 1)
          {
            $msgs = $outbox->findBySenderOut(urldecode($field));
          }
          if($optionSearch == 2)
          {
            $msgs = $outbox->findByTitleOut(urldecode($field));
          }
          if($optionSearch == 3)
          {
            $msgs = $outbox->findByDateOut(urldecode($field));
          }
          $this->view->list = $pagination->generatePagination($msgs,$page,10);
          $this->view->field = $field;
          $this->view->optionSearch = $optionSearch;
        }    
        else
        {
          $msgs = $outbox->listOutboxMessage();
          $this->view->list = $pagination->generatePagination($msgs,$page,10);
        }
   }

    public function newAction()
    {
      $new_mail = new Application_Model_Mail();
      $mail = $new_mail->createMessage($_POST);
      $annex = $new_mail->createAnnex($_FILES, $mail);

      if ($mail)
      {
        $this->_redirect('/mail/inbox/save/success');
      }
      else 
      {
        $this->_redirect('/mail/inbox/save/error');
      }
    }

    public function forwardAction()
    {
      $new_mail = new Application_Model_Mail();
      $mail = $new_mail->createMessage($_POST);
      mkdir(APPLICATION_PATH.'/upload/'.$mail.'/');
      $file = APPLICATION_PATH.'/upload/'.$_POST['forwarded_message'].'/'.$_POST['annex'];
      copy($file,APPLICATION_PATH.'/upload/'.$mail.'/'.$_POST['annex']);   
      $annex = $new_mail->newAnnexForw($mail,$_POST['annex']);
      if ($mail)
      {
        $this->_redirect('/mail/inbox/save/success');
      }
      else 
      {
        $this->_redirect('/mail/inbox/save/error');
      }
    }


    public function viewAction()
    {
        // action body
    }

    public function parentAction()
    {
      $this->_helper->layout()->setLayout('ajax');
      $list_parent = new Application_Model_Mail();
      $list_parent-> setRead($_POST['parent']);
      $messages = $list_parent->listParentConversation($_POST['parent']);
      echo Zend_Json::encode($messages->toArray());
    }

    public function parentOutAction()
    {
      $this->_helper->layout()->setLayout('ajax');
      $list_parent = new Application_Model_Mail();
      $messages = $list_parent->listParentConversation($_POST['parent']);
      echo Zend_Json::encode($messages->toArray());
    }

    public function newAnnexAction()
    {
      // code here
    }

    public function downloadAction()
    {
      $messageId = $this->getRequest()->getParam('id');
      $annexName = $this->getRequest()->getParam('name');
      $message = new Application_Model_Mail();
      if(!$message->verifyAccess($messageId)) 
        return $this->redirect('doesntallow');
      $fileDir = APPLICATION_PATH . '/upload/'.$messageId.'/'.$annexName;
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header("Content-Type: application/force-download");    
      header('Content-Disposition: attachment; filename=' . urlencode(basename($fileDir)));
      header("Content-Length: " . filesize($fileDir));
      header('Expires: 0');
      header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
      header('Pragma: public');
      readfile($fileDir);
      $this->view->layout()->disableLayout();
      $this->_helper->viewRenderer->setNoRender(true);
    }

    

}





















