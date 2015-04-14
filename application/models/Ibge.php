<?php

class Application_Model_Ibge
{
	public function listCodes()
	{
		$ibge = new Application_Model_DbTable_Ibge();
		$select = $ibge->select()->setIntegrityCheck(false);
		$select	->from(array('i' => 'ibge'),array());
		return $ibge->fetchAll($select);
	}

	public function isTrueByCode($code)
	{
		$ibge = new Application_Model_DbTable_Ibge();
		$select = $ibge->select()->setIntegrityCheck(false);
		$select	->from(array('i' => 'ibge'),array( 'name'))
						->where('ibge_code = ?', $code);
		return $ibge->fetchRow($select);
	}

}