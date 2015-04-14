<?php

class Application_Model_Mail
{

	public function createMessage($data)
	{
   	$authNamespace = new Zend_Session_Namespace('userInformation');
		$mail = new Application_Model_DbTable_MailBox();
		$mailNew = $mail->createRow();
		if($data["parent"] != '') $mailNew->parent = $data["parent"];
		$mailNew->sender = $authNamespace->user_id;
		$mailNew->receiver = $data["receiver_id"];
		$mailNew->title = $data["title"];
		$mailNew->body = $data["body"];
		$mailNew->date_sent = new Zend_Db_Expr('NOW()');
		return $mailNew->save();
	}

	public function createAnnex($data, $mail)
	{
	  	$annex = new Application_Model_DbTable_MailBox();
		  if ($data["annex"]["error"] > 0)
	    {
	    	$erro = "Return Code: " .$data["annex"]["error"] . "<br>";
	    	return $erro;
	    }
	    if(file_exists(APPLICATION_PATH."\upload\\".$mail."\\".$data["annex"]["name"]))
	    {
	    	$exist = "Um arquivo com o nome".$data["annex"]["name"]."já existe";
	    	return $exist;
	    }
	    else
	    {
	    	mkdir(APPLICATION_PATH."\upload\\".$mail);
		    if(move_uploaded_file($data["annex"]["tmp_name"],APPLICATION_PATH."\upload\\".$mail."\\".$data["annex"]["name"]))
		    {
					$annex_row = $annex->fetchRow($annex->select()->where('id = ?', $mail));
					if($annex_row)
					{
						$annex_row->annex = $data["annex"]["name"];
						$annex_row->save();
					}
		    	return true;
		    }
	    }
	}

	public function newAnnexForw($id, $file_name)
	{
			$annex = new Application_Model_DbTable_MailBox();
			$annex_row = $annex->fetchRow($annex->select()->where('id = ?', $id));
				if($annex_row)
				{
					if ($file_name != null)
					{
						$annex_row->annex = $file_name;
						$annex_row->save();
					}
					else
					{
						$annex_row->annex = NULL;
						$annex_row->save();	
					}
				}
	}


	public function listParentConversation($id)
	{	
      	$authNamespace = new Zend_Session_Namespace('userInformation');
		$mail = new Application_Model_DbTable_MailBox();
		$select = $mail->select()->setIntegrityCheck(false);
		$select	->from(array('m' => 'mail_box'), array('id','sender','title', 'body', 'parent', 'annex', 'date' => "DATE_FORMAT(date_sent,'%d/%m/%Y')", 'date_received_aux' => "DATE_FORMAT(date_received,'%d/%m/%Y')"))
						->joinInner(array('u' => 'user'), 'u.id=m.sender', array('name'))
						->where('m.parent = ?', $id)
						->orwhere('m.id = ?', $id);
		return $mail->fetchAll($select);
	}

	public function setRead($id)
	{

		$mail = new Application_Model_DbTable_MailBox();
		$mail_row = $mail->fetchAll($mail->select()->where('date_received is NULL')
												   ->where('parent = ? OR id= ?',$id));
												   
		foreach($mail_row as $row)
		{
			$row->date_received = new Zend_Db_Expr("NOW()");
			$row->save();
		}
 	}

