<?php

class Application_Model_Vehicle
{
	/**
	*	Register a new vehicle on the system.
	*	
	*	@param array $data - vehicle's data
	* @access public
	* @return integer
	*/
	public function newVehicle($data)
	{
		$vehicle = new Application_Model_DbTable_Vehicle();
		if(isset($data['start_date']) && $data['start_date'] != '') 
			$data['start_date'] = Application_Model_General::dateToUs($data['start_date']);
		else
			$data['start_date'] = new Zend_Db_Expr('NOW()');
		$data['date'] = new Zend_Db_Expr('NOW()');
		$data['plate'] = strtoupper($data['plate']);
		$data['floor'] = 1;
		$vehicleNew = $vehicle->createRow($data);
		$id = $vehicleNew->save();

		$this->newVehicleNew($id);
		return $id;
	}

	/**
	*	Register a new vehicle on vehicle new.
	*	
	*	@param array $vehicle_id - vehicle's id
	* @access public
	* @return integer
	*/
	public function newVehiclenew($vehicle_id)
	{
		$authNamespace = new Zend_Session_Namespace('userInformation');
		$vehicle = new Application_Model_DbTable_VehicleNew();
		$data['date'] = new Zend_Db_Expr('NOW()');
		$data['vehicle_id'] = $vehicle_id;
		$data['status'] = 1;
		$data['user_id'] = $authNamespace->user_id;
		$vehicleNew = $vehicle->createRow($data);
		
		return $vehicleNew->save();
	}

/**
	*	Return if the vehicle register is new.
	*	
	*	@param array $vehicle_id - vehicle's id
	* @access public
	* @return integer
	*/
	public function returnVehicleNew($vehicle_id)
	{
		$vehicle = new Application_Model_DbTable_VehicleNew();
		$vehicleRow = $vehicle->fetchRow($vehicle->select()->where('vehicle_id = ?',$vehicle_id)
														   ->where('status = 1'));
		return $vehicleRow;
	}

	/**
	*	Return status of a new vehicle. If instituition would be consortium, return 1 (registered). If not, return 4 (running).
	*
	*	@access private
	*	@return integer
	*/
	private function returnStatusNewVehicle()
	{
    $authNamespace = new Zend_Session_Namespace('userInformation');
		if($authNamespace->institution == 3)
		{
			return 1;
		}
		return 4; 
	}

	/**
	*	Save a new status into vehicle.
	*
	*	@param integer $vehicleId - vehicle's id
	* @param integer $userId - person who are registering
	* @param integer $status - vehicle's status
	*	@access public
	*	@return integer
	*/
	public function newStatus($vehicleId,$userId,$status=1)
	{
		$vehicleStatus = new Application_Model_DbTable_VehicleStatus();
		$vehicleStatusNew = $vehicleStatus->createRow();
		$vehicleStatusNew->date = new Zend_Db_Expr('NOW()');
		$vehicleStatusNew->vehicle_id = $vehicleId;
		$vehicleStatusNew->user_id = $userId;
		$vehicleStatusNew->status = $this->returnStatusNewVehicle();
		return $vehicleStatusNew->save();
	}

	/**
	*	Edit vehicle's data.
	*
	* @param array $data - vehicle's data
	* @param integer $vehicleId - vehicle's id
	* @access public
	*	@return boolean 
	*/
	public function editVehicle($data,$vehicleId)
	{
		if(isset($data['service']))
		{
			if($this->saveMain($data,$vehicleId) == 1){
				$this->newCRV($vehicleId);
				return true;
			}
			else if($this->saveMain($data,$vehicleId) == 2){
				return true;
			}
		}
		if(isset($data['chassi_number']))
		{
			if($this->saveBody($data,$vehicleId) == 1){
				$this->newCRV($vehicleId);
				return true;
			}
			else if($this->saveBody($data,$vehicleId) == 2){
				return true;
			}
		}
		if(isset($data['weight']))
		{
			if($this->saveMeasures($data,$vehicleId)){
				return true;
			}
		}
		if(isset($data['elevator']))
		{
			if($this->saveOther($data,$vehicleId) == 1){
				$this->newCRV($vehicleId);
				return true;
			}
			else if($this->saveOther($data,$vehicleId) == 2){
				return true;
			}
		}
		if(isset($data['external_number']))
		{
			if($this->saveHistoric($data,$vehicleId,$data['id']) == 1){
				$this->newCRV($vehicleId);
				return true;
			}
			else if($this->saveHistoric($data,$vehicleId,$data['id']) == 2){
				return true;
			}
		}
		if(isset($data['seal_roulette_old']))
		{
			if($this->saveSeal($data,$vehicleId) == 1){
				$this->newCRV($vehicleId);
				return true;
			}
			else if($this->saveSeal($data,$vehicleId) == 2){
				return true;
			}
		}
		if(isset($data['date_inspection']))
		{
			if($this->saveInspection($data,$vehicleId))
				return true;
		}
		if(isset($data['date_notification']))
		{
			if($this->saveNotification($data,$vehicleId))
				return true;
		}
		return false;
	}

	/**
	*	Return tab of forms.
	*
	*	@param integer $vehicleId - vehicle's id
	*	@access public
	*	@return integer
	*/
	public function returnTab($vehicleId)
	{
		$mechanics = new Application_Model_DbTable_VehicleMechanics();
		$mechanicsRow = $mechanics->fetchRow($mechanics->select()->where('id = ?',$vehicleId));
		if(!$mechanicsRow)
			return 2;
		$measures = new Application_Model_DbTable_VehicleMeasures();
		$measuresRow = $measures->fetchRow($measures->select()->where('id = ?',$vehicleId));
		if(!$measuresRow)
			return 3;
		$other = new Application_Model_DbTable_VehicleOther();
		$otherRow = $other->fetchRow($other->select()->where('id = ?',$vehicleId));
		if(!$otherRow)
			return 4;
		$historic = new Application_Model_DbTable_VehicleHistoric();
		$historicRow = $historic->fetchAll($historic->select()->where('vehicle_id = ?',$vehicleId));
		if(!count($historicRow))
			return 5;
		return 1;
	}

	/**
	*	Edit a main data of vehicle.
	*
	*	@param array $data - main's data of vehicle
	*	@param integer $vehicleId - vehicle's id
	*	@access protected
	*	@return integer
	*/
	protected function saveMain($data,$vehicleId)
	{
		$vehicle = new Application_Model_DbTable_Vehicle();
		$vehicleRow = $vehicle->fetchRow($vehicle->select()->where('id = ?',$vehicleId));
		$change_crv = 2;
		if($vehicleRow)
		{
			if($vehicleRow->plate != $data['plate'] || $vehicleRow->renavam != $data['renavam'] || $vehicleRow->color != $data['color']){
				$change1=1;
			}
			else{
				$change1=2;
			}
				$vehicleRow->start_date = Application_Model_General::dateToUs($data['start_date']);
				$vehicleRow->service = $data['service'];
				$vehicleRow->plate = $data['plate'];
				$vehicleRow->renavam = $data['renavam'];
				$vehicleRow->pattern = $data['pattern'];
				$vehicleRow->color = $data['color'];
				$vehicleRow->type = $data['type'];
				$vehicleRow->floor = 1; //$data['floor'];
				$edit = $vehicleRow->save();

				if($edit){
					if($change1 == 1){
						return $change_crv = 1;
					}		 	
				}
		}
		return false;
	}

	/**
	*	Edit a body data of vehicle.
	*
	*	@param array $data - bodie's data of vehicle
	*	@param integer $vehicleId - vehicle's id
	*	@access protected
	* @return integer
	*/
	protected function saveBody($data,$vehicleId)
	{
		try{
			$vehicle = new Application_Model_DbTable_VehicleMechanics();
			$vehicleRow = $vehicle->fetchRow($vehicle->select()->where('id = ?',$vehicleId));
			$change_crv=2;
			if(!$vehicleRow)
			{
				$vehicleRow = $vehicle->createRow();
				$vehicleRow->id = $vehicleId;
			}
			if($vehicleRow->chassi_number != $data['chassi_number'] || $vehicleRow->chassi_model != $data['chassi_model'] || $vehicleRow->chassi_year != $data['chassi_year'] || $vehicleRow->body_model != $data['body_model'] || $vehicleRow->body_year != $data['body_year']){
				$change_crv=1;
			}
			$vehicleRow->chassi_number = $data['chassi_number'];
			$vehicleRow->chassi_model = $data['chassi_model'];
			$vehicleRow->chassi_year = $data['chassi_year'];
			$vehicleRow->body_model = $data['body_model'];
			$vehicleRow->body_year = $data['body_year'];
			$vehicleRow->suspension = $data['suspension'];
			$vehicleRow->cambium = $data['cambium'];
			$vehicleRow->motor_localization = $data['motor_localization'];
			$vehicleRow->motor_power = $data['motor_power'];
			$vehicleRow->seat_type = $data['seat_type'];
			$vehicleRow->save();

			return $change_crv;
		}catch(Zend_Exception $e){
			return false;
		}
	}

