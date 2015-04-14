<?php

class Application_Model_VehicleChassi
{
	public function newVehicleChassi($data)
	{
		$vehicleChassi = new Application_Model_DbTable_VehicleChassi();
		$vehicleChassiRow = $vehicleChassi->createRow();
		$vehicleChassiRow->name = $data['name'];
		return $vehicleChassiRow->save();
	}

	public function lists()
	{
		$vehicleChassi = new Application_Model_DbTable_VehicleChassi();
		return $vehicleChassi->fetchAll();
	}

	public function editVehicleChassi($data,$vehicleChassiId)
	{
		$vehicleChassi = new Application_Model_DbTable_VehicleChassi();
		$vehicleChassiRow = $vehicleChassi->fetchRow($vehicleChassi->select()->where('id = ?',$vehicleChassiId));
		if($vehicleChassiRow)
		{
			$vehicleChassiRow->name = $data['name'];
			return $vehicleChassiRow->save();
		}
		return false;
	}

	public function returnById($vehicleChassiId)
	{
		$vehicleChassi = new Application_Model_DbTable_VehicleChassi();
		$select = $vehicleChassi->select()->setIntegrityCheck(false);
		$select	->from(array('u' => 'vehicle_chassi'));
		return $vehicleChassi->fetchRow($vehicleChassi->select()->where('id = ?',$vehicleChassiId));
	}

	public function findByName($name)
	{
		$vehicleChassi = new Application_Model_DbTable_VehicleChassi();
		return $vehicleChassi->fetchAll($vehicleChassi->select()->where('name LIKE ?', '%'.$name.'%'));
	}
}

