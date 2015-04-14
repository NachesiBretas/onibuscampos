<?php

class Application_Model_Inspection
{
	public function listVehicles()
	{
		$vehicle = new Application_Model_DbTable_Vehicle();
		$select = $vehicle->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
																						'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor', 'id') )
						->joinLeft(array('m' => 'vehicle_status'),'m.vehicle_id=v.id',array('status'));
		return $vehicle->fetchAll($select);
	}

	public function returnByPlate($plate)
	{
		$vehicle = new Application_Model_DbTable_Vehicle();
		$select = $vehicle->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
																						'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor', 'id') )
						->joinLeft(array('m' => 'vehicle_status'),'m.vehicle_id=v.id',array('status'))
						->where('plate LIKE ?','%'.$plate.'%');
		return $vehicle->fetchAll($select);
	}

	public function returnByRenavam($renavam)
	{
		$vehicle = new Application_Model_DbTable_Vehicle();
		$select = $vehicle->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
																						'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor', 'id') )
						->joinLeft(array('m' => 'vehicle_status'),'m.vehicle_id=v.id',array('status'))
						->where('renavam LIKE ?','%'.$renavam.'%');
		return $vehicle->fetchAll($select);
	}

	public function returnByExternalNumber($externalNumber)
	{
		$vehicle = new Application_Model_DbTable_Vehicle();
			$select = $vehicle->select()->setIntegrityCheck(false);
			$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
														 'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor', 'id') )
							->joinLeft(array('m' => 'vehicle_status'),'m.vehicle_id=v.id',array('status'))
							->joinLeft(array('vh' => 'vehicle_historic'),'vh.vehicle_id=v.id', array('historic_id' => 'id', 'external_number', 'end_historic_date'))
							->where('vh.external_number = ?',$externalNumber)
							->group('vh.external_number');
			return $vehicle->fetchAll($select);
	}

	public function returnMinimunRequirements($vehicleId)
	{
		$vehicle = new Application_Model_Vehicle();
		$insp_data = $vehicle->returnInspectionData($vehicleId);
		foreach ($insp_data as $inspection){
			$type = 'inspection_'.$inspection->date_inspection; 
		}
		$inspection = $vehicle->returnDocument($vehicleId,$type);
		if($inspection)
		{
			return true;
		}
		return false;
	}

	public function listVehiclesDown()
	{
		$vehicle = new Application_Model_DbTable_Vehicle();
		$select = $vehicle->select()->setIntegrityCheck(false);
		$select	->from(array('v' => 'vehicle'),array('start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
																						'service', 'plate', 'renavam', 'pattern', 'color', 'type', 'floor', 'id') )
						->joinLeft(array('m' => 'vehicle_status'),'m.vehicle_id=v.id',array('status'))
						->where('m.status = 10');
		return $vehicle->fetchAll($select);
	}

	public function acceptDown($vehicleId)
	{
		$vehicle = new Application_Model_DbTable_VehicleHistoric();
		$vehicleRow = $vehicle->fetchRow($vehicle->select()->where('vehicle_id = ?',$vehicleId)->where('end_historic_date IS NULL'));
		if($vehicleRow)
		{
			$vehicle = new Application_Model_Vehicle();
			$vehicle->changeStatus($vehicleId,4);

			$vehicleDown = new Application_Model_DbTable_VehicleDown();
			$vehicleDownRow = $vehicleDown->fetchRow($vehicleDown->select()->where('vehicle_id = ?',$vehicleId)->where('status = 1'));
			if($vehicleDownRow)
			{
				$vehicleDownRow->status = 0;
				$vehicleDownRow->save();
				$vehicleRow->end_historic_date = new Zend_Db_Expr('NOW()');
				return $vehicleRow->save();
			}
		}
	}

	public function denyDown($vehicleId)
	{
		$vehicle = new Application_Model_Vehicle();
		$vehicle->changeStatus($vehicleId,4);

		$vehicleDown = new Application_Model_DbTable_VehicleDown();
		$vehicleDownRow = $vehicleDown->fetchRow($vehicleDown->select()->where('vehicle_id = ?',$vehicleId)->where('status = 1'));
		if($vehicleDownRow)
		{
			$vehicleDownRow->status = 0;
			return $vehicleDownRow->save();
		}
	}


	public function vehiclesAskedCrv()
	{
		$transfer = new Application_Model_DbTable_VehicleTransfer();
		$select = $transfer->select()->setIntegrityCheck(false);
		$select	->from(array('vt' => 'vehicle_ask_crv'),array('id', 'justify') )
						->joinInner(array('v' => 'vehicle'),'v.id=vt.vehicle_id',array('vehicle_id' => 'id','plate'))
						->joinInner(array('vh' => 'vehicle_historic'),'v.id=vh.vehicle_id AND end_historic_date IS NULL',array('historic_id' => 'id'))
						->where('vt.status = 1')
						->group('vt.vehicle_id');
		return $transfer->fetchAll($select);
	}

	public function acceptCrv($id)
	{
		$crv = new Application_Model_DbTable_VehicleAskCrv();
		$crvRow = $crv->fetchRow($crv->select()->where('id = ?',$id));
		if($crvRow)
		{
			$crvRow->status = 2;
			return $crvRow->save();
		}
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

}

