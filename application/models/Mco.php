<?php

class Application_Model_Mco
{

	protected $mcoId;
	private $amount = array(),
  				$amountCash = array(),
  				$maxFare = array();

	/**
	*	Load a new file of mco. Each part of the file will be inserted in the properly part.
	*	
	* @param $_FILE $file - mco's file
	* @access public
	* @return integer
	*/
	public function loadFile($file)
	{
		$handle = @fopen($file['mco_file']['tmp_name'], "r");
    if ($handle) 
    {
      while (!feof($handle)) 
      {
        $buffer = fgets($handle, 4096);
        $splited = explode (';',$buffer);
        if($splited[0] == '0')
        {
        	if(!$this->registerNew($splited))
        	{
        		return false;
        	}
        }
        if($splited[0] == '1')
        {
        	$this->registerData($splited);
        }
        if($splited[0] == '3')
        {
        	$this->diffRoulette($splited);
        }
        if($splited[0] == '4')
        {
        	$this->registerCash($splited);
        }
        if($splited[0] == '5')
        {
        	$this->adjustmentsRoulette($splited);
        }
      }
      fclose($handle);
    }
    $this->updateCash();
  	return $this->mcoId;
	}

	/**
	*	Register a new mco day on the system. It is possible to import a new mco or a mco that has been deleted(all status == 0)
	*	
	* @param array $data - mco's data
	* @access public
	* @return integer
	*/
	protected function registerNew($data)
	{
  	$authNamespace = new Zend_Session_Namespace('userInformation');
		$mco = new Application_Model_DbTable_Mco();
		$mcoRow = $mco->fetchAll($mco->select()->where('date_operation LIKE ?',Application_Model_General::dateToUs($data[2])));
		foreach ($mcoRow as $value) {
			if($value->status == 1)
				return false;
		}

		$mcoNew = $mco->createRow();
		$mcoNew->user = $authNamespace->user_id; 
		$mcoNew->date = new Zend_Db_Expr('NOW()');
		$mcoNew->date_file = Application_Model_General::dateToUs($data[3]);
		$mcoNew->date_operation = Application_Model_General::dateToUs($data[2]);
		$mcoNew->status = 1;
		$mcoNew->lock_day = 1;
		$this->mcoId = $mcoNew->save();

		if($this->mcoId)
		{
			return true;
		}
		return false;
	}

	/**
	*	Register a new mco data on the system. This is the main data of buses.
	*	
	* @param array $data - mco's data
	* @access protected
	* @return null
	*/
	protected function registerData($data)
	{
		$mcoData = new Application_Model_DbTable_McoData();
		$mcoDataNew = $mcoData->createRow();
		$mcoDataNew->mco = $this->mcoId;
		$mcoDataNew->line = $data[1];
		if($data[3] == 'PD')
			$type = 1;
		if($data[3] == 'RI')
			$type = 2;
		if($data[3] == 'PI')
			$type = 3;
		if($data[3] == 'NT')
			$type = 4;
		if($data[3] == 'OU') //outros 
			$type = 5;
		if($data[3] == 'AL') // alternativos
			$type = 6;
		$mcoDataNew->type = $type;
		$mcoDataNew->vehicle_number = $data[4];
		$mcoDataNew->start_hour = Application_Model_General::convertHour($data[5]);
		$mcoDataNew->mid_hour = Application_Model_General::convertHour($data[6]);
		$mcoDataNew->end_hour = Application_Model_General::convertHour($data[7]);
		$mcoDataNew->start_roulette = $data[8];
		if($data[9]) $mcoDataNew->mid_roulette = $data[9];
		$mcoDataNew->end_roulette = $data[10];
		$mcoDataNew->incident = $this->returnIncident($data[18]);
		$mcoDataNew->traffic_jam = $this->returnTrafficJam($data[19]);
		$mcoDataNew->travel_interrupted = $this->returnTravelInterrupted($data[17]);
		$amount_passenger = $data[10] - $data[8];
		if($amount_passenger<0){
			$amount_passenger = (100000-$data[8])+$data[10];
		}
		$mcoDataNew->amount_passenger = $amount_passenger;
		$this->countPassengers($data[1],$amount_passenger);
		$mcoDataNew->start_date = Application_Model_General::dateToUs($data[14]);
		if($data[15]) $mcoDataNew->mid_date = Application_Model_General::dateToUs($data[15]);
		$mcoDataNew->end_date = Application_Model_General::dateToUs($data[16]);
		$mcoDataNew->imported = 1;
		$mcoDataNew->status = 1;
		$mcoDataNew->save();
	}

	protected function returnTravelInterrupted($travelInterrupted)
	{
		if($travelInterrupted == 'SIM')
			return 1;
		return 0;
	}

	protected function returnIncident($incident)
	{
		if($incident == 'TC')
			return 'a';
		elseif ($incident == 'PAN')
			return 'b';
		elseif ($incident == 'SIN')
			return 'c';
		elseif ($incident == 'AST')
			return 'd';
		return $incident;
	}

	protected function returnTrafficJam($trafficJam)
	{
		if($trafficJam == 'SIM')
			return 1;
		return 0;
	}

	/**
	*	Keep a count of passengers in array. It is easier and faster than query on database.
	*	
	* @param array $data - mco's data
	* @access protected
	* @return null
	*/
	protected function countPassengers($line, $amount)
	{
		$line = intval($line);
		$amount = intval($amount);
		if(array_key_exists($line, $this->amount))
		{
			$this->amount[$line] = $this->amount[$line] + $amount;
		}
		else
		{
			$this->amount[$line] = $amount;
		}
	}
	/**
	*	Keep a count of passengers in mco_cash in array. It is easier and faster than query on database.
	*	
	* @param array $data - mco's data
	* @access protected
	* @return null
	*/
	protected function countPassengersCash($line, $amount)
	{
		$line = intval($line);
		$amount = intval($amount);
		if(array_key_exists($line, $this->amountCash))
		{
			$this->amountCash[$line] = $this->amountCash[$line] + $amount;
		}
		else
		{
			$this->amountCash[$line] = $amount;
		}
	}

	/**
	*	Register a new mco cash on the system. This is the main data of buses.
	*	
	* @param array $data - mco's cash
	* @access protected
	* @return null
	*/
	protected function registerCash($data)
	{
		$mcoCash = new Application_Model_DbTable_McoCash();
		$mcoCashNew = $mcoCash->createRow();
		$mcoCashNew->mco_id = $this->mcoId;
		$mcoCashNew->line = $data[1];
		$mcoCashNew->type = $data[2];
		$mcoCashNew->amount = $data[3];
		$mcoCashNew->value = str_replace(',', '.', $data[4]);
		$mcoCashNew->diff = $data[5];
		$this->countPassengersCash($data[1],$data[3]);
		$this->getMaximumFare($data[1],$mcoCashNew->value);
		$mcoCashNew->save();
	}