	/**
	*	Edit a measures data of vehicle.
	*
	*	@param array $data - measures data of vehicle
	* @param integer $vehicleId - vehicle's id
	* @access protected
	* @return integer
	*/
	protected function saveMeasures($data,$vehicleId)
	{
		try{
			$vehicle = new Application_Model_DbTable_VehicleMeasures();
			$vehicleRow = $vehicle->fetchRow($vehicle->select()->where('id = ?',$vehicleId));
			if(!$vehicleRow)
			{
				$vehicleRow = $vehicle->createRow();
				$vehicleRow->id = $vehicleId;
			}
			$vehicleRow->weight = $data['weight'];
			$vehicleRow->length_before_roulette = $data['length_before_roulette'];
			$vehicleRow->length_after_roulette = $data['length_after_roulette'];
			$vehicleRow->width_before_roulette = $data['width_before_roulette'];
			$vehicleRow->width_after_roulette = $data['width_after_roulette'];
			$vehicleRow->width_door_front_right = $data['width_door_front_right'];
			$vehicleRow->width_door_middle1_right = $data['width_door_middle1_right'];
			$vehicleRow->width_door_middle2_right = $data['width_door_middle2_right'];
			$vehicleRow->width_door_back_right = $data['width_door_back_right'];
			$vehicleRow->width_door_front_left = $data['width_door_front_left'];
			$vehicleRow->width_door_middle1_left = $data['width_door_middle1_left'];
			$vehicleRow->width_door_middle2_left = $data['width_door_middle2_left'];
			$vehicleRow->width_door_back_left = $data['width_door_back_left'];
			$vehicleRow->length_deficient = $data['length_deficient'];
			$vehicleRow->width_deficient = $data['width_deficient'];
			$vehicleRow->tank1 = $data['tank1'];
			$vehicleRow->tank2 = $data['tank2'];
			$vehicleRow->amount_seats = $data['amount_seats'];
			return $vehicleRow->save();
		}catch(Zend_Exception $e){
			return false;
		}
	}

	/**
	*	Edit an another data of vehicle.
	*
	*	@param array $data - other data of vehicle
	*	@param integer $vehicleID - vehicle's id
	*	@access protected
	*	@return integer
	*/
	protected function saveOther($data,$vehicleId)
	{
		try{
			$vehicle = new Application_Model_DbTable_VehicleOther();
			$vehicleRow = $vehicle->fetchRow($vehicle->select()->where('id = ?',$vehicleId));
			if(!$vehicleRow)
			{
				$vehicleRow = $vehicle->createRow();
				$vehicleRow->id = $vehicleId;
			}

			if($vehicleRow->seal_roulette != $data['seal_roulette'] || $vehicleRow->seal_floor != $data['seal_floor'] || $vehicleRow->seal_support != $data['seal_support'] || $vehicleRow->collector_area != $data['collector_area']){
				$change_crv=1;
			}
			else{
				$change_crv=2;
			}

			$vehicleRow->elevator = $data['elevator'];
			$vehicleRow->elevator_date = Application_Model_General::dateToUs($data['elevator_date']);
			if(isset($data['seal_roulette']) && $data['seal_roulette'] != '') $vehicleRow->seal_roulette = $data['seal_roulette'];
			if(isset($data['seal_floor']) && $data['seal_floor'] != '') $vehicleRow->seal_floor = $data['seal_floor'];
			if(isset($data['seal_support']) && $data['seal_support'] != '') $vehicleRow->seal_support = $data['seal_support'];
			if(isset($data['seal_date']) && $data['seal_date'] != '') $vehicleRow->seal_date = Application_Model_General::dateToUs($data['seal_date']);
			$vehicleRow->insurer = $data['insurer'];
			$vehicleRow->insurer_date = Application_Model_General::dateToUs($data['insurer_date']);
			$vehicleRow->eletronic_roulette = $data['eletronic_roulette'];
			$vehicleRow->collector_area = $data['collector_area'];
			$vehicleRow->air_conditioning = $data['air_conditioning'];
			$vehicleRow->gps = $data['gps'];
			$vehicleRow->wifi = $data['wifi'];
			$vehicleRow->bike_support = $data['bike_support'];
			$vehicleRow->tv = $data['tv'];
			$vehicleRow->camera = $data['camera'];
			$vehicleRow->amount_validator = $data['amount_validator'];
			$vehicleRow->save();
			return $change_crv;
		}catch(Zend_Exception $e){
			return false;
		}
	}

