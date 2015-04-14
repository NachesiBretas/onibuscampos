<?php

class Application_Model_Auth
{

	public function login($data)
	{
		$dbAdapter = Zend_Db_Table::getDefaultAdapter();
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
		$authAdapter->setTableName('user')
					->setIdentityColumn('username')
					->setCredentialColumn('password')
					->setCredentialTreatment('SHA1(?)');
		$authAdapter->setIdentity($data['username'])
					->setCredential($data['password']);
		$auth = Zend_Auth::getInstance();
		$result = $auth->authenticate($authAdapter);
		if ( $result->isValid() ) 
		{
			$info = $authAdapter->getResultRowObject(null, 'password');
			$authNamespace = new Zend_Session_Namespace('userInformation');
			$authNamespace->email = $info->username;
			$authNamespace->user_id = $info->id;
			$authNamespace->username = $info->username;
			$authNamespace->institution = $info->institution;
			$authNamespace->consortium = $info->consortium;
			$authNamespace->company = $info->company_id;
			$this->keepDataAccess($info->id);
			return true;
		}
		else
		{
			return false;
		}
	}

	protected function keepDataAccess($userId)
	{
		$systemAccess = new Application_Model_DbTable_SystemAccess();
		$newAccess = $systemAccess->createRow();
		$newAccess->id_user = $userId;
		$newAccess->date = new Zend_Db_Expr('NOW()');
		$newAccess->ip = $_SERVER['REMOTE_ADDR'];
		$newAccess->save();
	}

}