	/**
	*	Register a new mco diff roulette on the system. This is the main data of buses.
	*	
	* @param array $data - mco's diff roulette
	* @access protected
	* @return null
	*/
	protected function diffRoulette($data)
	{
		$mcoDiffRoulette = new Application_Model_DbTable_McoDiffRoulette();
		$mcoDiffRouletteNew = $mcoDiffRoulette->createRow();
		$mcoDiffRouletteNew->mco_id = $this->mcoId;
		$mcoDiffRouletteNew->line = $data[1];
		$mcoDiffRouletteNew->vehicle = $data[2];
		$mcoDiffRouletteNew->roulette_before = $data[5];
		$mcoDiffRouletteNew->roulette_after = $data[6];
		$mcoDiffRouletteNew->date_before = Application_Model_General::dateToUs($data[8]);
		$mcoDiffRouletteNew->date_after = Application_Model_General::dateToUs($data[9]);
		if((($data[6] - $data[5]) > 300 && ($data[8] == $data[9] || $data[8]==Application_Model_General::dateBefore($data[9]))) || ($data[6] - $data[5]) < 0){
			$mcoDiffRouletteNew->accredit_passenger = $data[6] - $data[5];
			$mcoDiffRouletteNew->justify = 'ERRO DE VALIDADOR';
		}
		$mcoDiffRouletteNew->status = 1;
		$mcoDiffRouletteNew->save();
	}

	/**
	*	Register a new mco adjustments roulette on the system. This is the main data of buses.
	*	
	* @param array $data - mco's adjustments roulette
	* @access protected
	* @return null
	*/
	protected function adjustmentsRoulette($data)
	{
		$mcoAdjustmentsRoulette = new Application_Model_DbTable_McoAdjustmentsRoulette();
		$mcoAdjustmentsRouletteNew = $mcoAdjustmentsRoulette->createRow();
		$mcoAdjustmentsRouletteNew->mco_id = $this->mcoId;
		$mcoAdjustmentsRouletteNew->line = $data[1];
		$mcoAdjustmentsRouletteNew->vehicle = $data[2];
		$mcoAdjustmentsRouletteNew->roulette = $data[4];
		$mcoAdjustmentsRouletteNew->date = Application_Model_General::dateToUs($data[5]);
		$mcoAdjustmentsRouletteNew->save();
	}

	protected function getMaximumFare($line, $cash)
	{
		$line = intval($line);
		$cash = floatval($cash);
		if(array_key_exists($line, $this->maxFare))
		{
			if($this->maxFare[$line] < $cash)
				$this->maxFare[$line] = $cash;
		}
		else
		{
			if($cash) $this->maxFare[$line] = $cash;
		}
	}

	/**
	*	Register on mco_cash a money amount.
	*	
	* @access protected
	* @return null
	*/
	protected function updateCash()
	{
		foreach($this->amount as $line => $amount)
		{
			if(isset($this->maxFare[$line]) && $this->maxFare[$line])
			{
				$mcoCash = new Application_Model_DbTable_McoCash();
				$mcoCashNew = $mcoCash->createRow();
				$mcoCashNew->mco_id = $this->mcoId;
				$mcoCashNew->line = $line;
				$mcoCashNew->type = 'DNH';
				$mcoCashNew->amount = ($amount - $this->amountCash[$line]);
				$mcoCashNew->value = $this->maxFare[$line];
				$mcoCashNew->diff = 0;
				$mcoCashNew->save();
			}
		}	
	}

	public function lists()
	{
		$mco = new Application_Model_DbTable_Mco();
		$select = $mco->select()->setIntegrityCheck(false);
		$select	->from(array('m' => 'mco'),array( 'month' => 'DATE_FORMAT(date_operation,"%m-%Y")', 
									'date_operation' => 'DATE_FORMAT(date_operation,"%m/%Y")') )
						->where('status=1')
						->group('MONTH(date_operation)')
						->group('YEAR(date_operation)')
						->order('YEAR(date_operation) desc')
						->order('MONTH(date_operation) desc');
		return $mco->fetchAll($select);
	}

	public function returnMonth($period)
	{
		$aux = explode('-',$period);
		$mco = new Application_Model_DbTable_Mco();
		return $mco->fetchAll($mco->select()->where('MONTH(date_operation) = ?',$aux[0])
											->where('YEAR(date_operation) = ?', $aux[1])
											->where('status=1')
											->order('date_operation'));
	}

	public function returnByDate($date)
	{
		$mco = new Application_Model_DbTable_Mco();
		$select = $mco->select()->setIntegrityCheck(false);
		$select	->from(array('m' => 'mco'),array( 'month' => 'DATE_FORMAT(date_operation,"%m-%Y")', 
									'date_operation' => 'DATE_FORMAT(date_operation,"%m/%Y")') )
						->where('DATE_FORMAT(date_operation,"%m/%Y") = ?', $date)
						->where('status=1')
						->group('MONTH(date_operation)')
						->group('YEAR(date_operation)');
		return $mco->fetchAll($select);
	}

	public function returnCountFares($startDate,$endDate)
	{
		$mcoCash = new Application_Model_DbTable_McoCash();
		$select = $mcoCash->select()->setIntegrityCheck(false);
		$select	->from(array('m' => 'mco'),array() )
						->joinInner(array('mc' => 'mco_cash'),'mc.mco_id=m.id', array(
															'amount' => new Zend_Db_Expr('SUM(amount)'),
															'value', 'type', 'diff',
															'revenue' => new Zend_Db_Expr('value*(SUM(amount))')) )
						->where('m.date_operation >= ?',Application_Model_General::dateToUs($startDate))
						->where('m.date_operation <= ?',Application_Model_General::dateToUs($endDate))
						->group('mc.value');
		return $mcoCash->fetchAll($select);
	}

	public function calculateRevenue($listFares)
	{
		$totalPassengers = 0;
		$totalRevenue = 0;
		foreach ($listFares as $fares)
		{
			$totalPassengers += intval($fares->amount);
			$totalRevenue += floatval($fares->revenue);
		}
		return array(
				'totalPassengers' => $totalPassengers,
				'totalRevenue' 		=> $totalRevenue,
			);
	}

