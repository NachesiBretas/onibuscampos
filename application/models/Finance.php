<?php

class Application_Model_Finance
{

	public function listGroupFare()
	{
		$groupFare = new Application_Model_DbTable_FinanceFare();
		return $groupFare->fetchAll($groupFare->select()->order('value'));
	}

	public function newGroupFare($data)
	{
		$data['value'] = str_replace(',', '.', $data['value']);
		$data['date'] = new Zend_Db_Expr('NOW()');
		$groupFare = new Application_Model_DbTable_FinanceFare();
		$groupFareNew = $groupFare->createRow($data);
		return $groupFareNew->save();
	}

	public function editGroupFare($data,$id)
	{
		$data['value'] = str_replace(',', '.', $data['value']);
		$this->saveHistoricGroupFare($id,$data);
		unset($data['submit']);
		$data['date'] = new Zend_Db_Expr('NOW()');
		$groupFare = new Application_Model_DbTable_FinanceFare();
		$where = array('id = ?' => $id);
		return $groupFare->update($data,$where);
	}

	public function returnGroupFareById($id)
	{
		$groupFare = new Application_Model_DbTable_FinanceFare();
		$groupFareRow = $groupFare->fetchRow($groupFare->select()->where('id = ?',$id));
		$groupFareRow->value = number_format($groupFareRow->value,2,',','');
		return $groupFareRow;
	}

	protected function saveHistoricGroupFare($id,$data)
	{
    $authNamespace = new Zend_Session_Namespace('userInformation');
		$groupFare = new Application_Model_DbTable_FinanceFare();
		$groupFareRow = $groupFare->fetchRow($groupFare->select()->where('id = ?',$id));
		$groupFareHistoric = new Application_Model_DbTable_FinanceFareHistoric();
		$groupFareHistoricNew = $groupFareHistoric->createRow();
		$groupFareHistoricNew->finance_fare_id 		= $id;
		$groupFareHistoricNew->user_id 						= $authNamespace->user_id;
		$groupFareHistoricNew->date 							= new Zend_Db_Expr('NOW()');
		$groupFareHistoricNew->start_date 				= $groupFareRow->date;
		$groupFareHistoricNew->end_date 					= new Zend_Db_Expr('NOW()');
		$groupFareHistoricNew->name 							= $groupFareRow->name;
		$groupFareHistoricNew->value 							= $groupFareRow->value;
		return $groupFareHistoricNew->save();
	}

	public function returnHistoricGroupFareById($id)
	{
		$groupFareHistoric = new Application_Model_DbTable_FinanceFareHistoric();
		return $groupFareHistoric->fetchAll($groupFareHistoric->select()->where('finance_fare_id = ?',$id));
	}
}

