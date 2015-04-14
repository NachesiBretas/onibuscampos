<?php

class Application_Model_McoFinance
{

	public function calculateMonth($month)
	{
		$mcoFinance = new Application_Model_DbTable_McoCash();
		$off = explode('-',$month);
		$select = $mcoFinance->select()->setIntegrityCheck(false);
		$select	->from(array('mc' => 'mco_cash'))
						->joinInner(array('m' => 'mco'),'m.id=mc.mco_id')
						->where('MONTH(m.date_operation) = ?', $off[0])
						->where('YEAR(m.date_operation) = ?', $off[1]);
		return $mcoFinance->fetchAll($select);
	}
}

