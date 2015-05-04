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
	*	Calculate the quantity of reservation validator (they have right of 5% from the fleet).
	*	
	* @access public
	* @return integer
	*/
	public function reservationValidator($consortium_company){
		$validator = new Application_Model_DbTable_Validator();

		$select = $validator->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'vehicle'),array('COUNT(id)' => 'count'))
				->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id',array('consortium_company'))
				->where('vh.consortium_company = ?',$consortium_company)
				->where('type = 1');
		$qtd = $validator->fetchRow($select);
		$allowed_qtd = ceil($qdt->count*0.05);

		$select = $validator->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'vehicle'),array('COUNT(id)' => 'count'))
				->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id',array('consortium_company'))
				->where('vh.consortium_company = ?',$consortium_company)
				->where('type = 2');
		$qtd = $validator->fetchRow($select);
		$real_qdt = $qtd->count;

		return 2;
	}

}