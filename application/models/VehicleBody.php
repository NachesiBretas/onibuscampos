<?php

class Application_Model_VehicleBody
{
	public function newVehicleBody($data)
	{
		$vehicleBody = new Application_Model_DbTable_VehicleBody();
		$vehicleBodyRow = $vehicleBody->createRow();
		$vehicleBodyRow->name = $data['name'];
		return $vehicleBodyRow->save();
	}

	public function lists()
	{
		$vehicleBody = new Application_Model_DbTable_VehicleBody();
		return $vehicleBody->fetchAll();
	}

	public function editVehicleBody($data,$vehicleBodyId)
	{
		$vehicleBody = new Application_Model_DbTable_VehicleBody();
		$vehicleBodyRow = $vehicleBody->fetchRow($vehicleBody->select()->where('id = ?',$vehicleBodyId));
		if($vehicleBodyRow)
		{
			$vehicleBodyRow->name = $data['name'];
			return $vehicleBodyRow->save();
		}
		return false;
	}

	public function returnById($vehicleBodyId)
	{
		$vehicleBody = new Application_Model_DbTable_VehicleBody();
		$select = $vehicleBody->select()->setIntegrityCheck(false);
		$select	->from(array('u' => 'vehicle_body'));
		return $vehicleBody->fetchRow($vehicleBody->select()->where('id = ?',$vehicleBodyId));
	}

	public function findByName($name)
	{
		$vehicleBody = new Application_Model_DbTable_VehicleBody();
		return $vehicleBody->fetchAll($vehicleBody->select()->where('name LIKE ?', '%'.$name.'%'));
	}
}

