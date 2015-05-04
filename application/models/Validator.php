<?php

class Application_Model_Validator
{

	/**
	*	Register a new validator.
	*	
	* @param array $data - validator's data
	* @access public
	* @return integer
	*/
	public function newValidator($data)
	{
		$authNamespace = new Zend_Session_Namespace('userInformation');
		$validator = new Application_Model_DbTable_Validator();
		$validatorNew = $validator->createRow();
		$validatorNew->number = $data['number'];
		$validatorNew->serial = $data['serial'];
		$validatorNew->consortium = $authNamespace->consortium;
		$validatorNew->company = $authNamespace->company;
		$validatorNew->type = $data['type'];
		$validatorNew->status = 1;
		return $validatorNew->save();
	}

	/**
	*	Delete a validator.
	*	
	* @param int $id - validator's id
	* @access public
	* @return integer
	*/
	public function deleteValidator($id)
	{
		$validator = new Application_Model_DbTable_Validator();
		$validatorRow = $validator->fetchRow($validator->select()->where('id = ?',$id));
		if(count($validatorRow))
		{
			return $validatorRow->delete();
		}
		return false;
	}

	/**
	*	List all registered validators.
	*	
	* @access public
	* @return array
	*/
	public function listValidators(){
		$validator = new Application_Model_DbTable_Validator();

		$select = $validator->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'validator'),array('id','number','serial','type'));
		return $validator->fetchAll($select);
	}
	
	/**
	*	List the avaliable validators.
	*	
	* @access public
	* @return array
	*/
	public function listAvaliableValidators(){
		$authNamespace = new Zend_Session_Namespace('userInformation');
		$validator = new Application_Model_DbTable_Validator();

		$select = $validator->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'validator'),array('id','number','serial','type'))
				->where('v.consortium = ?',$authNamespace->consortium)
				->where('v.company = ?',$authNamespace->company)
				->where('v.status = 1');
		return $validator->fetchAll($select);
	}

	/**
	*	Calculate if is possibel make the registration of another reservation validator 
	*	(they have right of 5% from the fleet).
	*	
	* @access public
	* @return integer
	*/
	public function reservationValidator($consortium_company){
		$authNamespace = new Zend_Session_Namespace('userInformation');
		$validator = new Application_Model_DbTable_Validator();

		$select = $validator->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'vehicle'),array('id'))
				->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id',array('consortium_company'))
				->where('vh.consortium_company = ?',$consortium_company);
		$qtd = count($validator->fetchAll($select));
		$allowed_qtd = ceil($qtd*0.05);

		$select = $validator->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'validator'),array('id'))
				->where('v.consortium = ?',$authNamespace->consortium)
				->where('v.company = ?',$authNamespace->company)
				->where('type = 2');
		$qtd = count($validator->fetchAll($select));

		if($allowed_qtd < $qtd)
			return true;
		else
			return false;
		
	}

	/**
	*	Calculate if is possible registrate another main validator.
	*	
	* @access public
	* @return integer
	*/
	public function allowReg($consortium_company){
		$authNamespace = new Zend_Session_Namespace('userInformation');
		$validator = new Application_Model_DbTable_Validator();

		$select = $validator->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'vehicle'),array('id'))
				->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id',array('consortium_company'))
				->where('vh.consortium_company = ?',$consortium_company);
		$allowed_qtd = count($validator->fetchAll($select));

		$select = $validator->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'validator'),array('id'))
				->where('v.consortium = ?',$authNamespace->consortium)
				->where('v.company = ?',$authNamespace->company)
				->where('type = 1');
		$qtd = count($validator->fetchAll($select));

		if($allowed_qtd > $qtd)
			return true;
		else
			return false;
		
	}

	/**
	*	change status of validator.
	*	
	* @access public
	* @return integer
	*/
	public function changeStatus($validatorId,$status)
	{
		$validator = new Application_Model_DbTable_Validator();
		$validatorStatusRow = $validator->fetchRow($validator->select()->where('id = ?',$validatorId));
		$validatorStatusRow->status = $status;
		return $validatorStatusRow->save();
	}

}