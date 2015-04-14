<?php

class Application_Model_VehicleType
{
	public function newVehicleType($data)
	{
		$vehicleType = new Application_Model_DbTable_VehicleType();
		$vehicleTypeRow = $vehicleType->createRow();
		$vehicleTypeRow->name = $data['name'];
		return $vehicleTypeRow->save();
	}

	public function lists()
	{
		$vehicleType = new Application_Model_DbTable_VehicleType();
		return $vehicleType->fetchAll();
	}

	public function editVehicleType($data,$vehicleTypeId)
	{
		$vehicleType = new Application_Model_DbTable_VehicleType();
		$vehicleTypeRow = $vehicleType->fetchRow($vehicleType->select()->where('id = ?',$vehicleTypeId));
		if($vehicleTypeRow)
		{
			$vehicleTypeRow->name = $data['name'];
			return $vehicleTypeRow->save();
		}
		return false;
	}

	public function returnById($vehicleTypeId)
	{
		$vehicleType = new Application_Model_DbTable_VehicleType();
		$select = $vehicleType->select()->setIntegrityCheck(false);
		$select	->from(array('u' => 'vehicle_type'));
		return $vehicleType->fetchRow($vehicleType->select()->where('id = ?',$vehicleTypeId));
	}

	public function findByName($name)
	{
		$vehicleType = new Application_Model_DbTable_VehicleType();
		return $vehicleType->fetchAll($vehicleType->select()->where('name LIKE ?', '%'.$name.'%'));
	}
}