	/**
	*	Save historic's data of vehicle.
	*
	*	@param array $date - historic data of vehicle
	*	@param integer $vehicleId - vehicle's id
	*	@param integer $historicId - historic's id of vehicle
	*	@access public
	*	@return integer
	*/
	public function saveHistoric($data,$vehicleId,$historicId='')
	{
		try{
			$vehicle = new Application_Model_DbTable_VehicleHistoric();
			$vehicleRow = $vehicle->fetchRow($vehicle->select()->where('id = ?',$historicId));
			$change_crv = 2;
			if(!$vehicleRow){
				$vehicleRow = $vehicle->createRow();
				$vehicleRow->vehicle_id = $vehicleId;
			}

			$authNamespace = new Zend_Session_Namespace('userInformation');
			if($authNamespace->institution == 3){
				if(isset($data['consortium']) && $data['consortium'] != '')
				{
					if($vehicleRow->consortium != $data['consortium']){
						$change2=1;
					}
					else{
						$change2=2;
					}
					$vehicleRow->consortium = $data['consortium'];
				}
				else
				{
					$vehicleRow->consortium = $authNamespace->consortium;
				}
				$consortium_company = $this->consortiumCompany($authNamespace->consortium,$authNamespace->company);
				$vehicleRow->consortium_company = $consortium_company->id;
			}
			else{
				$vehicleRow->consortium = $data['consortium'];
				$vehicleRow->consortium_company = $data['consortium_company'];
			}
			$vehicleRow->vehicle_id = $data['vehicle_id'];
			$last_seq = $this->returnENSeq();
			if(!$last_seq){
				$seq = '001';
			}
			elseif(substr($last_seq->lastSeq,0,1) == '0' && substr($last_seq->lastSeq,1,1) == '0')
				$seq = '00'.strval(substr($last_seq->lastSeq,2,1)+1);
			elseif(substr($last_seq->lastSeq,0,1) == '0' && substr($last_seq->lastSeq,1,1) != '0')
				$seq = '0'.strval(substr($last_seq->lastSeq,-2)+1);
			else
				$seq = $last_seq->lastSeq+1;
			$external_number = $authNamespace->consortium.$authNamespace->company.$seq;
			$vehicleRow->external_number = $external_number;
			$vehicleRow->validator_id = $data['validator'];
			if(isset($data['authorization'])) $vehicleRow->authorization = $data['authorization'];
			if(isset($data['start_historic_date'])) $vehicleRow->start_historic_date = Application_Model_General::dateToUs($data['start_historic_date']);
			if(isset($data['end_historic_date'])) $vehicleRow->end_historic_date = Application_Model_General::dateToUs($data['end_historic_date']);
			$vehicleHistoricId = $vehicleRow->save();
			if($vehicleHistoricId){
				$validator = new Application_Model_Validator();
        		$validator->changeStatus($data['validator'],2);
			}

			if($change1 == 1 || $change2 == 1){
				$change_crv = 1;
			}
			return $change_crv;
		}catch(Zend_Exception $e){
			return false;
		}
	}
	public function returnENSeq()
	{
		$vehicle = new Application_Model_DbTable_VehicleHistoric();
		$select = $vehicle->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'vehicle_historic'),array('substr(v.external_number,-3) as lastSeq'))
 								->order('v.id DESC');
		return $vehicle->fetchRow($select);
	}

	public function saveLog($vehicleId, $status,$data)
	{
  	$authNamespace = new Zend_Session_Namespace('userInformation'); 
		$vehicle = new Application_Model_DbTable_VehicleLog();
		$vehicleNew = $vehicle->createRow();
		$vehicleNew->user = $authNamespace->user_id;
		$vehicleNew->date = new Zend_Db_Expr('NOW()');
		$vehicleNew->vehicle_id = $vehicleId;
		$vehicleNew->status = $status;
		$vehicleNew->start_date = Application_Model_General::dateToUs($data['start_date']);
		$vehicleNew->service = $data['service'];
		$vehicleNew->plate = $data['plate'];
		$vehicleNew->renavam = $data['renavam'];
		$vehicleNew->external_number = $data['external_number'];
		$vehicleNew->pattern = $data['pattern'];
		$vehicleNew->color = $data['color'];
		$vehicleNew->type = $data['type'];
		$vehicleNew->floor = 1;
		return $vehicleNew->save();
	}

	public function returnById($vehicleId)
	{
		$vehicle = new Application_Model_DbTable_Vehicle();
		$select = $vehicle->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
																						'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor') )
						->joinLeft(array('m' => 'vehicle_mechanics'),'m.id=v.id')
						->joinLeft(array('me' => 'vehicle_measures'),'me.id=v.id')
						->joinLeft(array('mo' => 'vehicle_other'),'mo.id=v.id', array(
															'elevator_date' => new Zend_Db_Expr ('DATE_FORMAT(elevator_date,"%d/%m/%Y")'),
															'seal_date' => new Zend_Db_Expr ('DATE_FORMAT(seal_date,"%d/%m/%Y")') ,
															'insurer_date'  => new Zend_Db_Expr ('DATE_FORMAT(insurer_date,"%d/%m/%Y")'),
															'elevator','seal_roulette','seal_floor','seal_support','insurer','eletronic_roulette','collector_area',
															'air_conditioning','gps','wifi','bike_support','tv','camera','amount_validator'))
						->joinLeft(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id AND end_historic_date IS NULL', array('external_number','validator_id','end_historic_date'))
						->joinLeft(array('va' => 'validator'),'va.id=vh.validator_id',array('number'))
						->joinLeft(array('c' => 'consortium'),'c.id=vh.consortium',array('consortium_name' => 'name'))
						->joinLeft(array('co' => 'consortium_companies'),'co.id=vh.consortium_company',array('cell_name' => 'name'))
						->joinLeft(array('com' => 'company'),'com.id=co.company',array('company_name' => 'company'))
						->where('v.id = ?',$vehicleId);
		return $vehicle->fetchRow($select);
	}

	public function returnInactiveById($vehicleId)
	{
		$vehicle = new Application_Model_DbTable_Vehicle();
		$select = $vehicle->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
																						'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor') )
						->joinLeft(array('m' => 'vehicle_mechanics'),'m.id=v.id')
						->joinLeft(array('me' => 'vehicle_measures'),'me.id=v.id')
						->joinLeft(array('mo' => 'vehicle_other'),'mo.id=v.id', array(
															'elevator_date' => new Zend_Db_Expr ('DATE_FORMAT(elevator_date,"%d/%m/%Y")'),
															'seal_date' => new Zend_Db_Expr ('DATE_FORMAT(seal_date,"%d/%m/%Y")') ,
															'insurer_date'  => new Zend_Db_Expr ('DATE_FORMAT(insurer_date,"%d/%m/%Y")'),
															'elevator','seal_roulette','seal_floor','seal_support','insurer','eletronic_roulette','collector_area',
															'air_conditioning','gps','wifi','bike_support','tv','camera','amount_validator'))
						->joinLeft(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id', array('external_number', 'end_historic_date'))
						->joinLeft(array('c' => 'consortium'),'c.id=vh.consortium',array('consortium_name' => 'name'))
						->joinLeft(array('co' => 'consortium_companies'),'co.id=vh.consortium_company',array('cell_name' => 'name'))
						->joinLeft(array('com' => 'company'),'com.id=co.company',array('company_name' => 'company'))
						->where('v.id = ?',$vehicleId);
		return $vehicle->fetchRow($select);
	}

	public function returnByHistoricId($historicId)
	{
		$vehicle = new Application_Model_DbTable_Vehicle();
		$select = $vehicle->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
																						'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor') )
						->joinLeft(array('m' => 'vehicle_mechanics'),'m.id=v.id')
						->joinLeft(array('me' => 'vehicle_measures'),'me.id=v.id')
						->joinLeft(array('mo' => 'vehicle_other'),'mo.id=v.id', array(
															'elevator_date' => new Zend_Db_Expr ('DATE_FORMAT(elevator_date,"%d/%m/%Y")'),
															'seal_date' => new Zend_Db_Expr ('DATE_FORMAT(seal_date,"%d/%m/%Y")') ,
															'insurer_date'  => new Zend_Db_Expr ('DATE_FORMAT(insurer_date,"%d/%m/%Y")'),
															'elevator','seal_roulette','seal_floor','seal_support','insurer','eletronic_roulette','collector_area',
															'air_conditioning','gps','wifi','bike_support','tv','camera','amount_validator'))
						->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id', array('external_number', 'start_historic_date', 'end_historic_date', 'consortium'))
						->joinInner(array('co' => 'consortium'),'co.id=vh.consortium',array('consortium_name' => 'name' ))
						->joinInner(array('cc' => 'consortium_companies'),'cc.id=vh.consortium_company', array())
						->joinInner(array('c' => 'company'),'c.id=cc.company',array('company_name' => 'company' ))
						->joinInner(array('vs' => 'vehicle_service'),'vs.id=v.service',array('service_name' => 'name' ))
						->joinInner(array('vp' => 'vehicle_pattern'),'vp.id=v.pattern',array('pattern_name' => 'name' ))
						->joinInner(array('vt' => 'vehicle_type'),'vt.id=v.type',array('type_name' => 'name' ))
						->joinInner(array('col' => 'vehicle_color'),'col.id=v.color',array('color_name' => 'name' ))
						->joinInner(array('vc' => 'vehicle_chassi'),'vc.id=m.chassi_model',array('chassi_model_name' => 'name' ))
						->joinInner(array('vb' => 'vehicle_body'),'vb.id=m.body_model',array('body_model_name' => 'name' ))
						->joinInner(array('vss' => 'vehicle_suspension'),'vss.id=m.suspension',array('suspension_name' => 'name' ))
						->joinInner(array('vcc' => 'vehicle_cambium'),'vcc.id=m.cambium',array('cambium_name' => 'name' ))
						->joinInner(array('vmo' => 'vehicle_motor'),'vmo.id=m.motor_localization',array('motor_localization_name' => 'name' ))
						->joinInner(array('vse' => 'vehicle_seat'),'vse.id=m.seat_type',array('seat_type_name' => 'name' ))
						->where('vh.id = ?',$historicId);
		return $vehicle->fetchRow($select);
	}

	public function returnHistoric($vehicleId)
	{
		$vehicle = new Application_Model_DbTable_VehicleHistoric();
		$select = $vehicle->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'vehicle_historic'),array(
															'id', 'vehicle_id', 'consortium', 'consortium_company', 'external_number','validator_id','authorization',
															'start_historic_date' => new Zend_Db_Expr ('DATE_FORMAT(start_historic_date,"%d/%m/%Y")'), 
															'end_historic_date' => new Zend_Db_Expr ('DATE_FORMAT(end_historic_date,"%d/%m/%Y")'),
															'consortium_companies_hidden' => 'consortium_company'))
						->where('v.vehicle_id = ?',$vehicleId);
		return $vehicle->fetchAll($select);
	}

	public function lists()
	{
    $authNamespace = new Zend_Session_Namespace('userInformation');
    $institution = $authNamespace->institution;
    $consortium = $authNamespace->consortium;
    $company = $authNamespace->company;
		if($institution == 3)
		{
			if($company)
			{
				$vehicle = new Application_Model_DbTable_Vehicle();
				$select = $vehicle->select()->setIntegrityCheck(false);
				$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
																								'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor', 'vehicle_id' => 'id') )
								->joinInner(array('m' => 'vehicle_status'),'m.vehicle_id=v.id',array('status'))
								->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id AND vh.end_historic_date IS NULL', array('historic_id' => 'id','external_number', 'end_historic_date'))
								->joinInner(array('cc' => 'consortium_companies'),'cc.id=vh.consortium_company', array())
								->joinInner(array('c' => 'company'),'c.id=cc.company', array())
								->where('cc.company = ?',$company)
								->where('m.status = 4 OR m.status = 1 OR m.status = 8')
								->order('vh.start_historic_date DESC')
								->group('v.id');
				return $vehicle->fetchAll($select);
			}
			else
			{
				$vehicle = new Application_Model_DbTable_Vehicle();
				$select = $vehicle->select()->setIntegrityCheck(false);
				$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
																								'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor', 'vehicle_id' => 'id') )
								->joinLeft(array('m' => 'vehicle_status'),'m.vehicle_id=v.id',array('status'))
								->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id', array('historic_id' => 'id','external_number', 'end_historic_date'))
								->where('vh.consortium = ?',$consortium)
								->where('m.status = 4');
				return $vehicle->fetchAll($select);
			}
		}
		else
		{
			$vehicle = new Application_Model_DbTable_Vehicle();
			$select = $vehicle->select()->setIntegrityCheck(false);
			$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
														 'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor', 'vehicle_id' => 'id') )
							->joinLeft(array('m' => 'vehicle_status'),'m.vehicle_id=v.id',array('status'))
							->joinLeft(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id', array('historic_id' => 'id','external_number', 'end_historic_date'));
			return $vehicle->fetchAll($select);
		}
	}

	public function returnByPlate($plate)
	{
    $authNamespace = new Zend_Session_Namespace('userInformation');
    $institution = $authNamespace->institution;
    $company = $authNamespace->company;
		if($institution == 3)
		{
			if($company)
			{
				$vehicle = new Application_Model_DbTable_Vehicle();
				$select = $vehicle->select()->setIntegrityCheck(false);
				$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
																								'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor', 'vehicle_id' => 'id') )
								->joinLeft(array('m' => 'vehicle_status'),'m.vehicle_id=v.id',array('status'))
								->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id AND vh.end_historic_date IS NULL', array('historic_id' => 'id','external_number', 'end_historic_date'))
								->joinInner(array('cc' => 'consortium_companies'),'cc.id=vh.consortium_company', array())
								->joinInner(array('c' => 'company'),'c.id=cc.company', array())
								->where('cc.company = ?',$company)
								->where('m.status = 4')
								->where('v.plate LIKE ?','%'.$plate.'%');
				return $vehicle->fetchAll($select);
			}
			else
			{
				$vehicle = new Application_Model_DbTable_Vehicle();
				$select = $vehicle->select()->setIntegrityCheck(false);
				$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
																								'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor', 'vehicle_id' => 'id') )
								->joinLeft(array('m' => 'vehicle_status'),'m.vehicle_id=v.id')
								->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id', array('historic_id' => 'id','external_number', 'end_historic_date'))
								->where('vh.consortium = ?',$authNamespace->consortium)
								->where('m.status = 4')
								->where('v.plate LIKE ?','%'.$plate.'%');
				return $vehicle->fetchAll($select);
			}
		}
		else
		{
			$vehicle = new Application_Model_DbTable_Vehicle();
			$select = $vehicle->select()->setIntegrityCheck(false);
			$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
														 'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor', 'vehicle_id' => 'id') )
							->joinLeft(array('m' => 'vehicle_status'),'m.vehicle_id=v.id',array('status'))
							->joinLeft(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id', array('historic_id' => 'id','external_number', 'end_historic_date'))
							->where('plate LIKE ?','%'.$plate.'%');
			return $vehicle->fetchAll($select);
		}
	}

	public function returnByRenavam($renavam)
	{
    $authNamespace = new Zend_Session_Namespace('userInformation');
    $institution = $authNamespace->institution;
    $company = $authNamespace->company;
		if($institution == 3)
		{
			if($company)
			{
				$vehicle = new Application_Model_DbTable_Vehicle();
				$select = $vehicle->select()->setIntegrityCheck(false);
				$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
																								'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor', 'vehicle_id' => 'id') )
								->joinLeft(array('m' => 'vehicle_status'),'m.vehicle_id=v.id',array('status'))
								->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id AND vh.end_historic_date IS NULL', array('historic_id' => 'id','external_number', 'end_historic_date'))
								->joinInner(array('cc' => 'consortium_companies'),'cc.id=vh.consortium_company', array())
								->joinInner(array('c' => 'company'),'c.id=cc.company', array())
								->where('cc.company = ?',$company)
								->where('m.status = 4')
								->where('v.renavam LIKE ?','%'.$renavam.'%');;
				return $vehicle->fetchAll($select);
			}
			else
			{
				$vehicle = new Application_Model_DbTable_Vehicle();
				$select = $vehicle->select()->setIntegrityCheck(false);
				$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
																								'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor', 'vehicle_id' => 'id') )
								->joinLeft(array('m' => 'vehicle_status'),'m.vehicle_id=v.id')
								->joinLeft(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id', array('historic_id' => 'id','external_number', 'end_historic_date'))
								->where('vh.consortium = ?',$authNamespace->consortium)
								->where('m.status = 4')
								->where('v.renavam LIKE ?','%'.$renavam.'%');
				return $vehicle->fetchAll($select);
			}
		}
		else
		{
			$vehicle = new Application_Model_DbTable_Vehicle();
			$select = $vehicle->select()->setIntegrityCheck(false);
			$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
														 'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor', 'vehicle_id' => 'id') )
							->joinLeft(array('m' => 'vehicle_status'),'m.vehicle_id=v.id',array('status'))
							->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id', array('historic_id' => 'id','external_number', 'end_historic_date'))
							->where('renavam LIKE ?','%'.$renavam.'%');
			return $vehicle->fetchAll($select);
		}
	}


	public function returnByExternalNumber($externalNumber)
	{
    $authNamespace = new Zend_Session_Namespace('userInformation');
    $institution = $authNamespace->institution;
    $company = $authNamespace->company;
		if($institution == 3)
		{
			if($company)
			{
				$vehicle = new Application_Model_DbTable_Vehicle();
				$select = $vehicle->select()->setIntegrityCheck(false);
				$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
																								'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor', 'vehicle_id' => 'id') )
								->joinLeft(array('m' => 'vehicle_status'),'m.vehicle_id=v.id',array('status'))
								->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id', array('historic_id' => 'id','external_number', 'end_historic_date'))
								->joinInner(array('cc' => 'consortium_companies'),'cc.id=vh.consortium_company', array())
								->joinInner(array('c' => 'company'),'c.id=cc.company', array())
								->where('cc.company = ?',$company)
								->where('vh.external_number = ?',$externalNumber)
								->group('vh.external_number');
				return $vehicle->fetchAll($select);
			}
			else
			{
				$vehicle = new Application_Model_DbTable_Vehicle();
				$select = $vehicle->select()->setIntegrityCheck(false);
				$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
															 'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor', 'vehicle_id' => 'id') )
								->joinLeft(array('m' => 'vehicle_status'),'m.vehicle_id=v.id')
								->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id', array('historic_id' => 'id','external_number', 'end_historic_date'))
								->where('vh.consortium = ?',$authNamespace->consortium)
								->where('m.status = 4')
								->where('vh.external_number = ?',$externalNumber);
				return $vehicle->fetchAll($select);
			}
		}
		else
		{
			$vehicle = new Application_Model_DbTable_Vehicle();
			$select = $vehicle->select()->setIntegrityCheck(false);
			$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
														 'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor', 'vehicle_id' => 'id') )
							->joinLeft(array('m' => 'vehicle_status'),'m.vehicle_id=v.id',array('status'))
							->joinLeft(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id', array('historic_id' => 'id', 'external_number', 'end_historic_date'))
							->where('vh.external_number = ?',$externalNumber);
			return $vehicle->fetchAll($select);
		}
	}

	public function removeHistoric($historicId,$vehicleId)
	{
    $authNamespace = new Zend_Session_Namespace('userInformation');
    $institution = $authNamespace->institution;
		if($institution == 3)
		{
			return false;
		}
		else
		{
			$vehicle = new Application_Model_DbTable_VehicleHistoric();
			$vehicleRow = $vehicle->fetchRow($vehicle->select()->where('id = ?', $historicId));
			return $vehicleRow->delete();
		}
	}

	public function reviews()
	{
		$vehicle = new Application_Model_DbTable_Vehicle();
		$select = $vehicle->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'vehicle'),array('vehicle_id' => 'id', 'plate', 'renavam') )
						->joinInner(array('vq' => 'vehicle_status'),'vq.vehicle_id=v.id',array('status_id' => 'id', 'status') )
						->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id AND vh.end_historic_date IS NULL',array('historic_id' => 'id',
																'external_number') )
						->joinInner(array('c' => 'consortium'),'c.id=vh.consortium',array('consortium_name' => 'name') )
						->joinInner(array('u' => 'user'),'u.id=vq.user_id',array('name'))
						->where('vq.status = 3');
		return $vehicle->fetchAll($select);
	}

	public function reviewsEdited()
	{
		$vehicle = new Application_Model_DbTable_Vehicle();
		$select = $vehicle->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'vehicle'),array('vehicle_id' => 'id', 'plate', 'renavam') )
						->joinInner(array('vq' => 'vehicle_status'),'vq.vehicle_id=v.id',array('status_id' => 'id', 'status') )
						->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id AND vh.end_historic_date IS NULL',array('historic_id' => 'id',
																'external_number') )
						->joinInner(array('c' => 'consortium'),'c.id=vh.consortium',array('consortium_name' => 'name') )
						->joinInner(array('u' => 'user'),'u.id=vq.user_id',array('name'))
						->joinInner(array('ve' => 'vehicle_edited'),'ve.vehicle_id=v.id')
						->group('v.id')
						->where('ve.need_crv != 1')
						->where('ve.edited = 1');
		return $vehicle->fetchAll($select);
	}

	public function returnHistorics($vehicleId)
	{

	}

	public function removeVehicle($vehicleId) 
	{
		$authNamespace = new Zend_Session_Namespace('userInformation');
    	$institution = $authNamespace->institution;
		if($institution == 3)
		{
			$this->changeStatus($vehicleId,6);
			return true;
		}
		else
		{
			$vehicle = new Application_Model_DbTable_Vehicle();
			$vehicleRow = $vehicle->fetchRow($vehicle->select()->where('id = ?',$vehicleId));
			if($vehicleRow)
			{
				$vehicleOther = new Application_Model_DbTable_VehicleOther();
				$vehicleOtherRow = $vehicleOther->fetchRow($vehicleOther->select()->where('id = ?',$vehicleId));
				if($vehicleOtherRow)
					$vehicleOtherRow->delete();

				$vehicleMeasures = new Application_Model_DbTable_VehicleMeasures();
				$vehicleMeasuresRow = $vehicleMeasures->fetchRow($vehicleMeasures->select()->where('id = ?',$vehicleId));
				if($vehicleMeasuresRow)
					$vehicleMeasuresRow->delete();

				$vehicleMechanics = new Application_Model_DbTable_VehicleMechanics();
				$vehicleMechanicsRow = $vehicleMechanics->fetchRow($vehicleMechanics->select()->where('id = ?',$vehicleId));
				if($vehicleMechanicsRow)
					$vehicleMechanicsRow->delete();

				$vehicleHistoric = new Application_Model_DbTable_VehicleHistoric();
				$vehicleHistoricRow = $vehicleHistoric->fetchAll($vehicleHistoric->select()->where('vehicle_id = ?',$vehicleId));
				if($vehicleHistoricRow)
				{
					foreach($vehicleHistoricRow as $aux)
					{
						$aux->delete();
					}
				}

				$vehicleStatus = new Application_Model_DbTable_VehicleStatus();
				$vehicleStatusRow = $vehicleStatus->fetchRow($vehicleStatus->select()->where('vehicle_id = ?',$vehicleId));
				if($vehicleStatusRow)
					$vehicleStatusRow->delete();

				return $vehicleRow->delete();
			}
		}
		return false;
	}

	/**
	*	Save information about documents attachment of a certain vehicle.
	*
	*	@param array $files - vehicle's file information
	*	@param array $data - vehicle's data
	*	@param integer $vehicleId - vehicle's id
	*	@access public
	*	@return integer
	*/
	public function saveDocument($files,$data,$vehicleId)
	{
		try{
			$vehicleDocuments = new Application_Model_DbTable_VehicleDocuments();
			$ext = substr(strrchr($files['file']['name'],'.'),1);
			
			if($this->saveFile($files,$ext,$data,$vehicleId))
			{
				$vehicleNew = $vehicleDocuments->createRow();
				$vehicleNew->vehicle_id = $vehicleId;
				if($data['date_notification'] != ''){
					$date = Application_Model_General::dateToUs($data['date_notification']);
					$vehicleNew->document = $data['document'].'_'.$date.'.'.$ext;
				}
				else if($data['date_inspection'] != ''){
					$date = Application_Model_General::dateToUs($data['date_inspection']);
					$vehicleNew->document = $data['document'].'_'.$date.'.'.$ext;
				}
				else{
					$vehicleNew->document = $data['document'].'.'.$ext;
				}
				return $vehicleNew->save();
			}
			return false;
			
		}catch(Zend_Exception $e){
			return false;
		}
	}

	/**
	*	Save a file in some directory.
	*
	*	@param array $files - vehicle's file information
	*	@param string $ext - extension of file
	*	@param array $data - vehicle's data
	*	@param integer $vehicleId - vehicle's id
	*	@access protected
	*	@return boolean
	*/
	protected function saveFile($file,$ext,$data,$vehicleId)
	{
		$target_directory = APPLICATION_PATH.'/../public/upload/vehicle/'.$vehicleId;
		$this->directory($target_directory);
		if($data['document'] == 'inspection'){
			$date = Application_Model_General::dateToUs($data['date_inspection']);
			$newFile = $target_directory.'/'.$data['document'].'_'.$date.'.'.$ext;
		}
		else{
			$date = Application_Model_General::dateToUs($data['date_notification']);
			$newFile = $target_directory.'/'.$data['document'].'.'.$ext;
		}
		return move_uploaded_file($file['file']['tmp_name'], $newFile);
	}

	/**
	*	Check if directory exists. If doesn't exists, it creates it.
	*
	*	@param string $target_directory - path of directory
	*	@access protected
	*	@return null
	*/
	protected function directory($target_directory)
	{
		if(!is_dir($target_directory))
		{
			mkdir($target_directory,0700);
		}
	}

	/**
	*	Verify access of institution to edit vehicle.
	*
	* @param integer $vehicleId - vehicle's id
	* @param integer $institution - institution's id
	* @access public
	* @return boolean
	*/
	public function verifyAccess($vehicleId,$institution)
	{
		$vehicleStatus = new Application_Model_DbTable_VehicleStatus();
		$vehicleStatusRow = $vehicleStatus->fetchRow($vehicleStatus->select()->where('vehicle_id = ?',$vehicleId));
		if($institution == 3)
		{
			if($vehicleStatusRow->status == 1)
			{
				return true;
			}
			else
			{
				return false;
			}
		}
		return true;
	}

	/**
	*	Check the minimum requirements of register vehicle on system.
	*
	*	@param integer $vehicleId - vehicle's id
	*	@access public
	*	@return boolean
	*/
	public function checkMinimumRequirements($vehicleId)
	{
		$vehicle = new Application_Model_DbTable_Vehicle();
		$vehicleRow = $vehicle->fetchRow($vehicle->select()->where('id = ?',$vehicleId));
		if(!$vehicleRow)
			return false;

		$vehicleOther = new Application_Model_DbTable_VehicleOther();
		$vehicleOtherRow = $vehicleOther->fetchRow($vehicleOther->select()->where('id = ?',$vehicleId));
		if(!$vehicleOtherRow)
			return false;

		$vehicleMeasures = new Application_Model_DbTable_VehicleMeasures();
		$vehicleMeasuresRow = $vehicleMeasures->fetchRow($vehicleMeasures->select()->where('id = ?',$vehicleId));
		if(!$vehicleMeasuresRow)
			return false;

		$vehicleMechanics = new Application_Model_DbTable_VehicleMechanics();
		$vehicleMechanicsRow = $vehicleMechanics->fetchRow($vehicleMechanics->select()->where('id = ?',$vehicleId));
		if(!$vehicleMechanicsRow)
			return false;

		$maintenance = $this->returnDocument($vehicleId,'maintenance');
		$crlv = $this->returnDocument($vehicleId,'crlv');
	
		if(!$maintenance || !$crlv)
		{
			return false;
		}
		return true;
	}

	public function newCRV($vehicleId)
	{
		$vehicle_new = $this->returnVehicleNew($vehicleId);
        if(!$vehicle_new){
			$authNamespace = new Zend_Session_Namespace('userInformation');
			$vehicle = new Application_Model_DbTable_VehicleAskCrv();
			$vehicleNew = $vehicle->createRow();
		    $vehicleNew->user_id = $authNamespace->user_id;
		    $vehicleNew->date = new Zend_Db_Expr('NOW()');
		    $vehicleNew->vehicle_id = $vehicleId;
		    $vehicleNew->justify = 'Solicitação automatica do DER-MG';
		    $vehicleNew->status = 2;
		    return $vehicleNew->save();
		}
		return true;
	}

	public function changeStatus($vehicleId,$status)
	{
		if($status == 3){
			$vehicleEdited = new Application_Model_DbTable_VehicleEdited();
			$vehicleEditedRow = $vehicleEdited->fetchRow($vehicleEdited->select()->where('vehicle_id = ?',$vehicleId));
			if($vehicleEditedRow->need_crv == 1){
				//solicitar crv
				return $this->newCRV($vehicleId);
			}
			else if($vehicleEditedRow->need_crv == 0){
				$vehicleStatus = new Application_Model_DbTable_VehicleStatus();
				$vehicleStatusRow = $vehicleStatus->fetchRow($vehicleStatus->select()->where('vehicle_id = ?',$vehicleId));
				$vehicleStatusRow->status = $status;
				return $vehicleStatusRow->save();
			}
		}
		else{
			$vehicleStatus = new Application_Model_DbTable_VehicleStatus();
			$vehicleStatusRow = $vehicleStatus->fetchRow($vehicleStatus->select()->where('vehicle_id = ?',$vehicleId));
			$vehicleStatusRow->status = $status;
			return $vehicleStatusRow->save();
		}
	}

	public function changeStatusVehicleNew($vehicleId)
	{
		$vehicleStatus = new Application_Model_DbTable_VehicleNew();
		$vehicleStatusRow = $vehicleStatus->fetchRow($vehicleStatus->select()->where('vehicle_id = ?',$vehicleId));
		$vehicleStatusRow->status = 0;
		return $vehicleStatusRow->save();
	}

	public function saveRoulette($data)
	{
		$vehicleOther = new Application_Model_DbTable_VehicleOther();
		$vehicleOtherRow = $vehicleOther->fetchRow($vehicleOther->select()->where('id = ?',$data['vehicle_id']));
		if($vehicleOtherRow)
		{
			$vehicleOtherRow->seal_roulette = $data['seal_roulette'];
			$vehicleOtherRow->seal_floor = $data['seal_floor'];
			$vehicleOtherRow->seal_support = $data['seal_support'];
			$vehicleOtherRow->seal_date = Application_Model_General::dateToUs($data['seal_date']);
			return $vehicleOtherRow->save();
		}
		return false;
	}

	protected function copyDocuments($vehicleId,$vehicleNewId)
	{
		$target_directory = APPLICATION_PATH.'/vehicle/'.$vehicleId;
		$new_target_directory = APPLICATION_PATH.'/vehicle/'.$vehicleNewId;
		if(is_dir($target_directory))
		{
			$dir = opendir($target_directory); 
	    @mkdir($new_target_directory); 
	    while(false !== ( $file = readdir($dir)) ) 
	    { 
        if (( $file != '.' ) && ( $file != '..' )) 
        { 
          if ( is_dir($src . '/' . $file) ) 
          { 
            recurse_copy($src . '/' . $file,$new_target_directory . '/' . $file); 
          } 
          else 
          { 
            copy($src . '/' . $file,$new_target_directory . '/' . $file); 
          } 
        } 
	    } 
		}
	}

	/**
	*	Return vehicles in process of register of certain consortium.
	*
	*	@access public
	*	@return array
	*/
	public function returnVehicleInProcess()
	{
    $authNamespace = new Zend_Session_Namespace('userInformation');
    $institution = $authNamespace->institution;
    $company = $authNamespace->company;
    $consortium_company = $this->consortiumCompanyCell($company);
    if(count($consortium_company))
    {
			$vehicle = new Application_Model_DbTable_Vehicle();
			$select = $vehicle->select()->setIntegrityCheck(false);
			$select	->from(array('v' => 'vehicle'),array('id', 'plate', 'renavam','id') )
							->joinLeft(array('m' => 'vehicle_status'),'m.vehicle_id=v.id',array('status'))
							->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id', array('historic_id' => 'id'))
							->where('vh.consortium_company IN (?)',$consortium_company->toArray())
							->where('m.status != 4')
							->where('m.status != 10')
							->order('v.id DESC')
							->group('v.id');
			return $vehicle->fetchAll($select);
    }
    else
    {
			$vehicle = new Application_Model_DbTable_Vehicle();
			$select = $vehicle->select()->setIntegrityCheck(false);
			$select	->from(array('v' => 'vehicle'),array('id', 'plate', 'renavam','id') )
							->joinLeft(array('m' => 'vehicle_status'),'m.vehicle_id=v.id',array('status'))
							->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id', array('historic_id' => 'id'))
							->where('vh.consortium = ?',$authNamespace->consortium)
							->where('m.status != 4')
							->where('m.status != 10')
							->order('v.id DESC')
							->group('v.id');
			return $vehicle->fetchAll($select);
    }
	}

	/**
	*	Accept review of registered vehicle.
	*
	*	@param integer $vehicleId - vehicle's id
	*	@access public
	*	@return boolean
	*/
	public function acceptReview($vehicleId)
  	{  
		return $this->changeStatus($vehicleId,4);
	}

	/**
	*	Deny review of registered vehicle.
	*
	*	@param integer $vehicleId - vehicle's id
	*	@access public
	*	@return boolean
	*/
	public function denyReview($vehicleId)
	{
		return $vehicle->changeStatus($vehicleId,8);
	}

	/**
	*	Create a new register of a vehicle transfer.
	*
	*	@param integer $vehicleId - vehicle's id
	*	@param integer $consortium - consortium's id
	*	@param integer $consortiumCompany - consortium's company id
	*	@access public
	*	@return integer
	*/
	public function transfer($vehicleId,$consortium,$consortiumCompany)
	{
  	$authNamespace = new Zend_Session_Namespace('userInformation');
		$transfer = new Application_Model_DbTable_VehicleTransfer();
    $transferNew = $transfer->createRow();
    $transferNew->user_id = $authNamespace->user_id;
    $transferNew->date = new Zend_Db_Expr('NOW()');
    $transferNew->vehicle_id = $vehicleId;
    $transferNew->consortium = $consortium;
    $transferNew->consortium_company = $consortiumCompany;
    $transferNew->status = 1;
    return $transferNew->save();
	}

	/**
	*	Return a list of vehicles transfered.
	*
	*	@access public
	*	@return public
	*/
	public function vehiclesTransfered()
	{
		$transfer = new Application_Model_DbTable_VehicleTransfer();
		$select = $transfer->select()->setIntegrityCheck(false);
		$select	->from(array('vt' => 'vehicle_transfer'),array('id') )
						->joinInner(array('v' => 'vehicle'),'v.id=vt.vehicle_id',array('vehicle_id' => 'id','plate'))
						->joinInner(array('vh' => 'vehicle_historic'),'v.id=vh.vehicle_id AND end_historic_date IS NULL',array('historic_id' => 'id'))
						->joinInner(array('c' => 'consortium'),'vt.consortium=c.id', array('pos_consortium' => 'name'))
						->joinInner(array('co' => 'consortium'),'vh.consortium=co.id', array('pre_consortium' => 'name'))
						->where('vt.status = 1')
						->group('vt.vehicle_id');
		return $transfer->fetchAll($select);
	}

	public function acceptTransfer($id)
	{
		$transfer = new Application_Model_DbTable_VehicleTransfer();
		$transferRow = $transfer->fetchRow($transfer->select()->where('id = ?',$id));
		if($transferRow)
		{
			$historic = new Application_Model_DbTable_VehicleHistoric();
			$historicRow = $historic->fetchRow($historic->select()
												->where('vehicle_id = ?',$transferRow->vehicle_id)
												->where('end_historic_date IS NULL'));
			if($historicRow)
			{
				$historicRow->end_historic_date = new Zend_Db_Expr('NOW()');
				if($historicRow->save())
				{
					$historicNew = $historic->createRow();
					$historicNew->vehicle_id = $transferRow->vehicle_id;
					$historicNew->consortium = $transferRow->consortium;
					$historicNew->consortium_company = $transferRow->consortium_company;
					$historicNew->external_number = $historicRow->external_number;
					$historicNew->authorization = '';
					$historicNew->start_historic_date = new Zend_Db_Expr('NOW()');
					$historicNew->save();

					$this->changeStatus($historicRow->vehicle_id, 4);

					$transferRow->status = 0;
					return $transferRow->save();
				}
			}
		}
		return false;
	}

	public function denyTransfer($id)
	{
		$transfer = new Application_Model_DbTable_VehicleTransfer();
		$transferRow = $transfer->fetchRow($transfer->select()->where('id = ?',$id));
		if($transferRow)
		{
			$this->changeStatus($transferRow->vehicle_id, 4);
			$transferRow->status = 0;
			return $transferRow->save();
		}
		return false;
	}

	public function returnSealData($vehicleId)
	{
		$seal = new Application_Model_DbTable_VehicleSeal();
		$sealRow = $seal->fetchAll($seal->select()->where('vehicle_id = ?',$vehicleId));
		return $sealRow;
	}

	/**
	*	Edit a seal data of vehicle.
	*
	*	@param array $data - seal data of vehicle
	*	@param integer $vehicleID - vehicle's id
	*	@access protected
	*	@return integer
	*/
	protected function saveSeal($data,$vehicleId)
	{
		try{
			$authNamespace = new Zend_Session_Namespace('userInformation');
			$vehicle = new Application_Model_DbTable_VehicleSeal();
			$vehicleRow = $vehicle->createRow();
			$vehicleRow->registration_date = new Zend_Db_Expr('NOW()');
			$vehicleRow->user = $authNamespace->user_id;
			$vehicleRow->vehicle_id = $vehicleId;
			if(isset($data['seal_roulette_new']) && $data['seal_roulette_new'] != '') $vehicleRow->seal_roulette_new = $data['seal_roulette_new'];
			if(isset($data['seal_floor_new']) && $data['seal_floor_new'] != '') $vehicleRow->seal_floor_new = $data['seal_floor_new'];
			if(isset($data['seal_support_new']) && $data['seal_support_new'] != '') $vehicleRow->seal_support_new = $data['seal_support_new'];
			if(isset($data['seal_roulette_old']) && $data['seal_roulette_old'] != '') $vehicleRow->seal_roulette_old = $data['seal_roulette_old'];
			if(isset($data['seal_floor_old']) && $data['seal_floor_old'] != '') $vehicleRow->seal_floor_old = $data['seal_floor_old'];
			if(isset($data['seal_support_old']) && $data['seal_support_old'] != '') $vehicleRow->seal_support_old = $data['seal_support_old'];
			$vehicleRow->change_date = Application_Model_General::dateToUs($data['change_date']);
			$vehicleRow->old_roulette_number = $data['old_roulette_number'];
			$vehicleRow->new_roulette_number = $data['new_roulette_number'];
			$vehicleRow->seal_change_origin = $data['seal_change_origin'];
			$vehicleRow->seal_change_reason = $data['seal_change_reason'];
			$vehicleRow->dae_number = $data['dae_number'];
			return $vehicleRow->save();
		}catch(Zend_Exception $e){
			return false;
		}
	}

	/**
	*	Edit a inspection data of vehicle.
	*
	*	@param array $data - inspection data of vehicle
	*	@param integer $vehicleID - vehicle's id
	*	@access protected
	*	@return integer
	*/
	protected function saveInspection($data,$vehicleId)
	{
		try{
			$authNamespace = new Zend_Session_Namespace('userInformation');
			$vehicle = new Application_Model_DbTable_VehicleInspection();
			$vehicleRow = $vehicle->createRow();
			$vehicleRow->registration_date = new Zend_Db_Expr('NOW()');
			$vehicleRow->user = $authNamespace->user_id;
			$vehicleRow->vehicle_id = $vehicleId;
			if(isset($data['roulette_number']) && $data['roulette_number'] != '') $vehicleRow->roulette_number = $data['roulette_number'];
			if(isset($data['hour_inspection']) && $data['hour_inspection'] != '') $vehicleRow->hour_inspection = $data['hour_inspection'];
			if(isset($data['date_inspection']) && $data['date_inspection'] != '') $vehicleRow->date_inspection = Application_Model_General::dateToUs($data['date_inspection']);
			if(isset($data['observation']) && $data['observation'] != '') $vehicleRow->observation = $data['observation'];
			return $vehicleRow->save();
		}catch(Zend_Exception $e){
			return false;
		}
	}

	public function returnInspectionData($vehicleId)
	{
		$vehicle = new Application_Model_DbTable_VehicleInspection();
		$vehicleRow = $vehicle->fetchAll($vehicle->select()->where('vehicle_id = ?',$vehicleId));
		return $vehicleRow;
	}

	/**
	*	Edit a notification data of vehicle.
	*
	*	@param array $data - notification data of vehicle
	*	@param integer $vehicleID - vehicle's id
	*	@access protected
	*	@return integer
	*/
	protected function saveNotification($data,$vehicleId)
	{
		try{
			$authNamespace = new Zend_Session_Namespace('userInformation');
			$vehicle = new Application_Model_DbTable_VehicleNotification();
			$vehicleRow = $vehicle->createRow();
			$vehicleRow->registration_date = new Zend_Db_Expr('NOW()');
			$vehicleRow->user = $authNamespace->user_id;
			$vehicleRow->vehicle_id = $vehicleId;
			if(isset($data['roulette_number']) && $data['roulette_number'] != '') $vehicleRow->roulette_number = $data['roulette_number'];
			if(isset($data['hour_notification']) && $data['hour_notification'] != '') $vehicleRow->hour_notification = $data['hour_notification'];
			if(isset($data['date_notification']) && $data['date_notification'] != '') $vehicleRow->date_notification = Application_Model_General::dateToUs($data['date_notification']);
			if(isset($data['observation']) && $data['observation'] != '') $vehicleRow->observation = $data['observation'];
			return $vehicleRow->save();
		}catch(Zend_Exception $e){
			return false;
		}
	}

	public function returnNotificationData($vehicleId)
	{
		$vehicle = new Application_Model_DbTable_VehicleNotification();
		$vehicleRow = $vehicle->fetchAll($vehicle->select()->where('vehicle_id = ?',$vehicleId));
		return $vehicleRow;
	}

	/**
	*	Ask to remove a vehicle in the system.
	*
	*	@param integer vehicleId - vehicle's id
	*	@access public
	*	@return integer
	*/
	public function down($vehicleId)
	{
  	$authNamespace = new Zend_Session_Namespace('userInformation');
		$vehicle = new Application_Model_DbTable_VehicleDown();
		$vehicleNew = $vehicle->createRow();
    $vehicleNew->user_id = $authNamespace->user_id;
    $vehicleNew->date = new Zend_Db_Expr('NOW()');
    $vehicleNew->vehicle_id = $vehicleId;
    $vehicleNew->status = 1;
    return $vehicleNew->save();
	}

	public function askCrv($vehicleId, $body)
	{
  	$authNamespace = new Zend_Session_Namespace('userInformation');
		$vehicle = new Application_Model_DbTable_VehicleAskCrv();
		$vehicleNew = $vehicle->createRow();
    $vehicleNew->user_id = $authNamespace->user_id;
    $vehicleNew->date = new Zend_Db_Expr('NOW()');
    $vehicleNew->vehicle_id = $vehicleId;
    $vehicleNew->justify = $body;
    $vehicleNew->status = 1;
    return $vehicleNew->save();
	}

	public function vehiclesAskedCrv()
	{
		$transfer = new Application_Model_DbTable_VehicleTransfer();
		$select = $transfer->select()->setIntegrityCheck(false);
		$select	->from(array('vt' => 'vehicle_ask_crv'),array('id', 'justify') )
						->joinInner(array('v' => 'vehicle'),'v.id=vt.vehicle_id',array('vehicle_id' => 'id','plate'))
						->joinInner(array('vh' => 'vehicle_historic'),'v.id=vh.vehicle_id AND end_historic_date IS NULL',array('historic_id' => 'id'))
						->where('vt.status = 2')
						->group('vt.vehicle_id');
		return $transfer->fetchAll($select);
	}

	public function acceptCrv($id)
	{
		$crv = new Application_Model_DbTable_VehicleAskCrv();
		$crvRow = $crv->fetchAll($crv->select()->where('vehicle_id = ?',$id)
											   ->where('status=2'));
		foreach ($crvRow as $value)
		{
			$value->status = 0;
			$saved = $value->save();
		}
		return $saved;
		return false;
	}

	public function denyCrv($id)
	{
		$crv = new Application_Model_DbTable_VehicleAskCrv();
		$crvRow = $crv->fetchRow($crv->select()->where('id = ?',$id));
		if($crvRow)
		{
			$crvRow->status = 0;
			return $crvRow->save();
		}
		return false;
	}

	/**
	*	Return the research document .
	*
	*	@param integer vehicleId - vehicle's id
	*	@param string document - type document
	*	@access public
	*	@return integer
	*/
	public function returnDocument($vehicleId,$document){
		$aux = substr($document,0,4);
		$vehicle = new Application_Model_DbTable_VehicleDocuments();
		$select = $vehicle->select()->setIntegrityCheck(false);
		if($aux == 'insp'){
			$aux2 = explode($document,"_");
			if(!isset($aux2[1]) && $aux2[1] == ''){
				$select	->from(array('v' => 'vehicle_documents'),array('id','document') )
								->where('v.vehicle_id = ?', $vehicleId)
								->where('substr(v.document,1,4) like ?', $aux)
								->order('v.id DESC');
			}else{
				$select	->from(array('v' => 'vehicle_documents'),array('id','document') )
								->where('v.vehicle_id = ?', $vehicleId)
								->where('substr(v.document,1,21) like ?', $document);
			}
		}
		elseif ($aux == 'noti') {
			$select	->from(array('v' => 'vehicle_documents'),array('id','document') )
							->where('v.vehicle_id = ?', $vehicleId)
							->where('substr(v.document,1,23) like ?', $document);
		}
		else{
			$select	->from(array('v' => 'vehicle_documents'),array('id','document') )
							->where('v.vehicle_id = ?', $vehicleId)
							->where('substr(v.document,1,4) like ?', $aux);
		}
		$vehicleRow = $vehicle->fetchRow($select);
		return $vehicleRow;
	}

	/**
	*	delete document .
	*
	*	@param integer id - document id
	*	@access public
	*	@return integer
	*/
	public function deleteDocument($id){
		$document = new Application_Model_DbTable_VehicleDocuments();
		$documentRow = $document->fetchRow($document->select()->where('id = ?',$id));
		if($documentRow->delete())
		{
			return true;
		}
		return false;
	}

	/**
	*	Remove vehicle inputing end_historic_date with now().
	*
	*	@param integer $vehicleId - vehicle's id
	*	@access public
	*	@return integer
	*/
	public function downVehicle($vehicleId)
	{
		$vehicle = new Application_Model_DbTable_VehicleHistoric();
		$vehicleRow = $vehicle->fetchRow($vehicle->select()->where('vehicle_id = ?',$vehicleId)->where('end_historic_date IS NULL'));
		if($vehicleRow)
		{
			$vehicleRow->end_historic_date = new Zend_Db_Expr('NOW()');
			return $vehicleRow->save();
		}
		return false;
	}

	public function returnVehicleActive()
	{
		$vehicle = new Application_Model_DbTable_Vehicle();
		$select = $vehicle->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
																						'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor') )
						->joinInner(array('m' => 'vehicle_mechanics'),'m.id=v.id')
						->joinInner(array('me' => 'vehicle_measures'),'me.id=v.id')
						->joinInner(array('mo' => 'vehicle_other'),'mo.id=v.id', array(
															'elevator_date' => new Zend_Db_Expr ('DATE_FORMAT(elevator_date,"%d/%m/%Y")'),
															'seal_date' => new Zend_Db_Expr ('DATE_FORMAT(seal_date,"%d/%m/%Y")') ,
															'insurer_date'  => new Zend_Db_Expr ('DATE_FORMAT(insurer_date,"%d/%m/%Y")'),
															'elevator','seal_roulette','seal_floor','seal_support','insurer','eletronic_roulette','collector_area',
															'air_conditioning','gps','wifi','bike_support','tv','camera','amount_validator'))
						->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id AND end_historic_date IS NULL', array('external_number', 'start_historic_date', 'end_historic_date'))
						->joinInner(array('c' => 'consortium'),'c.id=vh.consortium',array('consortium_name' => 'name'))
						->joinInner(array('co' => 'consortium_companies'),'co.id=vh.consortium_company',array('cell_name' => 'name'))
						->joinInner(array('com' => 'company'),'com.id=co.company',array('company_name' => 'company'))
						->joinInner(array('col' => 'vehicle_color'),'col.id=v.color',array('color_name' => 'name'))
						->joinInner(array('pat' => 'vehicle_pattern'),'pat.id=v.pattern',array('pattern_name' => 'name'))
						->joinInner(array('vch' => 'vehicle_chassi'),'vch.id=m.chassi_model',array('model_chassi_name' => 'name'))
						->joinInner(array('vmo' => 'vehicle_motor'),'vmo.id=m.motor_localization',array('motor_localization_name' => 'name'))
						->joinInner(array('vbo' => 'vehicle_body'),'vbo.id=m.body_model',array('body_model_name' => 'name'))
						->joinInner(array('vsu' => 'vehicle_suspension'),'vsu.id=m.suspension',array('suspension_name' => 'name'))
						->joinInner(array('vca' => 'vehicle_cambium'),'vca.id=m.cambium',array('cambium_name' => 'name'))
						->joinInner(array('vse' => 'vehicle_seat'),'vse.id=m.seat_type',array('seat_name' => 'name'))
						->joinInner(array('vty' => 'vehicle_type'),'vty.id=v.type',array('type_name' => 'name'))
						->joinInner(array('vsv' => 'vehicle_service'),'vsv.id=v.service',array('service_name' => 'name'))
						->order('vh.external_number');
		return $vehicle->fetchAll($select);
	}

	public function returnAllVehicle()
	{
		$vehicle = new Application_Model_DbTable_Vehicle();
		$select = $vehicle->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
																						'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor') )
						->joinInner(array('m' => 'vehicle_mechanics'),'m.id=v.id')
						->joinInner(array('me' => 'vehicle_measures'),'me.id=v.id')
						->joinInner(array('mo' => 'vehicle_other'),'mo.id=v.id', array(
															'elevator_date' => new Zend_Db_Expr ('DATE_FORMAT(elevator_date,"%d/%m/%Y")'),
															'seal_date' => new Zend_Db_Expr ('DATE_FORMAT(seal_date,"%d/%m/%Y")') ,
															'insurer_date'  => new Zend_Db_Expr ('DATE_FORMAT(insurer_date,"%d/%m/%Y")'),
															'elevator','seal_roulette','seal_floor','seal_support','insurer','eletronic_roulette','collector_area',
															'air_conditioning','gps','wifi','bike_support','tv','camera','amount_validator'))
						->joinInner(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id', array('external_number', 'start_historic_date', 'end_historic_date'))
						->joinInner(array('c' => 'consortium'),'c.id=vh.consortium',array('consortium_name' => 'name'))
						->joinInner(array('co' => 'consortium_companies'),'co.id=vh.consortium_company',array('cell_name' => 'name'))
						->joinInner(array('com' => 'company'),'com.id=co.company',array('company_name' => 'company'))
						->joinInner(array('col' => 'vehicle_color'),'col.id=v.color',array('color_name' => 'name'))
						->joinInner(array('pat' => 'vehicle_pattern'),'pat.id=v.pattern',array('pattern_name' => 'name'))
						->joinInner(array('vch' => 'vehicle_chassi'),'vch.id=m.chassi_model',array('model_chassi_name' => 'name'))
						->joinInner(array('vmo' => 'vehicle_motor'),'vmo.id=m.motor_localization',array('motor_localization_name' => 'name'))
						->joinInner(array('vbo' => 'vehicle_body'),'vbo.id=m.body_model',array('body_model_name' => 'name'))
						->joinInner(array('vsu' => 'vehicle_suspension'),'vsu.id=m.suspension',array('suspension_name' => 'name'))
						->joinInner(array('vca' => 'vehicle_cambium'),'vca.id=m.cambium',array('cambium_name' => 'name'))
						->joinInner(array('vse' => 'vehicle_seat'),'vse.id=m.seat_type',array('seat_name' => 'name'))
						->joinInner(array('vty' => 'vehicle_type'),'vty.id=v.type',array('type_name' => 'name'))
						->joinInner(array('vsv' => 'vehicle_service'),'vsv.id=v.service',array('service_name' => 'name'))
						->order('vh.external_number');
		return $vehicle->fetchAll($select);
	}

	public function consortiumCompany($consortium,$company)
	{
		$consortiumCompany = new Application_Model_DbTable_ConsortiumCompanies();
		$consortiumCompanyRow = $consortiumCompany->fetchRow($consortiumCompany->select()->where('consortium_id = ?',$consortium)
												  ->where('company = ?',$company));
		return $consortiumCompanyRow;
	}

	/**
	*	Return all consortium cell of certain company.
	*
	*	@param integer $company -> company's id
	*	@access public
	*	@return Zend_Db_Table_Rowset
	*/
	public function consortiumCompanyCell($company)
	{
		$consortiumCompany = new Application_Model_DbTable_ConsortiumCompanies();
		return $consortiumCompany->fetchAll($consortiumCompany->select()->where('company = ?',$company));
	}

	public function delete($vehicleId)
	{
		$vehicle = new Application_Model_DbTable_Vehicle();
		$vehicleRow = $vehicle->fetchRow($vehicle->select()->where('id = ?',$vehicleId));
		return $vehicleRow->delete();
	}


	/**
	*	Register a vehicle edit on the system.
	*	
	*	@param array $data - vehicle's edited data
	* @access public
	* @return integer
	*/
	public function saveVehicleEdited($vehicle_id,$need_crv=0)
	{
		$vehicle = new Application_Model_DbTable_VehicleEdited();
		$authNamespace = new Zend_Session_Namespace('userInformation');
		$data['date'] = new Zend_Db_Expr('NOW()');
		$data['vehicle_id'] =$vehicle_id;
		$data['edited'] = 1;
		$data['need_crv'] = $need_crv;
		$data['user_id'] = $authNamespace->user_id;
		$vehicleNew = $vehicle->createRow($data);
		return $vehicleNew->save();
	}

	public function reviewEditedStatus($vehicleId)
	{
		$vehicleEdited = new Application_Model_DbTable_VehicleEdited();
		$vehicleEditedRow = $vehicleEdited->fetchAll($vehicleEdited->select()->where('vehicle_id = ?',$vehicleId));
		foreach ($vehicleEditedRow as $value) {
		    $value->edited = 0;
		    $value->save();
		}
		return true;
	}

}

