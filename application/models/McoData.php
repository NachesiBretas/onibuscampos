<?php

class Application_Model_McoData
{

	public function returnJourneys($id)
	{
    $mcoData = new Application_Model_DbTable_McoData();
    return $mcoData->fetchAll($mcoData->select()->where('mco = ?',$id)->where('status=1')->order('line'));
	}

	public function returnJourneysByLine($id,$line)
	{
    $mcoData = new Application_Model_DbTable_McoData();
    return $mcoData->fetchAll($mcoData->select()->where('mco = ?',$id)->where('line = ?',$line)->where('status=1'));
	}

	public function returnJourneysByVehicle($id,$vehicle_number)
	{
    $mcoData = new Application_Model_DbTable_McoData();
    return $mcoData->fetchAll($mcoData->select()->where('mco = ?',$id)->where('vehicle_number = ?',$vehicle_number)->where('status=1')->order('line'));
	}

	public function returnAdjustments($id)
	{
		$before = $id - 1;
		$mcoAdjustments = new Application_Model_DbTable_McoAdjustmentsRoulette();
		$select = $mcoAdjustments->select()->setIntegrityCheck(false);
		$select	->from(array('m' => 'mco_adjustments_roulette'),array('id', 'mco_id', 'line', 'vehicle',
																						'roulette', 'date') )
						->where('m.mco_id = ?',$id)
						->group('m.roulette');
    return $mcoAdjustments->fetchAll($select);
	}

	public function returnDiffs($id)
	{
		$mcoDiffs = new Application_Model_DbTable_McoDiffRoulette();
    return $mcoDiffs->fetchAll($mcoDiffs->select()->where('mco_id = ?',$id)
    											  ->where('status=1')
    											  ->order('line'));
	}

	public function returnFinance($id)
	{
		$mcoFinance = new Application_Model_DbTable_McoCash();
    return $mcoFinance->fetchAll($mcoFinance->select()->where('mco_id = ?',$id));
	}

	public function returnFinanceByLine($id,$line)
	{
		$mcoFinance = new Application_Model_DbTable_McoCash();
    return $mcoFinance->fetchAll($mcoFinance->select()->where('mco_id = ?',$id)->where('line = ?',$line));
	}

	public function returnPassengerDiff($id)
	{
		$mcoDiffs = new Application_Model_DbTable_McoDiffRoulette();
    return $mcoDiffs->fetchAll($mcoDiffs->select()->where('id = ?',$id));
	}

