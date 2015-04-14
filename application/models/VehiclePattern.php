<?php

class Application_Model_VehiclePattern
{
	public function newVehiclePattern($data)
	{
		$vehiclePattern = new Application_Model_DbTable_VehiclePattern();
		$vehiclePatternRow = $vehiclePattern->createRow();
		$vehiclePatternRow->name = $data['name'];
		return $vehiclePatternRow->save();
	}

	public function lists()
	{
		$vehiclePattern = new Application_Model_DbTable_VehiclePattern();
		return $vehiclePattern->fetchAll();
	}

	public function editVehiclePattern($data,$vehiclePatternId)
	{
		$vehiclePattern = new Application_Model_DbTable_VehiclePattern();
		$vehiclePatternRow = $vehiclePattern->fetchRow($vehiclePattern->select()->where('id = ?',$vehiclePatternId));
		if($vehiclePatternRow)
		{
			$vehiclePatternRow->name = $data['name'];
			return $vehiclePatternRow->save();
		}
		return false;
	}

	public function returnById($vehiclePatternId)
	{
		$vehiclePattern = new Application_Model_DbTable_VehiclePattern();
		$select = $vehiclePattern->select()->setIntegrityCheck(false);
		$select	->from(array('u' => 'vehicle_pattern'));
		return $vehiclePattern->fetchRow($vehiclePattern->select()->where('id = ?',$vehiclePatternId));
	}

	public function findByName($name)
	{
		$vehiclePattern = new Application_Model_DbTable_VehiclePattern();
		return $vehiclePattern->fetchAll($vehiclePattern->select()->where('name LIKE ?', '%'.$name.'%'));
	}
}

