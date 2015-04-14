<?php

class Application_Model_City
{
	public function newCity($data)
	{
		$city = new Application_Model_DbTable_City();
		$cityRow = $city->createRow();
		$cityRow->name = $data['name'];
		return $cityRow->save();
	}

	public function lists()
	{
		$city = new Application_Model_DbTable_City();
		return $city->fetchAll();
	}

	public function editCity($data,$cityId)
	{
		$city = new Application_Model_DbTable_City();
		$cityRow = $city->fetchRow($city->select()->where('id = ?',$cityId));
		if($cityRow)
		{
			$cityRow->name = $data['name'];
			return $cityRow->save();
		}
		return false;
	}

	public function returnById($cityId)
	{
		$city = new Application_Model_DbTable_City();
		$select = $city->select()->setIntegrityCheck(false);
		$select	->from(array('u' => 'city'));
		return $city->fetchRow($city->select()->where('id = ?',$cityId));
	}

	public function findByName($name)
	{
		$city = new Application_Model_DbTable_City();
		return $city->fetchAll($city->select()->where('name LIKE ?', '%'.$name.'%'));
	}
}