	public function listInboxMessage()
	{	
    $authNamespace = new Zend_Session_Namespace('userInformation');
		$registry = Zend_Registry::getInstance();
		$db = $registry->get('db');
		$aux = $db->query("
			SELECT 
			t1.id,
			t1.sender, 
			t1.title,
			t1.body,
			t2.contador, 
			IF(t1.sender= ".$authNamespace->user_id.", u1.name, u.name) as name,
		 	DATE_FORMAT(t1.date_sent,'%d/%m/%Y') as date_aux, 
		 	DATE_FORMAT(t1.date_received,'%d/%m/%Y') as date_aux_received, 
		 	t3.id as id_filha, 
		 	t3.pai2,
		 	CASE
				WHEN t3.id IS NULL and t3.pai2 IS NULL THEN '0'
				WHEN t3.estado_lida IS NULL THEN '1'
				ELSE t3.estado_lida
				END AS estado_lida
			FROM 
				(SELECT 
					id, sender, receiver, title, body, date_sent, date_received
				FROM 
					mail_box 
				WHERE 
					(parent is NULL AND receiver = ".$authNamespace->user_id.") 
				UNION 
					(SELECT 
						id, sender, receiver, title, body, date_sent, date_received
					FROM 
						mail_box as u1
					INNER JOIN 
						(SELECT 
							parent as mensagem, title as titulo, body as corpo, date_sent as data_enviada, date_received as data_recebida
						FROM 
							mail_box
						WHERE 
							parent is NOT NULL) as u2
					ON 
						(u1.id = u2.mensagem)
					WHERE 
						sender =".$authNamespace->user_id.")) as t1 
			INNER JOIN 
				user as u 
			ON 
				(u.id = t1.sender)
			INNER JOIN 
				user as u1 
			ON 
				(u1.id = t1.receiver)
			LEFT JOIN 
				(SELECT 
					parent as parent_id, SUM(IF(parent IS NULL OR parent IS NOT NULL,1,0))+1 as contador
				FROM 
					mail_box 
				WHERE 
					parent is not null group by parent) as t2 on (t1.id = t2.parent_id)
			LEFT JOIN
			 	(SELECT 
			 		a1.id, a1.date_received as estado_lida, a1.parent as pai2
			 	FROM 
			 		mail_box as a1
			 	JOIN 
			 		(SELECT 
			 			MAX(id) AS id
				  FROM 
				  	mail_box
			    WHERE 
			    	parent IS NOT NULL
			    GROUP BY 
			    	parent) AS grp
				   ON 
				   	grp.id = a1.id
				  WHERE receiver = ".$authNamespace->user_id.") AS t3 ON (t1.id = t3.pai2)
			ORDER BY date_sent DESC");
			return $aux->fetchAll();
	}

	public function verifyAccess($messageId)
	{
		$authNamespace = new Zend_Session_Namespace('userInformation');
		$messageStatus = new Application_Model_DbTable_MailBox();
		$messageStatusRow = $messageStatus->fetchRow($messageStatus->select()->where('id = ?',$messageId));
		if($messageStatusRow->sender = $authNamespace->user_id || $messageStatusRow->receiver = $authNamespace->user_id)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function listOutboxMessage()
	{
		$authNamespace = new Zend_Session_Namespace('userInformation');
		$registry = Zend_Registry::getInstance();
		$db = $registry->get('db');
		$aux = $db->query("
			SELECT 
				t1.id,
				t1.sender, 
				t1.title,
				t1.body,
				t2.contador, 
				IF(t1.sender= ".$authNamespace->user_id.", u1.name, u.name) AS name, 
				DATE_FORMAT(t1.date_sent,'%d/%m/%Y') AS date_aux,
				DATE_FORMAT(t1.date_received,'%d/%m/%Y') AS date_aux_received
			FROM 
				(
					SELECT 
						m.id, m.sender, m.receiver, m.title, m.body, m.date_sent, m.date_received
					FROM 
						mail_box m
					WHERE 
						m.parent IS NULL AND m.sender = ".$authNamespace->user_id." 
				UNION 
					SELECT 
						p.id, p.sender, p.receiver, p.title, p.body, p.date_sent, p.date_received
				 	FROM 
				 		mail_box p
					INNER JOIN 
						mail_box q ON (p.id = q.parent)
					WHERE 
					 	p.receiver =".$authNamespace->user_id."
				) AS t1 
			INNER JOIN 
				user u ON (u.id = t1.sender)
			INNER JOIN 
				user u1 ON (u1.id = t1.receiver)
			LEFT JOIN 
			(
				SELECT 
					parent AS parent_id, SUM(IF(parent IS NULL OR parent IS NOT NULL,1,0))+1 AS contador 
				FROM 
					mail_box 
				WHERE 
					parent IS NOT NULL 
				GROUP BY parent
			) AS t2 ON (t1.id = t2.parent_id)
			ORDER BY 
				date_sent DESC
			");
		return $aux->fetchAll();
		}

	public function getUnreadMessages()
	{
		$authNamespace = new Zend_Session_Namespace('userInformation');
		$registry = Zend_Registry::getInstance();
		$db = $registry->get('db');
		$aux = $db->query("
		SELECT t1.id, t1.date_received as estado_lida, t1.parent
		FROM mail_box as t1
		INNER JOIN (SELECT MAX(id) AS id
		      FROM mail_box
		      WHERE parent IS NOT NULL AND date_received IS NULL
		      GROUP BY parent) AS grp
		ON grp.id = t1.id
		WHERE receiver = ".$authNamespace->user_id."
		UNION
			 (SELECT id,date_received, parent
			 FROM mail_box
			 WHERE parent IS NULL AND date_received IS NULL AND sender != ".$authNamespace->user_id.")
		ORDER BY id ASC");
		return count($aux->fetchAll()); 

		// Adaptar para o zend, pesquisar sobre o union e join com subquerys e organizar
		// $authNamespace = new Zend_Session_Namespace('userInformation');
		// $messageStatus = new Application_Model_DbTable_MailBox();
		// $select->from(array('m' => 'mail_box'), array('estado_lida' => 'date_received', 'id', 'parent')
		// 			 ->join()	
		// 					;)
		// $messageCount = $messageStatus->fetchAll($messageStatus->select()
		// 																							->where('date_received IS NULL')
		// 																							->where('receiver = ?',$authNamespace->user_id));
	}

	public function findBySender($sender)
	{
		$authNamespace = new Zend_Session_Namespace('userInformation');
		$registry = Zend_Registry::getInstance();
		$db = $registry->get('db');
		$mail = $db->query("
			SELECT 
			t1.id,
			t1.sender, 
			t1.title,
			t1.body,
			DATE_FORMAT(t1.date_sent,'%d/%m/%Y') as date_aux, 
		 	DATE_FORMAT(t1.date_received,'%d/%m/%Y') as date_aux_received,
			u.name as name
			FROM mail_box as t1
			INNER JOIN user as u
			ON (t1.sender = u.id)
			WHERE u.name LIKE '%".$sender."%' 
			AND receiver =".$authNamespace->user_id);
		return $mail->fetchAll();
		// $mail= new Application_Model_DbTable_MailBox();
		// $authNamespace = new Zend_Session_Namespace('userInformation');
		// $select = $mail->select()->setIntegrityCheck(false);
		// $select ->from(array('u' => 'user', ) )
		// 				->joinInner(array('m' => 'mail_box'), 'u.id=m.sender', array('id', 'title', 'body', 'receiver', 
		// 					'title','date_sent', 'date_received'))
		// 				->where('u.name LIKE', '%' .$sender. '%')
		// 				->where('m.receiver ='.$authNamespace->user_id);
		// return $mail->fetchRow($select);
	}

	public function findByTitle($title)
	{
		// $mail = new Application_Model_DbTable_MailBox();
		// $authNamespace = new Zend_Session_Namespace('userInformation');
		// return $mail->fetchAll($mail->select()
		// 												->where('title LIKE ?', '%'.$title.'%')
		// 												->where('receiver = ?',$authNamespace->user_id) );
		$authNamespace = new Zend_Session_Namespace('userInformation');
		$registry = Zend_Registry::getInstance();
		$db = $registry->get('db');
		$mail = $db->query("
			SELECT 
			t1.id,
			t1.sender, 
			t1.title,
			t1.body,
			DATE_FORMAT(t1.date_sent,'%d/%m/%Y') as date_aux, 
		 	DATE_FORMAT(t1.date_received,'%d/%m/%Y') as date_aux_received,
			u.name as name
			FROM mail_box as t1
			INNER JOIN user as u
			ON (t1.sender = u.id)
			WHERE title LIKE '%".$title."%' 
			AND receiver =".$authNamespace->user_id);
		return $mail->fetchAll();

	}

	public function findByDate($date)
	{
		if(strlen($date) == '10'){
		$authNamespace = new Zend_Session_Namespace('userInformation');
		$registry = Zend_Registry::getInstance();
		$db = $registry->get('db');
		$mail = $db->query("
			SELECT 
			t1.id,
			t1.sender, 
			t1.title,
			t1.body,
			DATE_FORMAT(t1.date_sent,'%d/%m/%Y') as date_aux, 
		 	DATE_FORMAT(t1.date_received,'%d/%m/%Y') as date_aux_received,
			u.name as name
			FROM mail_box as t1
			INNER JOIN user as u
			ON (t1.sender = u.id)
			WHERE date_sent LIKE '%".Application_Model_General::dateToUs($date)."%' 
			AND receiver =".$authNamespace->user_id);
		return $mail->fetchAll();	
		}else{
			return array();
		}	
		// $mail= new Application_Model_DbTable_MailBox();
		// $authNamespace = new Zend_Session_Namespace('userInformation');
		// $select = $mail->select()->setIntegrityCheck(false);
		// $select ->from(array('m' => 'mail_box', ), array('id', 'sender', 'receiver', 
		// 					'title','date_sent'))
		// 				->where('m.date_sent LIKE ?', '%' .$date. '%')
		// 				->where('m.receiver ='.$authNamespace->user_id);
		// return $mail->fetchRow($select);
	}

public function findBySenderOut($sender)
	{
		$authNamespace = new Zend_Session_Namespace('userInformation');
		$registry = Zend_Registry::getInstance();
		$db = $registry->get('db');
		$mail = $db->query("
			SELECT 
			t1.id,
			t1.sender, 
			t1.title,
			t1.body,
			DATE_FORMAT(t1.date_sent,'%d/%m/%Y') as date_aux, 
		 	DATE_FORMAT(t1.date_received,'%d/%m/%Y') as date_aux_received,
			u.name as name
			FROM mail_box as t1
			INNER JOIN user as u
			ON (t1.receiver = u.id)
			WHERE u.name LIKE '%".$sender."%' 
			AND sender =".$authNamespace->user_id);
		return $mail->fetchAll();
		// $mail= new Application_Model_DbTable_MailBox();
		// $authNamespace = new Zend_Session_Namespace('userInformation');
		// $select = $mail->select()->setIntegrityCheck(false);
		// $select ->from(array('u' => 'user', ) )
		// 				->joinInner(array('m' => 'mail_box'), 'u.id=m.sender', array('id', 'title', 'body', 'receiver', 
		// 					'title','date_sent', 'date_received'))
		// 				->where('u.name LIKE', '%' .$sender. '%')
		// 				->where('m.receiver ='.$authNamespace->user_id);
		// return $mail->fetchRow($select);
	}

	public function findByTitleOut($title)
	{
		// $mail = new Application_Model_DbTable_MailBox();
		// $authNamespace = new Zend_Session_Namespace('userInformation');
		// return $mail->fetchAll($mail->select()
		// 												->where('title LIKE ?', '%'.$title.'%')
		// 												->where('receiver = ?',$authNamespace->user_id) );
		$authNamespace = new Zend_Session_Namespace('userInformation');
		$registry = Zend_Registry::getInstance();
		$db = $registry->get('db');
		$mail = $db->query("
			SELECT 
			t1.id,
			t1.receiver, 
			t1.title,
			t1.body,
			DATE_FORMAT(t1.date_sent,'%d/%m/%Y') as date_aux, 
		 	DATE_FORMAT(t1.date_received,'%d/%m/%Y') as date_aux_received,
			u.name as name
			FROM mail_box as t1
			INNER JOIN user as u
			ON (t1.receiver = u.id)
			WHERE title LIKE '%".$title."%' 
			AND sender = ".$authNamespace->user_id);
		return $mail->fetchAll();

	}

	public function findByDateOut($date)
	{
		if(strlen($date) == '10'){
		$authNamespace = new Zend_Session_Namespace('userInformation');
		$registry = Zend_Registry::getInstance();
		$db = $registry->get('db');
		$mail = $db->query("
			SELECT 
			t1.id,
			t1.receiver, 
			t1.title,
			t1.body,
			DATE_FORMAT(t1.date_sent,'%d/%m/%Y') as date_aux, 
		 	DATE_FORMAT(t1.date_received,'%d/%m/%Y') as date_aux_received,
			u.name as name
			FROM mail_box as t1
			INNER JOIN user as u
			ON (t1.receiver = u.id)
			WHERE date_sent LIKE '%".Application_Model_General::dateToUs($date)."%' 
			AND sender = ".$authNamespace->user_id);
		return $mail->fetchAll();	
		}else{
			return array();
		}	
		// $mail= new Application_Model_DbTable_MailBox();
		// $authNamespace = new Zend_Session_Namespace('userInformation');
		// $select = $mail->select()->setIntegrityCheck(false);
		// $select ->from(array('m' => 'mail_box', ), array('id', 'sender', 'receiver', 
		// 					'title','date_sent'))
		// 				->where('m.date_sent LIKE ?', '%' .$date. '%')
		// 				->where('m.receiver ='.$authNamespace->user_id);
		// return $mail->fetchRow($select);
	}


} 