	public function returnGroupFares($startDate,$endDate)
	{
		$mcoArray = array();
		$mcoArray['totalPassengers'] = 0;
		$mcoArray['totalRevenue'] = 0;
		$mcoCash = new Application_Model_DbTable_McoCash();
		$select = $mcoCash->select()->setIntegrityCheck(false);
		$select	->from(array('m' => 'mco'),array() )
						->joinInner(array('mc' => 'mco_cash'),'mc.mco_id=m.id', array(
															'amount', 'value', 'type', 'diff', 'revenue' => new Zend_Db_Expr('NULL')) )
						->where('m.date_operation >= ?',Application_Model_General::dateToUs($startDate))
						->where('m.date_operation <= ?',Application_Model_General::dateToUs($endDate))
						->order('mc.type');
		$mcoCashRow = $mcoCash->fetchAll($select);
		return $this->calculateResultsFinanceReport($mcoCashRow);
	}

	private function calculateResultsFinanceReport($mcoCashRow)
	{
		foreach($mcoCashRow as $cash)
		{
			$amount = intval($cash->amount);
			$value = floatval($cash->value);
			$diff = floatval(str_replace(',', '.', $cash->diff));
			$mcoArray['totalPassengers'] += $amount;
			$mcoArray['totalRevenue'] += $amount*$value;
			$mcoArray['totalCbtu'] += $amount*$diff;
		}

		foreach($mcoCashRow as $cash)
		{
			$amount = intval($cash->amount);
			$value = floatval($cash->value);
			if(array_key_exists($cash->type, $mcoArray))
			{
				$mcoArray[$cash->type]['amount'] += $amount;
				$mcoArray[$cash->type]['revenue'] += $amount*$value;
				$mcoArray[$cash->type]['cbtu'] += ($amount*$value)-($amount*$cash->diff);
			}
			else
			{
				$mcoArray[$cash->type]['type'] = $cash->type;
				$mcoArray[$cash->type]['diff'] = str_replace(',', '.', $cash->diff);
				$mcoArray[$cash->type]['amount'] = $amount;
				$mcoArray[$cash->type]['revenue'] = $amount*$value;
			}
		}
		return $mcoArray;
	}

	/**
	*	Return the passengers that paid by money and the total price that they paid.
	*	
	* @param integer $total_passenger - total of passenger
	* @param integer $subtotal_passenger - total of passenger in mco_cash
	* @param date $start_date - start date of research report
	* @param date $end_date - end date of research report
	* @access public
	* @return array
	*/
	public function returnMoney($total_passenger,$subtotal_passenger,$start_date,$end_date){
		$line = $this->returnLines($start_date,$end_date);
		$money[0] = 0;
		$money[1] = 0;
		foreach ($line as $lines) {
			$total_line = $this->returnTotalPassengerByLine($lines->line,$start_date,$end_date);
			$subtotal_line = $this->returnSubtotalPassengerByLine($lines->line,$start_date,$end_date);
			if($subtotal_line->subtotal_passenger_by_line == ''){
				$subtotal_line->subtotal_passenger_by_line=0;
			}
			$passenger_dnh = 0;
			$passenger_dnh = $total_line->total_passenger_by_line - $subtotal_line->subtotal_passenger_by_line;
			$fare = $this->returnFinaceFare($lines->line,$lines->mco);
			if($fare == '') {
				$fare=2.90; // buscar o valor do QCO
			}
			$money[0] += $passenger_dnh; //total passageiros que pagaram no dinheiro
			$money[1] += ($passenger_dnh * $fare) - (($passenger_dnh * $fare)*0.0037);// total da receita - 0,37% de taxa
		}
	return $money;
	}

	/**
	*	Return the passengers that paid by money and the price that they paid BY LINE.
	*	
	* @param integer $total_passenger - total of passenger
	* @param integer $subtotal_passenger - total of passenger in mco_cash
	* @param date $start_date - start date of research report
	* @param date $end_date - end date of research report
	* @access public
	* @return array
	*/
	public function returnMoneyFare($total_passenger,$subtotal_passenger,$start_date,$end_date){
		$line = $this->returnLines($start_date,$end_date);
		$money[0] = 0;
		$money[1] = 0;
		$money[2] = 0;
		$x=0;
		foreach ($line as $lines) {
			$total_line = $this->returnTotalPassengerByLine($lines->line,$start_date,$end_date);
			$subtotal_line = $this->returnSubtotalPassengerByLine($lines->line,$start_date,$end_date);
			if($subtotal_line->subtotal_passenger_by_line == ''){
				$subtotal_line->subtotal_passenger_by_line=0;
			}
			$passenger_dnh = 0;
			$passenger_dnh = $total_line->total_passenger_by_line - $subtotal_line->subtotal_passenger_by_line;
			$fare = $this->returnFinaceFare($lines->line,$lines->mco);
			if($fare == '') {
				$fare=2.90; // buscar o valor do QCO
			}
			$money[0] = $passenger_dnh; //total passageiros que pagaram no dinheiro
			$money[1] = ($passenger_dnh * $fare); // total da receita bruta  //- (($passenger_dnh * $fare)*0.0037);// total da receita - 0,37% de taxa
			$money[2] = $lines->line; // linha
			$money[3] = $fare; // tarifa dessa linha, considerada no calculo
			$vet_money[$x] = $money;
			$x++;
		}
	return $vet_money;
	}

	/**
	*	Return all the active and distinct lines in mco_data, filter by the report date.
	*	
	* @param date $start_date - start date of research report
	* @param date $end_date - end date of research report
	* @access public
	* @return array
	*/
	public function returnLines($start_date,$end_date){
		$mcoData = new Application_Model_DbTable_McoData();
		$select = $mcoData->select()->setIntegrityCheck(false);
		$select ->from(array("mc" => "mco_data"),array('DISTINCT(line),mco'))
					->where('start_date >= ?', Application_Model_General::dateToUs($start_date))
					->where('start_date <= ?', Application_Model_General::dateToUs($end_date))
					->where('status=1');
	return $mcoData->fetchAll($select);
	}

