<?php

class Application_Model_QcoQH extends Application_Model_Qco
{

	private $file;
	private $numberCommunication;
	private $qcoId;

	public function checkFile($files,$qcoId)
	{
		$this->file = fopen($files['qco_file']['tmp_name'], 'r');
		$startLine = fgets($this->file);
		$tes = explode(' ', $startLine);
		$startLine = $tes[0];
		if($startLine[0].$startLine[1] == '01')
		{
			$qco = new Application_Model_Qco();
			$numberCommunication = $startLine[2].$startLine[3].$startLine[4].$startLine[5];
			$qcoRow = $qco->returnByNumberCommunication($numberCommunication);
			if($qcoRow)
			{
				return true;
			}
		}
		return false;
	}

	public function saveHoursFleet($qcoId)
	{
		while (!feof($this->file)) 
		{
			try{
		      $buffer = fgets($this->file, 4096);
		      if($buffer[0].$buffer[1] == '02')
		      {
		      	$typeDay = $this->returnTypeDay($buffer[2].$buffer[3]);
		      	$pc = $buffer[4];
				$this->truncateRowTypeDay($qcoId, $typeDay->id,$pc);
		      }
		      if($buffer[0].$buffer[1] == '03')
		      {
		      	if($typeDay->id != '' && $pc != '')
		      	{
		      		$this->saveHourQH($qcoId, $typeDay->id, $typeDay->name, $pc, $buffer[2].$buffer[3].$buffer[4].$buffer[5], $buffer[6].$buffer[7]);
		      	}
		      }
		      if($buffer[0].$buffer[1] == '09')
		      {
		      	// 09|01|00|00|00|00|00|00|00|00|00|00|01|01|01|00|00|00|01|01|01|00|00|00|00|00|
		      	$aux = explode('|', $buffer);
		      	$typeDay = $this->returnTypeDay($aux[1]);
		      	$this->saveFleet($qcoId, $typeDay , $aux);
		      }
			}catch(Zend_Exception $e){
		        echo $e->getMessage();
		    }
    	}
    return true;
	}

	private function saveHourQH($qcoId, $typeDayId,$typeDayName, $pc, $hour, $typeJourney)
	{
		$typeJourneyRow = $this->returnTypeJourney($typeJourney);
		$qco = new Application_Model_DbTable_QcoHour();
		$qcoNew = $qco->createRow();
		$qcoNew->id_type_day = $typeDayId;
		$qcoNew->name = $typeDayName. ' PC ' . $pc;
		$qcoNew->hour = $hour;
		$qcoNew->id_type_journey = $typeJourneyRow->id;
		$qcoNew->qco_id = $qcoId;
		$qcoNew->pc = $pc;
		return $qcoNew->save();
	}

	public function returnTypeDay($typeDayId)
	{
		$qco = new Application_Model_DbTable_QcoTypeDay();
		return $qco->fetchRow($qco->select()->where('id = ?',$typeDayId) );
	}

	public function returnTypeJourney($typeJourney)
	{
		if($typeJourney == 'PD')
			$IdTypeJourney = 1;
		if($typeJourney == 'RI')
			$IdTypeJourney = 2;
		if($typeJourney == 'PI')
			$IdTypeJourney = 3;
		if($typeJourney == 'NT')
			$IdTypeJourney = 4;
		if($typeJourney == 'OU')
			$IdTypeJourney = 5;
		if($typeJourney == 'AL')
			$IdTypeJourney = 6;

		$qco = new Application_Model_DbTable_QcoTypeJourney();
		return $qco->fetchRow($qco->select()->where('id = ?',$IdTypeJourney) );
	}

	private function truncateRowTypeDay($qcoId, $typeDay,$pc)
	{
		$qco = new Application_Model_DbTable_QcoHour();
		$qcoRows = $qco->fetchAll($qco->select()->where('qco_id = ?', $qcoId)->where('id_type_day = ?', $typeDay)->where('pc = ?',$pc));
		if($qcoRows){
			foreach($qcoRows as $hours)
			{
				$hours->delete();
			}
		}
	}

	private function saveFleet($qcoId, $typeDay, $data)
	{
		$this->truncateRowFleet($qcoId, $typeDay->id);

		$qcoFleet = new Application_Model_DbTable_QcoFleet();
		$qcoFleetNew = $qcoFleet->createRow();
		$qcoFleetNew->id_type_day = $typeDay->id;
		$qcoFleetNew->hour_00 = $data[2];
		$qcoFleetNew->hour_01 = $data[3];
		$qcoFleetNew->hour_02 = $data[4];
		$qcoFleetNew->hour_03 = $data[5];
		$qcoFleetNew->hour_04 = $data[6];
		$qcoFleetNew->hour_05 = $data[7];
		$qcoFleetNew->hour_06 = $data[8];
		$qcoFleetNew->hour_07 = $data[9];
		$qcoFleetNew->hour_08 = $data[10];
		$qcoFleetNew->hour_09 = $data[11];
		$qcoFleetNew->hour_10 = $data[12];
		$qcoFleetNew->hour_11 = $data[13];
		$qcoFleetNew->hour_12 = $data[14];
		$qcoFleetNew->hour_13 = $data[15];
		$qcoFleetNew->hour_14 = $data[16];
		$qcoFleetNew->hour_15 = $data[17];
		$qcoFleetNew->hour_16 = $data[18];
		$qcoFleetNew->hour_17 = $data[19];
		$qcoFleetNew->hour_18 = $data[20];
		$qcoFleetNew->hour_19 = $data[21];
		$qcoFleetNew->hour_20 = $data[22];
		$qcoFleetNew->hour_21 = $data[23];
		$qcoFleetNew->hour_22 = $data[24];
		$qcoFleetNew->hour_23 = $data[25];
		$qcoFleetNew->qco_id = $qcoId;
		return $qcoFleetNew->save();
	}

	private function truncateRowFleet($qcoId, $typeDay)
	{
		$qcoFleet = new Application_Model_DbTable_QcoFleet();
		$qcoRow = $qcoFleet->fetchRow($qcoFleet->select()->where('qco_id = ?', $qcoId)->where('id_type_day = ?', $typeDay) );
		if($qcoRow != ''){
			foreach($qcoRow as $fleet)
			{
				$qcoRow->delete();
			}
		}
	}

}

