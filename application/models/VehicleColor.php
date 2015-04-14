<?php

class Application_Model_VehicleColor
{
	public function newVehicleColor($data)
	{
		$vehicleColor = new Application_Model_DbTable_VehicleColor();
		$vehicleColorRow = $vehicleColor->createRow();
		$vehicleColorRow->name = $data['name'];
		return $vehicleColorRow->save();
	}

	public function lists()
	{
		$vehicleColor = new Application_Model_DbTable_VehicleColor();
		return $vehicleColor->fetchAll();
	}

	public function editVehicleColor($data,$vehicleColorId)
	{
		$vehicleColor = new Application_Model_DbTable_VehicleColor();
		$vehicleColorRow = $vehicleColor->fetchRow($vehicleColor->select()->where('id = ?',$vehicleColorId));
		if($vehicleColorRow)
		{
			$vehicleColorRow->name = $data['name'];
			return $vehicleColorRow->save();
		}
		return false;
	}

	public function returnById($vehicleColorId)
	{
		$vehicleColor = new Application_Model_DbTable_VehicleColor();
		$select = $vehicleColor->select()->setIntegrityCheck(false);
		$select	->from(array('u' => 'vehicle_color'));
		return $vehicleColor->fetchRow($vehicleColor->select()->where('id = ?',$vehicleColorId));
	}

	public function findByName($name)
	{
		$vehicleColor = new Application_Model_DbTable_VehicleColor();
		return $vehicleColor->fetchAll($vehicleColor->select()->where('name LIKE ?', '%'.$name.'%'));
	}
}