	public function editAccreditPassenger($data,$id)
	{
		$mcoDiffs = new Application_Model_DbTable_McoDiffRoulette();
		$editAccredit = $mcoDiffs->fetchRow($mcoDiffs->select()->where('id = ?',$id));
		$editAccredit->id = $id;
		$editAccredit->status = 0;
		$editAccredit->save();

		$accreditNew = $mcoDiffs->fetchRow($mcoDiffs->select()->where('id = ?',$id));
		$editAccreditNew['id'] = null;
		$editAccreditNew['mco_id'] = $accreditNew->mco_id;
		$editAccreditNew['line'] = $accreditNew->line;
		$editAccreditNew['vehicle'] = $accreditNew->vehicle;
		$editAccreditNew['roulette_before'] = $accreditNew->roulette_before;
		$editAccreditNew['roulette_after'] = $accreditNew->roulette_after;
		$editAccreditNew['date_before'] = $accreditNew->date_before;
		$editAccreditNew['date_after'] = $accreditNew->date_after;
		$editAccreditNew['accredit_passenger'] = $data['accredit_passenger'];
		$editAccreditNew['justify'] = $data['justify'];
		$editAccreditNew['status'] = 1;
		$mcoDiffNew = $mcoDiffs->createRow($editAccreditNew);
		$save = $mcoDiffNew->save();

		$this->saveLogDiff($save,$id,2);

		if($save)
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public function returnJourneysId($id)
	{
    $mcoData = new Application_Model_DbTable_McoData();
    return $mcoData->fetchAll($mcoData->select()->where('id = ?',$id));
	}

	public function editLostLog($data,$id)
	{
	    $mcoData = new Application_Model_DbTable_McoData();
	    $editLostlog = $mcoData->fetchRow($mcoData->select()->where('id = ?',$id));
		$editLostlog->id = $id;
		$editLostlog->status = 0;
		$editLostlog->save();
		$lostlog = $mcoData->fetchRow($mcoData->select()->where('id = ?',$id));
		$editLostlogNew['id'] = null;
		$editLostlogNew['mco'] = $lostlog->mco;
		$editLostlogNew['type'] = $lostlog->type;
		$editLostlogNew['vehicle_number'] = $lostlog->vehicle_number;
		$editLostlogNew['start_roulette'] = $lostlog->start_roulette;
		$editLostlogNew['mid_roulette'] = $lostlog->mid_roulette;
		$editLostlogNew['end_roulette'] = $lostlog->end_roulette;
		$editLostlogNew['start_date'] = $lostlog->start_date;
		$editLostlogNew['mid_date'] = $lostlog->mid_date;
		$editLostlogNew['end_date'] = $lostlog->end_date;		
		$editLostlogNew['imported'] = $lostlog->imported;
		$editLostlogNew['line'] = $data['line'];
		$editLostlogNew['start_hour'] = $data['start_hour'];
		$editLostlogNew['mid_hour'] = $data['mid_hour'];
		$editLostlogNew['end_hour'] = $data['end_hour'];
		$editLostlogNew['craft'] = $data['craft'];
		$editLostlogNew['status'] = 1;
		$mcoDataNew = $mcoData->createRow($editLostlogNew);
		$save = $mcoDataNew->save();
		$this->saveLogData($save, $id,2);
		if($save)
			return true;
		else
			return false;
	}

	public function newLostLog($vehicle_number,$craft,$data,$mco_id)
	{
		$mcoData = new Application_Model_DbTable_McoData();
		if(isset($data['start_date']) && $data['start_date'] != '') 
			$data['start_date'] = Application_Model_General::dateToUs($data['start_date']);
		if(isset($data['mid_date']) && $data['mid_date'] != '') 
			$data['mid_date'] = Application_Model_General::dateToUs($data['mid_date']);
		if(isset($data['end_date']) && $data['end_date'] != '') 
			$data['end_date'] = Application_Model_General::dateToUs($data['end_date']);
		$data['mco'] = $mco_id;
		$data['vehicle_number'] = $vehicle_number;
		$data['craft'] = $craft;
		$data['status'] = 1;
		$data['imported'] = 0;
		$mcoDataNew = $mcoData->createRow($data);
		$save = $mcoDataNew->save();
		$this->saveLogData($save,null,1);
		return $save;
	}

	public function deleteLostLog($id)
	{
		$mcoData = new Application_Model_DbTable_McoData();
		$deleteMcoData = $mcoData->fetchRow($mcoData->select()->where('id = ?',$id));
		if($deleteMcoData)
		{
			//$deleteMcoData->delete();
			$deleteMcoData->id = $id;
			$deleteMcoData->status = 0;
			$deleteMcoData->save();
		}

		$this->saveLogData(null,$id,3);
		return true;
	}

	public function returnByLine($line,$id)
	{
		$mcoDiffs = new Application_Model_DbTable_McoDiffRoulette();
		return $mcoDiffs->fetchAll($mcoDiffs->select()->where('line = ?', $line)
													  ->where('mco_id = ?', $id)
													  ->where('status=1'));
	}

	public function returnDiffsByVehicle($id,$vehicle)
	{
		$mcoDiffs = new Application_Model_DbTable_McoDiffRoulette();
		return $mcoDiffs->fetchAll($mcoDiffs->select()->where('vehicle = ?', $vehicle)
													  ->where('mco_id = ?', $id)
													  ->where('status=1'));
	}

	public function saveLogData($mco_data_new_id, $mco_data_old_id, $action_type)
	{
		try { 
			$user = new Application_Model_DbTable_User();
 			$authNamespace = new Zend_Session_Namespace('userInformation');
 			$user = $authNamespace->user_id;

			$data['mco_data_new_id'] = $mco_data_new_id;
			$data['mco_data_old_id'] = $mco_data_old_id;
			$data['action_type'] = $action_type;
			$data['registration_date'] = date('Y-m-d H:i:s');
			$data['user'] = $user;
			$mcoDataLog = new Application_Model_DbTable_McoDataLog();
			$mcoDataLogNew = $mcoDataLog->createRow($data);
			$mcoDataLogNew->save();
		}catch(Zend_Exception $e) {

		}
	}

	public function saveLogDiff($mco_diff_new_id, $mco_diff_old_id, $action_type)
	{
		try { 
			$user = new Application_Model_DbTable_User();
 			$authNamespace = new Zend_Session_Namespace('userInformation');
 			$user = $authNamespace->user_id;

			$data['mco_diff_roulette_new_id'] = $mco_diff_new_id;
			$data['mco_diff_roulette_old_id'] = $mco_diff_old_id;
			$data['action_type'] = $action_type;
			$data['registration_date'] = date('Y-m-d H:i:s');
			$data['user'] = $user;
			$mcoDataLog = new Application_Model_DbTable_McoDiffRouletteLog();
			$mcoDataLogNew = $mcoDataLog->createRow($data);
			$save = $mcoDataLogNew->save();
			return $save;
		}catch(Zend_Exception $e) {

		}
	}

	public function deleteDay($id)
	{
		$a=$this->deleteMcoAdjustmentsRoulette($id);
		$b=$this->deleteMcoCash($id);
		$c=$this->deleteMcoData($id);
		$d=$this->deleteMcoDiffRoulette($id);

		$mco = new Application_Model_DbTable_Mco();
		$deleteDay = $mco->fetchRow($mco->select()->where('id = ?',$id));
		if($deleteDay)
		{
			//$deleteDay->delete();
			$deleteDay->id = $id;
			$deleteDay->status = 0;
			$deleteDay->save();
		}
		$save=$this->saveLogMco(null,$id,3);
		return true;
	}

	public function deleteMcoAdjustmentsRoulette($id)
	{
		$mco = new Application_Model_DbTable_McoAdjustmentsRoulette();
		$delete = $mco->fetchAll($mco->select()->where('mco_id = ?',$id));
		if($delete)
		{
			foreach($delete as $deleteAdjustmentsRoulette){
				$deleteAdjustmentsRoulette->delete();
			}
		}
		return true;
	}

	public function deleteMcoCash($id)
	{
		$mco = new Application_Model_DbTable_McoCash();
		$delete = $mco->fetchAll($mco->select()->where('mco_id = ?',$id));
		if($delete)
		{
			foreach($delete as $deleteCash){
				$deleteCash->delete();
			}
		}
		return true;
	}

	public function deleteMcoData($id)
	{
		$mco = new Application_Model_DbTable_McoData();
		$delete = $mco->fetchAll($mco->select()->where('mco = ?',$id));
		if($delete)
		{
			foreach($delete as $deleteData){
				//$deleteData->delete();
				$deleteData->status = 0;
				$deleteData->save();
			}
		}
		$this->saveLogData(null,$id,3);
		return true;
	}

	public function deleteMcoDiffRoulette($id)
	{
		$mco = new Application_Model_DbTable_McoDiffRoulette();
		$delete = $mco->fetchAll($mco->select()->where('mco_id = ?',$id));
		if($delete)
		{
			foreach($delete as $deleteDiffRoulette){
				//$deleteDiffRoulette->delete();
				$deleteDiffRoulette->status = 0;
				$teste = $deleteDiffRoulette->save();
			}
		}

		$save=$this->saveLogDiff(null,$id,3);
		return true;
	}

	public function saveLogMco($mco_new_id,$mco_old_id,$action_type)
	{
		try { 
			$user = new Application_Model_DbTable_User();
 			$authNamespace = new Zend_Session_Namespace('userInformation');
 			$user = $authNamespace->user_id;

 			$data['mco_new_id'] = $mco_new_id;
			$data['mco_old_id'] = $mco_old_id;
			$data['registration_date'] = date('Y-m-d H:i:s');
			$data['user'] = $user;
			$data['action_type'] = $action_type;
			$mcoDeleteDayLog = new Application_Model_DbTable_McoLog();
			$mcoDeleteDayLogNew = $mcoDeleteDayLog->createRow($data);
			$save = $mcoDeleteDayLogNew->save();
			return $save;
		}catch(Zend_Exception $e) {

		}
	}

	public function lockDay($id)
	{
		try { 

			$user = new Application_Model_DbTable_User();
 			$authNamespace = new Zend_Session_Namespace('userInformation');
 			$user = $authNamespace->user_id;

 			$mco = new Application_Model_DbTable_Mco();
			$editMco = $mco->fetchRow($mco->select()->where('id = ?',$id));
			$editMcoNew['id'] = null;
			$editMcoNew['date_file'] = $editMco->date_file;
			$editMcoNew['date'] = $editMco->date;
			$editMcoNew['date_operation'] = $editMco->date_operation;
			$editMcoNew['status'] = 0;
			$editMcoNew['lock_day'] = 1;
			$editMcoNew['user'] = $editMco->user;
			$mcoNew = $mco->createRow($editMcoNew);
			$save = $mcoNew->save();

			$lockDay = $mco->fetchRow($mco->select()->where('id = ?',$id));
			if($lockDay)
			{
				$lockDay->id = $id;
				$lockDay->lock_day = 0;
				$lockDay->date = date('Y-m-d H:i:s');
				$lockDay->user = $user;
				$lockDay->save();
			}
			// the new id is the field $id and the old id is $save because the really new id($save) there isn't references in the sytem
			$this->saveLogMco($id,$save,2);
			return true;
		}catch(Zend_Exception $e) {

		}
	}

	public function mcoDate($mco_id){
		$mco = new Application_Model_DbTable_Mco();
		return $mco->fetchRow($mco->select()->where('id = ?',$mco_id));
	}

}