	/**
	*	Return the total of actives passengers by line and filter by the report date.
	*	
	* @param integer $line - line
	* @param date $start_date - start date of research report
	* @param date $end_date - end date of research report
	* @access public
	* @return array
	*/
	public function returnTotalPassengerByLine($line,$start_date,$end_date){
		$mcoData = new Application_Model_DbTable_McoData();
		$select = $mcoData->select()->setIntegrityCheck(false);
		$select ->from(array("mc" => "mco_data"), array('total_passenger_by_line' => new Zend_Db_Expr('SUM(amount_passenger)')))
					->where('start_date >= ?', Application_Model_General::dateToUs($start_date))
					->where('start_date <= ?', Application_Model_General::dateToUs($end_date))
					->where('line = ?',$line)
					->where('status=1');
	return $mcoData->fetchRow($select);
	}

	/**
	*	Return the difference roulette data actives and filter by the report date. 
	*	Calculate the quantity of accredit passenger and the fare of this passengers.
	*	
	* @param date $start_date - start date of research report
	* @param date $end_date - end date of research report
	* @access public
	* @return array
	*/
	public function returnDiffRoulette($start_date,$end_date){
		$mco = new Application_Model_DbTable_Mco();
		$select = $mco->select()->setIntegrityCheck(false);
		$select	->from(array('mdr' => 'mco_diff_roulette'))
				->joinInner(array('m' => 'mco'), 'm.id=mdr.mco_id')
				->where('m.date_operation >=?', Application_Model_General::dateToUs($start_date))
				->where('m.date_operation <= ?', Application_Model_General::dateToUs($end_date))
				->where('mdr.status=1');// conferir como funciona este status
		$mco_diff_roulette = $mco->fetchAll($select);
		$diff_roulette[0]=0;
		$diff_roulette[1]=0;
		foreach ($mco_diff_roulette as $diffRoulette) {
			$diff = $diffRoulette->roulette_after - $diffRoulette->roulette_before;
			if($diffRoulette->accredit_passenger != 0 && $diff > $diffRoulette->accredit_passenger){
				$diff_roulette[0] += $diff - $diffRoulette->accredit_passenger;
				$fare = $this->returnFinaceFare($diffRoulette->line,$diffRoulette->mco_id);
				//quantidade de passageiros * tarifa da linha - 0,37% de taxa
				$diff_roulette[1] += (($diff - $diffRoulette->accredit_passenger) * $fare) - ((($diff - $diffRoulette->accredit_passenger) * $fare)*0.0037);
			}
		}
	return $diff_roulette;
	}

	/**
	*	Return the line's finance fare .
	*	
	* @param date $start_date - start date of research report
	* @param date $end_date - end date of research report
	* @access public
	* @return array
	*/
	public function returnFinaceFare($line,$mco_id){
		$mco = new Application_Model_DbTable_Mco();
		$select = $mco->select()->setIntegrityCheck(false);
		$select ->from(array("mc" => "mco_cash"))
						->joinInner(array('m' => 'mco'), 'm.id=mc.mco_id')
						->where('type = "VTE"')
						->where('line = ?', $line)
						->where('mco_id= ?', $mco_id);
		$mcoCash = $mco->fetchRow($select);	
	return $mcoCash->value;
	}

	public function reportCashTotalPassenger($diff, $inicio,$fim){
		$mcoCash = new Application_Model_DbTable_Mco();
		$select = $mcoCash->select()->setIntegrityCheck(false);
		$select ->from(array("mc" => "mco_cash"), array('total_passenger' => new Zend_Db_Expr('SUM(amount) -'.$diff)))
						->joinInner(array('m' => 'mco'), 'm.id=mc.mco_id')
						->where('date_operation >= ?', Application_Model_General::dateToUs($inicio))
						->where('date_operation <= ?', Application_Model_General::dateToUs($fim));
	return $mcoCash->fetchRow($select);
	}

	/**
	*	Return the passenger's total from mco_cash filter by the report date.
	*	
	* @param date $start_date - start date of research report
	* @param date $end_date - end date of research report
	* @access public
	* @return array
	*/
	public function returnSubtotalPassenger($start_date,$end_date){
		$mcoCash = new Application_Model_DbTable_Mco();
		$select = $mcoCash->select()->setIntegrityCheck(false);
		$select ->from(array("mc" => "mco_cash"), array('subtotal_passenger' => new Zend_Db_Expr('SUM(amount)')))
						->joinInner(array('m' => 'mco'), 'm.id=mc.mco_id')
						->where('date_operation >= ?', Application_Model_General::dateToUs($start_date))
						->where('date_operation <= ?', Application_Model_General::dateToUs($end_date));
	return $mcoCash->fetchRow($select);
	}

	/**
	*	Return the passenger's total active from mco_cash BY LINE and filter by the report date.
	*	
	* @param integer $line - line
	* @param date $start_date - start date of research report
	* @param date $end_date - end date of research report
	* @access public
	* @return array
	*/
	public function returnSubtotalPassengerByLine($line,$start_date,$end_date){
		$mco = new Application_Model_DbTable_Mco();
		$select = $mco->select()->setIntegrityCheck(false);
		$select ->from(array("mc" => "mco_cash"), array('subtotal_passenger_by_line' => new Zend_Db_Expr('SUM(amount)')))
						->joinInner(array('m' => 'mco'), 'm.id=mc.mco_id')
						->where('date_operation >= ?', Application_Model_General::dateToUs($start_date))
						->where('date_operation <= ?', Application_Model_General::dateToUs($end_date))
						->where('line = ?',$line)
						->where('status=1');
		$subtotal = $mco->fetchRow($select);
	return $subtotal;
	}

	/**
	*	Calculate and populate the field: amount_passenger, from mco_data.
	*	
	* @access public
	* @return true or false
	*/
	public function populateMcoData(){
		$mcoDiffs = new Application_Model_DbTable_McoData();
		$editAccredit = $mcoDiffs->fetchAll($mcoDiffs->select()->where('mco = 9'));
		foreach ($editAccredit as $edit) {
			$edit['id'] = $edit->id;
			$edit['amount_passenger'] = $edit->end_roulette - $edit->start_roulette;
			$edit->save();
		}
	}

	/**
	*	Return all the actives passengers from mco_data, filter by the report date and considering the accredit_passenger in this total
	*	
	* @param integer $accredit_passenger - number of accredit passenger
	* @param date $start_date - start date of research report
	* @param date $end_date - end date of research report
	* @access public
	* @return array
	*/
	public function returnTotalPassenger($accredit_passenger, $start_date,$end_date){
		$mcoData = new Application_Model_DbTable_McoData();
		$select = $mcoData->select()->setIntegrityCheck(false);
		$select ->from(array("mc" => "mco_data"), array('total_passenger' => new Zend_Db_Expr('SUM(amount_passenger) +'.$accredit_passenger)))
					->where('start_date >= ?', Application_Model_General::dateToUs($start_date))
					->where('start_date <= ?', Application_Model_General::dateToUs($end_date))
					->where('status=1');
	return $mcoData->fetchRow($select);
	}

	/**
	*	Return the data from mco_cash, filter by the report date and the active data, group by the type of payment.
	*	considering the CBTU's taxes when is necessarie.
	*	
	* @param date $start_date - start date of research report
	* @param date $end_date - end date of research report
	* @access public
	* @return array
	*/
	public function returnPaymentType($start_date,$end_date){
		$mcoCash = new Application_Model_DbTable_McoCash();
		$select = $mcoCash->select()->setIntegrityCheck(false);
		$select ->from(array("mc" => "mco_cash"), array('type','amount' => new Zend_Db_Expr('SUM(amount)'),
														'full_composition' => new Zend_Db_Expr('CAST(SUM(value*amount) as DECIMAL(11,3))'),
														'liquid_composition' => new Zend_Db_Expr('(SUM(CASE
														 WHEN type = "IOM" THEN 
														 CAST((((value- 1.449)*amount)-(((value- 1.449)*amount)*0.0037)) as DECIMAL(11,2))
														 WHEN type = "IMO" THEN 
														 CAST((((value- 1.449)*amount)-(((value- 1.449)*amount)*0.0037)) as DECIMAL(11,2))
														 ELSE CAST((value*amount)-((value*amount)*0.0037) as DECIMAL(11,2)) END))'),
														'cbtu_transfer' => new Zend_Db_Expr('SUM(CASE 
														 WHEN type = "IOM" THEN CAST(1.449*amount as DECIMAL(11,2))
														 WHEN type = "IMO" THEN CAST(1.449*amount as DECIMAL(11,2))
														 ELSE 0 END)')))
		->joinInner(array('m' => 'mco'), 'm.id=mc.mco_id')
		->where('date_operation >= ?', Application_Model_General::dateToUs($start_date))
		->where('date_operation <= ?', Application_Model_General::dateToUs($end_date))
		->where('status=1')
		->group('type');
		return $mcoCash->fetchAll($select);
	}

	/**
	*	Return the totals of the fields: full_composition, liquid_composition and cbtu_transfer, sum the values of each one.
	*	
	* @param date $start_date - start date of research report
	* @param date $end_date - end date of research report
	* @param real $dhn - money's finance composition
	* @param real $dc - diffence of roulette's finance composition
	* @access public
	* @return array
	*/
	public function returnTotals($start_date,$end_date,$dhn,$dc){
		$totals_type = $this->returnPaymentType($start_date,$end_date);
		$total_full_composition = 0;
		$total_liquid_composition = 0;
		$total_cbtu_transfer = 0;
		foreach ($totals_type as $totals) {
			$total_full_composition += $totals->full_composition;
			$total_liquid_composition += $totals->liquid_composition;
			$total_cbtu_transfer += $totals->cbtu_transfer;
		}
		$total_full_composition += $dhn+$dc;
		$total_liquid_composition += $dhn+$dc;
		$total[0]=$total_full_composition;
		$total[1]=$total_liquid_composition;
		$total[2]=$total_cbtu_transfer;
	return $total;
	}

	/**
	*	Return the percent from the total of each field
	*	
	* @param date $start_date - start date of research report
	* @param date $end_date - end date of research report
	* @param integer $total_passenger - passenger's total
	* @param integer $amount_dc - total passengers who "paid" by difference of roulette
	* @param integer $amount_dhn - total passengers who paid in cash
	* @param real $composition_dc - total finance composition of passengers who "paid" by difference of roulette
	* @param real $composition_dhn - total finance composition of passengers who paid in cash
	* @access public
	* @return array
	*/
	public function returnPercent($start_date,$end_date,$total_passenger,$amount_dc,$amount_dhn,$composition_dc,$composition_dhn){
		$values_by_type = $this->returnPaymentType($start_date,$end_date);
		$x=0;
		$totals=$this->returnTotals($start_date,$end_date,$composition_dhn,$composition_dc);
		foreach ($values_by_type as $value) {
			$amount_percent[$x] = ($value->amount * 100)/$total_passenger;
			$full_composition_percent[$x] = ($value->full_composition * 100)/$totals[0];
			$liquid_composition_percent[$x] = ($value->liquid_composition * 100)/$totals[1];
			$cbtu_percent[$x] = ($value->cbtu_transfer * 100)/$totals[2];
			$x++;
		}
		$amount_percent[$x] = ($amount_dhn * 100)/$total_passenger;
		$amount_percent[$x+1] = ($amount_dc * 100)/$total_passenger;
		$full_composition_percent[$x] = ($composition_dhn * 100)/$totals[0];
		$full_composition_percent[$x+1] = ($composition_dc * 100)/$totals[0];
		$liquid_composition_percent[$x] = ($composition_dhn * 100)/$totals[1];
		$liquid_composition_percent[$x+1] = ($composition_dc * 100)/$totals[1];

		$percent[0]=$amount_percent;
		$percent[1]=$full_composition_percent;
		$percent[2]=$liquid_composition_percent;
		$percent[3]=$cbtu_percent;
	return $percent;
	}

  public function consortium(){
	$cell = new Application_Model_DbTable_Consortium;
	$select = $cell->select()->setIntegrityCheck(false);
	$select ->from(array("c" => "consortium"), array('name', 'id'))
					->order('name');
	return $cell->fetchAll($select);
  }

  public function listFare(){
	$fares = new Application_Model_DbTable_FinanceFare;
	$select = $fares->select()->setIntegrityCheck(false);
	$select ->from(array("f" => "finance_fare"))
					->group('value');
	return $fares->fetchAll($select);
  }


	public function reportCashSystemValue($start_date, $end_date, $total_passenger,$total_revenue,$total_cbtu_transfer,$total_liquid_revenue,$subtotal_passenger)
	{
		$mcoCash = new Application_Model_DbTable_Mco();
		$select = $mcoCash->select()->setIntegrityCheck(false);
		$select ->from(array("mc" => "mco_cash"), array('value'=> new Zend_Db_Expr('CAST(value as DECIMAL(4,2))'), 
			'amount' => new Zend_Db_Expr('SUM(amount)'),
			'amount_percent' => new Zend_Db_Expr('CAST((SUM(amount)*100/'.$total_passenger.') as DECIMAL(5,2))'), 
			'full_composition' => new Zend_Db_Expr('CAST(SUM(value*amount) as DECIMAL(11,2))'),
			'full_composition_percentage' => new Zend_Db_Expr('CAST((SUM(value*amount)*100/'.$total_revenue.') as DECIMAL(5,2))'),
			'cbtu_transfer' => new Zend_Db_Expr('
				SUM(CASE 
				WHEN type = "IOM" THEN CAST(1.449*amount as DECIMAL(11,2))
				WHEN type = "IMO" THEN CAST(1.449*amount as DECIMAL(11,2))
				ELSE 0
				END)'),
			'cbtu_transfer_percentage' => new Zend_Db_Expr('
				SUM(CASE 
				WHEN type = "IOM" THEN CAST((1.449*amount*100)/'.$total_cbtu_transfer.' as DECIMAL(5,2))
				WHEN type = "IMO" THEN CAST((1.449*amount*100)/'.$total_cbtu_transfer.' as DECIMAL(5,2))
				ELSE 0
				END)'),
			'liquid_composition' => new Zend_Db_Expr('
				SUM(CASE
				WHEN type = "IOM" THEN CAST((value- 1.449)*amount as DECIMAL(11,2))
				WHEN type = "IMO" THEN CAST((value- 1.449)*amount as DECIMAL(11,2))
				ELSE CAST((value*amount) as DECIMAL(11,2))
				END)'),
			'liquid_composition_percentage' => new Zend_Db_Expr('
				SUM(CASE
				WHEN type = "IOM" THEN CAST(((value- 1.449)*amount*100)/'.$total_liquid_revenue.' as DECIMAL(5,2))
				WHEN type = "IMO" THEN CAST(((value- 1.449)*amount*100)/'.$total_liquid_revenue.' as DECIMAL(5,2))
				ELSE CAST(((value*amount*100)/'.$total_liquid_revenue.') as DECIMAL(5,2))
				END)')
			))
		->joinInner(array('m' => 'mco'), 'm.id=mc.mco_id', array())
		->where('date_operation >= ?', Application_Model_General::dateToUs($start_date))
		->where('date_operation <= ?', Application_Model_General::dateToUs($end_date))
		->group('value');
		return $mcoCash->fetchAll($select);
	}


	public function reportCashCellPassengerDiff($inicio,$fim,$cell){

	$mcoCash = new Application_Model_DbTable_Mco();
	$select = $mcoCash->select()->setIntegrityCheck(false);
	$select ->from(array("mc" => "mco_cash"), array())
					->joinInner(array('mdr' => 'mco_diff_roulette'), 'mdr.mco_id=mc.mco_id', array(
						'total_diff' => new Zend_Db_Expr('
							SUM(DISTINCT(roulette_after - roulette_before - IF(accredit_passenger IS NULL, 0,accredit_passenger)))')))
					->joinInner(array('m' => 'mco'), 'm.id=mc.mco_id')
					->joinInner(array('q' => 'qco'), 'mc.line=q.number_communication')
					->joinInner(array('cc' => 'consortium_companies'), 'q.consortium_companies_id = cc.id')
					->where('date_operation >=?', Application_Model_General::dateToUs($inicio))
					->where('date_operation <= ?', Application_Model_General::dateToUs($fim))
					->where('cc.id = ?', $cell); 
	return $mcoCash->fetchRow($select);
	}

	public function reportCashCellTotalPassenger($diff, $inicio, $fim, $cell){
	if ($diff == "") {
		$diff = 0;
	}
	$mcoCash = new Application_Model_DbTable_Mco();
	$select = $mcoCash->select()->setIntegrityCheck(false);
	$select ->from(array("mc" => "mco_cash"), array('total_passenger' => new Zend_Db_Expr('SUM(amount) -'.$diff)))
					->joinInner(array('m' => 'mco'), 'm.id=mc.mco_id')
					->joinInner(array('q' => 'qco'), 'mc.line=q.number_communication')
					->joinInner(array('cc' => 'consortium_companies'), 'q.consortium_companies_id = cc.id')
					->where('date_operation >= ?', Application_Model_General::dateToUs($inicio))
					->where('date_operation <= ?', Application_Model_General::dateToUs($fim))
					->where('cc.id = ?', $cell);
	return $mcoCash->fetchRow($select);
	}

	public function reportCashCellTotal($inicio,$fim, $cell)
	{
		$mcoCash = new Application_Model_DbTable_Mco();
		$select = $mcoCash->select()->setIntegrityCheck(false);
		$select ->from(array("mc" => "mco_cash"), array(
			'total_revenue' => new Zend_Db_Expr('CAST(SUM(amount*value) as DECIMAL(11,2))'),
			'total_cbtu_transfer' => new Zend_Db_Expr('
			CAST(SUM(CASE 
			WHEN type = "IOM" THEN CAST(1.449*amount as DECIMAL(10,2))
			WHEN type = "IMO" THEN CAST(1.449*amount as DECIMAL(10,2))
			ELSE 0
			END) as DECIMAL(11,2))'),
			'total_liquid_revenue' => new Zend_Db_Expr('
			CAST(SUM(CASE
			WHEN type = "IOM" THEN CAST(((value- 1.449)*amount) as DECIMAL(10,2))
			WHEN type = "IMO" THEN CAST(((value- 1.449)*amount) as DECIMAL(10,2))
			ELSE (value*amount)
			END) as DECIMAL(11,2))')
			))
		->joinInner(array('m' => 'mco'), 'm.id=mc.mco_id', array())
		->joinInner(array('q' => 'qco'), 'mc.line=q.number_communication')
		->joinInner(array('cc' => 'consortium_companies'), 'q.consortium_companies_id = cc.id')
		->where('date_operation >=?', Application_Model_General::dateToUs($inicio))
		->where('date_operation <= ?', Application_Model_General::dateToUs($fim))
		->where('cc.id = ?', $cell);
		return $mcoCash->fetchRow($select);	
	}

	public function reportCashCellType($cell, $inicio, $fim, $total_passenger,$total_revenue,$total_cbtu_transfer,$total_liquid_revenue)
	{
		$mcoCash = new Application_Model_DbTable_Mco();
		$select = $mcoCash->select()->setIntegrityCheck(false);
		$select ->from(array("mc" => "mco_cash"), array('type',
			'amount' => new Zend_Db_Expr('SUM(amount)'),
			'amount_percent' => new Zend_Db_Expr('CAST(SUM(amount*100/'.$total_passenger.') as DECIMAL(5,2))'),
			'full_composition' => new Zend_Db_Expr('CAST(SUM(value*amount) as DECIMAL(11,3))'), 
			'full_composition_percentage' => new Zend_Db_Expr('CAST((SUM(value*amount)*100/'.$total_revenue.') as DECIMAL(5,2))'),
			'cbtu_transfer' => new Zend_Db_Expr('SUM(CASE 
				WHEN type = "IOM" THEN CAST(1.449*amount as DECIMAL(11,2))
				WHEN type = "IMO" THEN CAST(1.449*amount as DECIMAL(11,2))
				ELSE 0
				END)'),
			'cbtu_transfer_percentage' => new Zend_Db_Expr('
				CAST((SUM(CASE 
				WHEN type = "IOM" THEN CAST((1.449*amount*100)/'.$total_cbtu_transfer.' as DECIMAL(5,2))
				WHEN type = "IMO" THEN CAST((1.449*amount*100)/'.$total_cbtu_transfer.' as DECIMAL(5,2))
				ELSE 0
				END)) as DECIMAL(11,3))'),
			'liquid_composition' => new Zend_Db_Expr('(SUM(CASE
				WHEN type = "IOM" THEN CAST((value- 1.449)*amount as DECIMAL(11,2))
				WHEN type = "IMO" THEN CAST((value- 1.449)*amount as DECIMAL(11,2))
				ELSE CAST((value*amount) as DECIMAL(11,2))
				END))'),
			'liquid_composition_percentage' => new Zend_Db_Expr('(SUM(CASE
				WHEN type = "IOM" THEN CAST(((value- 1.449)*amount*100)/'.$total_liquid_revenue.' as DECIMAL(5,2))
				WHEN type = "IMO" THEN CAST(((value- 1.449)*amount*100)/'.$total_liquid_revenue.' as DECIMAL(5,2))
				ELSE CAST(((value*amount*100)/'.$total_liquid_revenue.') as DECIMAL(5,2))
				END))')))
		->joinInner(array('m' => 'mco'), 'm.id=mc.mco_id', array())
		->joinInner(array('q' => 'qco'), 'mc.line=q.number_communication')
		->joinInner(array('cc' => 'consortium_companies'), 'q.consortium_companies_id = cc.id')
		->where('date_operation >= ?', Application_Model_General::dateToUs($inicio))
		->where('date_operation <= ?', Application_Model_General::dateToUs($fim))
		->where('cc.id = ?', $cell)
		->group('type');
	// $sql = $select->__toString();
	// echo "$total_passenger\n";
	// echo "$sql\n";
	// exit;
		return $mcoCash->fetchAll($select);
	}

	public function reportCashCellValue($cell,$inicio, $fim, $total_passenger,$total_revenue,$total_cbtu_transfer,$total_liquid_revenue)
	{
		$mcoCash = new Application_Model_DbTable_Mco();
		$select = $mcoCash->select()->setIntegrityCheck(false);
		$select ->from(array("mc" => "mco_cash"), array('value'=> new Zend_Db_Expr('CAST(value as DECIMAL(4,2))'), 
			'amount' => new Zend_Db_Expr('SUM(amount)'),
			'amount_percent' => new Zend_Db_Expr('CAST((amount*100/'.$total_passenger.') as DECIMAL(5,2))'), 
			'full_composition' => new Zend_Db_Expr('CAST(SUM(value*amount) as DECIMAL(11,2))'),
			'full_composition_percentage' => new Zend_Db_Expr('CAST((SUM(value*amount)*100/'.$total_revenue.') as DECIMAL(5,2))'),
			'cbtu_transfer' => new Zend_Db_Expr('
				SUM(CASE 
				WHEN type = "IOM" THEN CAST(1.449*amount as DECIMAL(11,2))
				WHEN type = "IMO" THEN CAST(1.449*amount as DECIMAL(11,2))
				ELSE 0
				END)'),
			'cbtu_transfer_percentage' => new Zend_Db_Expr('
				SUM(CASE 
				WHEN type = "IOM" THEN CAST((1.449*amount*100)/'.$total_cbtu_transfer.' as DECIMAL(5,2))
				WHEN type = "IMO" THEN CAST((1.449*amount*100)/'.$total_cbtu_transfer.' as DECIMAL(5,2))
				ELSE 0
				END)'),
			'liquid_composition' => new Zend_Db_Expr('
				SUM(CASE
				WHEN type = "IOM" THEN CAST((value- 1.449)*amount as DECIMAL(11,2))
				WHEN type = "IMO" THEN CAST((value- 1.449)*amount as DECIMAL(11,2))
				ELSE CAST((value*amount) as DECIMAL(11,2))
				END)'),
			'liquid_composition_percentage' => new Zend_Db_Expr('
				SUM(CASE
				WHEN type = "IOM" THEN CAST(((value- 1.449)*amount*100)/'.$total_liquid_revenue.' as DECIMAL(5,2))
				WHEN type = "IMO" THEN CAST(((value- 1.449)*amount*100)/'.$total_liquid_revenue.' as DECIMAL(5,2))
				ELSE CAST(((value*amount*100)/'.$total_liquid_revenue.') as DECIMAL(5,2))
				END)')
			))
		->joinInner(array('m' => 'mco'), 'm.id=mc.mco_id')
		->joinInner(array('q' => 'qco'), 'mc.line=q.number_communication')
		->joinInner(array('cc' => 'consortium_companies'), 'q.consortium_companies_id = cc.id')
		->where('date_operation >= ?', Application_Model_General::dateToUs($inicio))
		->where('date_operation <= ?', Application_Model_General::dateToUs($fim))
		->where('cc.id = ?', $cell)
		->group('value');
		return $mcoCash->fetchAll($select);
	}

	/**
	*	Return the data from mco_data to show in the view. The data are about all overcrowded travels considering the vhicles capacity
	*	 and the amount of passengers in the travel. 
	*	
	* @param date $start_date - start date of research report
	* @param date $end_date - end date of research report
	* @param integer $capacity - capacity of passengers that the bus can carry
	* @access public
	* @return array
	*/
	public function reportOvercrowded($start_date,$end_date){

		$mcoData = new Application_Model_DbTable_McoData();
		$select = $mcoData->select()->setIntegrityCheck(false);
		$select ->from(array("md" => "mco_data"),array('md.line','md.vehicle_number','md.start_hour','vp.name','md.end_hour',
			'gr.gratuity_factor','gr.turnover_factor','gr.research_date',
			'date_exibition' => 'md.start_date',
			'qtd_passenger' => new Zend_Db_Expr('(md.amount_passenger*gr.gratuity_factor)/gr.turnover_factor'),
			'qtd_passenger_way' => new Zend_Db_Expr('CASE WHEN mid_roulette != 0 THEN (((mid_roulette-start_roulette)*gr.gratuity_factor)/gr.turnover_factor) END'),
			'qtd_passenger_back' => new Zend_Db_Expr('CASE WHEN mid_roulette != 0 THEN (((end_roulette-mid_roulette)*gr.gratuity_factor)/gr.turnover_factor) END'),
			'capacity' => new Zend_Db_Expr('(((((vm.width_before_roulette*vm.length_before_roulette)+(vm.width_after_roulette*vm.length_after_roulette))/100)/5)+vm.amount_seats)'),
			'exceed_fp' => new Zend_Db_Expr('IF(((md.amount_passenger*gr.gratuity_factor)/gr.turnover_factor) < (((((vm.width_before_roulette*vm.length_before_roulette)+
				(vm.width_after_roulette*vm.length_after_roulette))/100)/5)+vm.amount_seats),0,1)'),
			'exceed_p' => new Zend_Db_Expr('IF(((md.amount_passenger*gr.gratuity_factor)/gr.turnover_factor) < (((((vm.width_before_roulette*vm.length_before_roulette)+
				(vm.width_after_roulette*vm.length_after_roulette))/100)/6.5)+vm.amount_seats),0,1)')))
		->joinInner(array('vh' => 'vehicle_historic'), 'md.vehicle_number=vh.external_number', array())
		->joinInner(array('vm' => 'vehicle_measures'), 'vm.id=vh.vehicle_id')
		->joinInner(array('v' => 'vehicle'), 'v.id=vh.vehicle_id')
		->joinInner(array('vp' => 'vehicle_pattern'), 'v.pattern=vp.id')
		->joinInner(array('gr' => 'gratuity_turnover'), 'md.line=gr.line')
		->where('md.start_date >= ?', Application_Model_General::dateToUs($start_date))
		->where('md.end_date <= ?', Application_Model_General::dateToUs($end_date))
		->where('substr(md.start_date,1,4) = substr(gr.research_date,1,4)')
		->where('md.status=1');
		return $mcoData->fetchAll($select);
	}


	/**
	*	Return the travel data to show in the view. 
	*	
	* @param date $start_date - start date of research report
	* @param date $end_date - end date of research report
	* @access public
	* @return array
	*/
	public function reportTravel($start_date,$end_date){

		$mcoData = new Application_Model_DbTable_McoData();
		$select = $mcoData->select()->setIntegrityCheck(false);
		$select ->from(array("md" => "mco_data"),array('md.line','md.vehicle_number','md.start_hour','vp.name','md.end_hour',
			'v.plate','md.mid_hour','md.start_roulette',
			'md.mid_roulette','md.end_roulette','md.amount_passenger','md.incident','md.traffic_jam',
			'travel_type' => 'md.type',
			'date_exibition' => 'md.start_date',
			'qtd_passenger_way' => new Zend_Db_Expr('CASE WHEN mid_roulette != 0 THEN mid_roulette-start_roulette END'),
			'qtd_passenger_back' => new Zend_Db_Expr('CASE WHEN mid_roulette != 0 THEN end_roulette-mid_roulette END'),
			'capacity' => new Zend_Db_Expr('(((((vm.width_before_roulette*vm.length_before_roulette)+(vm.width_after_roulette*vm.length_after_roulette))/100)/5)+vm.amount_seats)')))
		->joinInner(array('vh' => 'vehicle_historic'), 'md.vehicle_number=vh.external_number', array())
		->joinInner(array('vm' => 'vehicle_measures'), 'vm.id=vh.vehicle_id')
		->joinInner(array('v' => 'vehicle'), 'v.id=vh.vehicle_id')
		->joinInner(array('vp' => 'vehicle_pattern'), 'v.pattern=vp.id')
		->where('md.start_date >= ?', Application_Model_General::dateToUs($start_date))
		->where('md.end_date <= ?', Application_Model_General::dateToUs($end_date))
		->where('md.status=1');
		return $mcoData->fetchAll($select);
	}


	/**
	*	Return the production by hour by qco day, of all day types together. 
	*	
	* @param date $start_date - start date of research report
	* @param date $end_date - end date of research report
	* @access public
	* @return array
	*/
	public function reportHourProductionTotal($start_date,$end_date,$hour1,$hour2){
		$mcoData = new Application_Model_DbTable_McoData();
		$select = $mcoData->select()->setIntegrityCheck(false);
		$select ->from(array("md" => "mco_data"),array('md.start_hour','md.end_hour',
		'kilometric_production_a' => new Zend_Db_Expr('(sum(qr.ext_asphalt)/1000)'),
		'kilometric_production_t' => new Zend_Db_Expr('(sum(qr.ext_land)/1000)'),
		'kilometric_production_p' => new Zend_Db_Expr('(sum(qr.ext_poli)/1000)'),
		'ipk' => new Zend_Db_Expr('sum(md.amount_passenger)/sum(qr.ext_asphalt)'),
		'travel' => new Zend_Db_Expr('count(md.id)'),
		'travel_passenger' => new Zend_Db_Expr('sum(md.amount_passenger)/count(md.id)'),
		'total_passenger' => new Zend_Db_Expr('sum(md.amount_passenger)'),
		'km_hour' => new Zend_Db_Expr('((sum(qr.ext_asphalt)+sum(qr.ext_land)+sum(qr.ext_poli))/1000)/((sum(((substr(md.end_hour,1,2)*60)+substr(md.end_hour,4,2)) - ((substr(md.start_hour,1,2)*60)+substr(md.start_hour,4,2))))/60)'),
		'travel_time' => new Zend_Db_Expr('(sum(((substr(md.end_hour,1,2)*60)+substr(md.end_hour,4,2)) - ((substr(md.start_hour,1,2)*60)+substr(md.start_hour,4,2))))/count(md.id)'),
		'realized_travel' => new Zend_Db_Expr('sum(travel_interrupted)')))
		->joinInner(array('q' => 'qco'), 'md.line=q.number_communication')
		->joinInner(array('qr' => 'qco_route'), 'q.id=qr.qco_id')
		->where('md.start_date >= ?', Application_Model_General::dateToUs($start_date))
		->where('md.end_date <= ?', Application_Model_General::dateToUs($end_date))
		->where('md.start_hour >= ?', $hour1)
		->where('md.start_hour <= ?', $hour2)
		->where('qr.type_journey = 1')
		->where('md.status=1');
		return $mcoData->fetchAll($select);
	}

}

