<?php

class Application_Model_Qco
{

	/**
	*	Array of hour to increment on QCO's specification.
	*/
	public $hour = array( 
			'00', 
			'01',
			'02',
			'03', 
			'04',
			'05',
			'06',
			'07',
			'08',
			'09', 
			'10',
			'11',
			'12', 
			'13',
			'14',
			'15', 
			'16',
			'17',
			'18', 
			'19',
			'20',
			'21', 
			'22',
			'23',

	);

	/**
	*	Register a new QCO.
	*
	*	@param array $data -> Main's QCO data
	*	@access public
	*	@return integer
	*/
	public function newQCO($data)
	{
		$qco = new Application_Model_DbTable_Qco();
		$qcoNew = $qco->createRow();
		$qcoNew->date = new Zend_Db_Expr('NOW()');
		$qcoNew->name = $data['name'];
		$qcoNew->historic = $data['historic'];
		$qcoNew->start_validity_date = Application_Model_General::dateToUs($data['start_validity_date']);
		$qcoNew->number_communication = $data['number_communication'];
		$qcoNew->finance_fare_id = $data['finance_fare_id'];
		$qcoNew->integration_finance_fare_id = $data['integration_finance_fare_id'];
		return $qcoNew->save();
	}

	/**
	*	Fetch all QCO's actives (with end_date null).
	*
	*	@access public
	*	@return Zend_Db_Table_Rowset
	*/
	public function lists()
	{
		$qco = new Application_Model_DbTable_Qco();
		return $qco->fetchAll($qco->select()->where('end_date IS NULL')->order('number_communication'));
	}

	/**
	*	Fetch a QCO with a certain number of communication.
	*
	*	@param integer $field -> Line's number communication
	*	@access public
	*	@return Zend_Db_Table_Rowset
	*/
	public function listByName($field)
	{
		$qco = new Application_Model_DbTable_Qco();
		return $qco->fetchAll($qco->select()->where('number_communication = ?',$field)->order('number_communication'));
	}

	/**
	*	
	*/
	public function editQco($data,$qcoId)
	{
		if(isset($data['finance_fare_id']) && $data['finance_fare_id'] != '')
		{
			$this->saveMain($data,$qcoId);
			return true;
		}
		if(isset($data['ini_date_paralysed']) && isset($data['end_date_paralysed']) && 
		(($data['end_date_paralysed'] != '') && ($data['ini_date_paralysed'] != '')))
		{
			$this->saveParalysed($data,$qcoId);
			return true;
		}
		if(isset($data['name_route']) && $data['name_route'] != '')
		{
			$this->saveRoute($data,$qcoId);
			return true;
		}
		if(isset($data['description']) && $data['description'] != '')
		{
			$this->saveLog($data,$qcoId);
			return true;
		}
		return false;
	}

	/**
	*	Save main data on QCO.
	*
	*	@param array $data -> main QCO's data
	*	@param integer $qcoId -> qco id
	*	@access protected
	*	@return integer
	*/
	protected function saveMain($data,$qcoId)
	{
		$qco = new Application_Model_DbTable_Qco();
		$qcoRow = $qco->fetchRow($qco->select()->where('id = ?',$qcoId));
		$qcoRow->name = $data['name'];
		$qcoRow->number_communication = $data['number_communication'];
		$qcoRow->finance_fare_id = $data['finance_fare_id'];
		if(!$data['integration_finance_fare_id'])
		{
			$qcoRow->integration_finance_fare_id = new Zend_Db_Expr('NULL');
		}
		else
		{
			$qcoRow->integration_finance_fare_id = $data['integration_finance_fare_id'];
		}
		$qcoRow->consortium_companies_id = $data['consortium_company'];
		return $qcoRow->save();
	}

	/**
	*	
	*/
	protected function saveRoute($data,$qcoId)
	{
		$qcoRoute = new Application_Model_DbTable_QcoRoute();
		if($data['id'] == '')
		{
			$qcoNew = $qcoRoute->createRow();
			$qcoNew->qco_id = $qcoId;
			$qcoNew->name_route = $data['name_route'];
			$qcoNew->pc = $data['pc'];
			$qcoNew->pc_location = $data['pc_location'];
			$qcoNew->type_journey = $data['type_journey'];
			$qcoNew->ext_asphalt = $data['ext_asphalt'];
			$qcoNew->ext_poli = $data['ext_poli'];
			$qcoNew->ext_land = $data['ext_land'];
			$qcoNew->route = $data['route'];
			$qcoNew->ped = $data['ped'];
			return $qcoNew->save();
		}
		else
		{
			$qcoRow = $qcoRoute->fetchRow($qcoRoute->select()->where('id = ?',$data['id']));
			$qcoRow->name_route = $data['name_route'];
			$qcoRow->pc = $data['pc'];
			$qcoRow->pc_location = $data['pc_location'];
			$qcoRow->type_journey = $data['type_journey'];
			$qcoRow->ext_asphalt = $data['ext_asphalt'];
			$qcoRow->ext_poli = $data['ext_poli'];
			$qcoRow->ext_land = $data['ext_land'];
			$qcoRow->route = $data['route'];
			$qcoRow->ped = $data['ped'];
			return $qcoRow->save();
		}
	}

	public function returnIntegrationFare()
	{
		$qco = new Application_Model_DbTable_Qco();
		$select = $qco->select()->setIntegrityCheck(false);
		$select	->from(array('q' => 'qco'),array('integration_finance_fare_id'));
		return $qco->fetchRow($select);	
	}

	public function returnMainById($qcoId)
	{
		$qco = new Application_Model_DbTable_Qco();
		$select = $qco->select()->setIntegrityCheck(false);
		$select	->from(array('q' => 'qco'),array('name', 'number_communication', 'finance_fare_id', 'integration_finance_fare_id', 'historic','consortium_companies_id'))
				->joinInner(array('cc' => 'consortium_companies'),'cc.id=q.consortium_companies_id', array('consortium_companies' => 'name', 'cell_id' => 'id'))		
				->joinInner(array('c' => 'consortium'),'cc.consortium_id=c.id', array('consortium' => 'name', 'consortium_id' => 'id'))
				->where('q.id = ?',$qcoId);
		return $qco->fetchRow($select);
	}

	public function returnRouteById($qcoId)
	{
		$qco = new Application_Model_DbTable_QcoRoute();
		$select = $qco->select()->setIntegrityCheck(false);
		$select	->from(array('q' => 'qco_route'))
						->joinInner(array('qtj' => 'qco_type_journey'),'qtj.id=q.type_journey',array('type_journey_name' => 'name'))
						->where('q.qco_id = ?',$qcoId)
						->order('q.type_journey');
		return $qco->fetchAll($select);
	}

	public function returnRoute($qcoId,$id_type_journey)
	{
		$qco = new Application_Model_DbTable_QcoRoute();
		$select = $qco->select()->setIntegrityCheck(false);
		$select	->from(array('q' => 'qco_route'))
						->where('q.qco_id = ?',$qcoId)
						->where('q.type_journey = ?',$id_type_journey);
		return $qco->fetchAll($select);
	}

	public function returnHourById($qcoId)
	{
		$qco = new Application_Model_DbTable_QcoHour();
		$select = $qco->select()->setIntegrityCheck(false);
		$select	->from(array('q' => 'qco_hour'))
 						->where('q.qco_id = ?',$qcoId)
						->joinInner(array('qtd' => 'qco_type_day'),'qtd.id=q.id_type_day',array('type_day_name' => 'name'))
 						->group('q.name');
		return $qco->fetchAll($select);
	}

	public function returnMinutes($qcoId, $typeDay, $hour)
	{
		$qco = new Application_Model_DbTable_QcoHour();
		$select = $qco->select()->setIntegrityCheck(false);
		$select	->from(array('q' => 'qco_hour'), array('minutes' => 'RIGHT(hour,2)'))
 						->where('q.qco_id = ?',$qcoId)
 						->where('q.id_type_day = ?',$typeDay)
 						->where('LEFT(hour,2) = ?',$hour);
		return $qco->fetchAll($select);
	}

	public function returnCountJourneyById($qcoId, $typeDay)
	{
		$qco = new Application_Model_DbTable_QcoHour();
		$select = $qco->select()->setIntegrityCheck(false);
		$select	->from(array('q' => 'qco_hour'), array('count' => 'COUNT(*)'))
 						->where('q.qco_id = ?',$qcoId)
 						->where('q.id_type_day = ?',$typeDay)
 						->group('id_type_day');
		return $qco->fetchRow($select);
	}

	public function returnFleetById($qcoId)
	{
		$qco = new Application_Model_DbTable_QcoFleet();
		$select = $qco->select()->setIntegrityCheck(false);
		$select	->from(array('q' => 'qco_fleet'))
						->joinInner(array('qtd' => 'qco_type_day'),'qtd.id=q.type_day_id',array('type_day_name' => 'name'))
 						->where('q.qco_id = ?',$qcoId)
 						->group('q.type_day_id')
 						->order('q.type_day_id');
		return $qco->fetchAll($select);
	}

	public function returnByNumberCommunication($numberCommunication)
	{
		$qco = new Application_Model_DbTable_Qco();
		$select = $qco->select()->setIntegrityCheck(false);
		$select	->from(array('q' => 'qco'),array('id', 'name', 'number_communication', 'finance_fare_id', 'historic') )
						->where('q.number_communication = ?',$numberCommunication);
		return $qco->fetchRow($select);
	}

	public function saveShape($file,$data)
	{
		$target_directory = APPLICATION_PATH.'/shape/'.$data['qco_id'];
		if(!is_dir($target_directory))
		{
			mkdir($target_directory,0700);
		}
		else
		{
			$this->deleteDirectory($target_directory);
			mkdir($target_directory,0700);
		}
		$newFile = $target_directory.'/'.$data['qco_id'].'.zip';
		move_uploaded_file($file['qco_file']['tmp_name'], $newFile);
		// $this->descompressFile($target_directory, $data, $newFile);
		return 5;
	}

	protected function descompressFile($target_directory, $data, $newFile)
	{
		$filter = new Zend_Filter_Decompress(array(
			'adapter' => 'Zip',
			'options' => array(
				'target' => $target_directory.'/'
			)
		));
		$filter->filter($newFile);
	}

	public function deleteDirectory($dir) 
	{
    if (!file_exists($dir)) return true;
    if (!is_dir($dir)) return unlink($dir);
    foreach (scandir($dir) as $item) 
    {
        if ($item == '.' || $item == '..') continue;
        if (!$this->deleteDirectory($dir.DIRECTORY_SEPARATOR.$item)) return false;
    }
    return rmdir($dir);
	}

	protected function saveParalysed($data,$qcoId)
	{
		$qco = new Application_Model_DbTable_Qco();
		$qcoRow = $qco->fetchRow($qco->select()->where('id = ?',$qcoId));
		$qcoRow->ini_date_paralysed = Application_Model_General::dateToUs($data['ini_date_paralysed']);
		$qcoRow->end_date_paralysed = Application_Model_General::dateToUs($data['end_date_paralysed']);
		$qcoRow->paralysed_days = $data['diff_period'];
		// $this->saveParalysedHistoric($data,$qcoId);
		return $qcoRow->save();
	}

	protected function saveParalysedHistoric($data,$qcoId)
	{
		$qcoHistoric = new Application_Model_DbTable_QcoHistoric();
		$qcoRowHistoric = $qcoHistoric->fetchRow($qcoHistoric->select()->where('qco_id = ?',$qcoId));
		$qcoRowHistoric->ini_date_paralysed = Application_Model_General::dateToUs($data['ini_date_paralysed']);
		$qcoRowHistoric->end_date_paralysed = Application_Model_General::dateToUs($data['end_date_paralysed']);
		return $qcoRowHistoric->save();
	}

	protected function saveLog($data,$qcoId)
	{
		$qcoLog = new Application_Model_DbTable_QcoLog();
		if($data['id'] == '')
		{
			$authNamespace = new Zend_Session_Namespace('userInformation');
			$qcoNew = $qcoLog->createRow();
			$qcoNew->qco_id = $qcoId;
			$qcoNew->user_id = $authNamespace->user_id;
			$qcoNew->date = new Zend_Db_Expr('NOW()');
			$qcoNew->subject = $data['subject'];
			$qcoNew->description = $data['description'];
			return $qcoNew->save();
		}
		else
		{
			// Does not allow to edit log
		}
	}

	protected function saveHourMinute($hour,$data,$qcoHourId)
	{
		foreach($data as $minutes)
		{
			if($minutes != '')
			{
				$qcoHour = new Application_Model_DbTable_QcoHourMinute();
				$qcoHourNew = $qcoHour->createRow();
				$qcoHourNew->qco_hour_id = $qcoHourId;
				$qcoHourNew->hour = $hour;
				$qcoHourNew->minute = $minutes;
				$qcoHourNew->save();
				unset($qcoHour);
			}
		}
	}

	/**
	*	Remove a route from a QCO data.
	*
	*	@param integer $routeId -> route's id
	*	@param integer $qcoId -> qco's id
	*	@access public
	*	@return integer
	*/
	public function removeRoute($routeId,$qcoId)
	{
		$qcoRoute = new Application_Model_DbTable_QcoRoute();
		$qcoRouteRow = $qcoRoute->fetchRow($qcoRoute->select()->where('qco_id = ?',$qcoId)->where('id = ?',$routeId));
		return $qcoRouteRow->delete();
	}

	protected function removeHourQco($qcoHourId)
	{	
		$qcoHour = new Application_Model_DbTable_QcoHourMinute();
		$qcoHourAux = $qcoHour->fetchAll($qcoHour->select()->where('qco_hour_id = ?',$qcoHourId));
		foreach($qcoHourAux as $aux)
		{
			$aux->delete();
		}
	}

	public function removeQH($id)
	{
		$qcoHour = new Application_Model_DbTable_QcoHour();
		$qcoHourRow = $qcoHour->fetchRow($qcoHour->select()->where('id = ?',$id));
		if(count($qcoHourRow))
		{
			return $qcoHourRow->delete();
		}
		return false;
	}

	public function removeLog($id)
	{
		$qco = new Application_Model_DbTable_QcoLog();
		$qcoLogRow = $qco->fetchRow($qco->select()->where('id = ?',$id));
		if(count($qcoLogRow))
		{
			return $qcoLogRow->delete();
		}
		return false;
	}

	public function saveQH($files,$data)
	{
		try
		{
			$qcoQH = new Application_Model_QcoQH();
			$checked = $qcoQH->checkFile($files,$data['qco_id']);
			if($checked)
			{
				$this->saveQcoHistoric($data['qco_id']);
				$save = $qcoQH->saveHoursFleet($data['qco_id']);		
			}
			return true;
		}catch(Zend_Exception $e) {
			return false;
		}
	}

	/**
	*	Register a new date in general calendar.
	*	
	* @param array $data - general calendar's data
	* @access public
	* @return integer
	*/
	public function newGeneralCalendar($data)
	{
		$calendar = new Application_Model_DbTable_GeneralCalendar();
		$calendarNew = $calendar->createRow();
		$calendarNew->start_date = Application_Model_General::dateToUs($data['start_date']);
		$calendarNew->end_date = Application_Model_General::dateToUs($data['end_date']);
		$calendarNew->type_day = $data['type_day'];
		return $calendarNew->save();
	}

	/**
	*	List all registered dates in general calendar.
	*	
	* @access public
	* @return array
	*/
	public function listGeneralCalendar(){
		$calendar = new Application_Model_DbTable_GeneralCalendar();

		$select = $calendar->select()->setIntegrityCheck(false);
		$select	->from(array('gc' => 'calendar_general'),array('id','type_day',
			'start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
			'end_date' => new Zend_Db_Expr ('DATE_FORMAT(end_date,"%d/%m/%Y")')))
			->order('YEAR(start_date) desc')
 			->order('MONTH(start_date) desc')
 			->order('DAY(start_date) desc');
		return $calendar->fetchAll($select);
	}

	/**
	*	Delete a date in general calendar.
	*	
	* @param int $id - general calendar's id
	* @access public
	* @return integer
	*/
	public function deleteGeneralCalendar($id)
	{
		$calendar = new Application_Model_DbTable_GeneralCalendar();
		$calendarRow = $calendar->fetchRow($calendar->select()->where('id = ?',$id));
		if(count($calendarRow))
		{
			return $calendarRow->delete();
		}
		return false;
	}

	/**
	*	Register a new date in line calendar.
	*	
	* @param array $data - line calendar's data
	* @access public
	* @return integer
	*/
	public function newLineCalendar($data)
	{
		$calendar = new Application_Model_DbTable_LineCalendar();
        if(count($data) > 4){
        	for($i=0;(count($data)-3) > $i;$i++){
        		$calendarNew = $calendar->createRow();
				$calendarNew->start_date = Application_Model_General::dateToUs($data['start_date']);
				$calendarNew->end_date = Application_Model_General::dateToUs($data['end_date']);
				$calendarNew->type_day = $data['type_day'];
				$calendarNew->line = $data['line_'.$i];
				$save = $calendarNew->save();
        	}
        	if($save){
        		return true;
        	}
        	else
        		return false;
        }
        else{
			$calendarNew = $calendar->createRow();
			$calendarNew->start_date = Application_Model_General::dateToUs($data['start_date']);
			$calendarNew->end_date = Application_Model_General::dateToUs($data['end_date']);
			$calendarNew->type_day = $data['type_day'];
			$calendarNew->line = $data['line_0'];
			return $calendarNew->save();
		}
	}
	/**
	*	List all registered dates in line calendar.
	*	
	* @access public
	* @return array
	*/
	public function listLineCalendar(){
		$calendar = new Application_Model_DbTable_LineCalendar();

		$select = $calendar->select()->setIntegrityCheck(false);
		$select	->from(array('lc' => 'calendar_line'),array('id','type_day','line',
			'start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
			'end_date' => new Zend_Db_Expr ('DATE_FORMAT(end_date,"%d/%m/%Y")')))
 			->order('MONTH(start_date) desc')
 			->order('DAY(start_date) desc');
		return $calendar->fetchAll($select);
	}

	/**
	*	Delete a date in Line calendar.
	*	
	* @param int $id - line calendar's id
	* @access public
	* @return integer
	*/
	public function deleteLineCalendar($id)
	{
		$calendar = new Application_Model_DbTable_LineCalendar();
		$calendarRow = $calendar->fetchRow($calendar->select()->where('id = ?',$id));
		if(count($calendarRow))
		{
			return $calendarRow->delete();
		}
		return false;
	}

	/**
	*	Register a new date in consortium calendar.
	*	
	* @param array $data - consortium calendar's data
	* @access public
	* @return integer
	*/
	public function newConsortiumCalendar($data)
	{
		$calendar = new Application_Model_DbTable_ConsortiumCalendar();
        if(count($data) > 4){
        	for($i=0;(count($data)-3) > $i;$i++){
        		$calendarNew = $calendar->createRow();
				$calendarNew->start_date = Application_Model_General::dateToUs($data['start_date']);
				$calendarNew->end_date = Application_Model_General::dateToUs($data['end_date']);
				$calendarNew->type_day = $data['type_day'];
				$calendarNew->consortium = $data['consortium_'.$i];
				$save = $calendarNew->save();
        	}
        	if($save){
        		return true;
        	}
        	else
        		return false;
        }
        else{
			$calendarNew = $calendar->createRow();
			$calendarNew->start_date = Application_Model_General::dateToUs($data['start_date']);
			$calendarNew->end_date = Application_Model_General::dateToUs($data['end_date']);
			$calendarNew->type_day = $data['type_day'];
			$calendarNew->consortium = $data['consortium_0'];
			return $calendarNew->save();
		}
	}

	/**
	*	List all registered dates in consortium calendar.
	*	
	* @access public
	* @return array
	*/
	public function listConsortiumCalendar(){
		$calendar = new Application_Model_DbTable_ConsortiumCalendar();

		$select = $calendar->select()->setIntegrityCheck(false);
		$select	->from(array('cc' => 'calendar_consortium'),array('id','type_day','consortium',
			'start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
			'end_date' => new Zend_Db_Expr ('DATE_FORMAT(end_date,"%d/%m/%Y")')))
 			->order('MONTH(start_date) desc')
 			->order('DAY(start_date) desc');
		return $calendar->fetchAll($select);
	}

	/**
	*	Delete a date in consortium calendar.
	*	
	* @param int $id - consortium calendar's id
	* @access public
	* @return integer
	*/
	public function deleteConsortiumCalendar($id)
	{
		$calendar = new Application_Model_DbTable_ConsortiumCalendar();
		$calendarRow = $calendar->fetchRow($calendar->select()->where('id = ?',$id));
		if(count($calendarRow))
		{
			return $calendarRow->delete();
		}
		return false;
	}

	/**
	*	Register a new date in cell calendar.
	*	
	* @param array $data - cell calendar's data
	* @access public
	* @return integer
	*/
	public function newCellCalendar($data)
	{
		//print_r($data);exit;
		$calendar = new Application_Model_DbTable_CellCalendar();
		$calendarNew = $calendar->createRow();
		$calendarNew->start_date = Application_Model_General::dateToUs($data['start_date']);
		$calendarNew->end_date = Application_Model_General::dateToUs($data['end_date']);
		$calendarNew->type_day = $data['type_day'];
		$calendarNew->consortium = $data['consotiumOption'];
		$calendarNew->consortium_companies_id = $data['cellOption'];
		return $calendarNew->save();
	}

	/**
	*	List all registered dates in cell calendar.
	*	
	* @access public
	* @return array
	*/
	public function listCellCalendar(){
		$calendar = new Application_Model_DbTable_CellCalendar();

		$select = $calendar->select()->setIntegrityCheck(false);
		$select	->from(array('cc' => 'calendar_cell'),array('id','type_day','consortium_companies_id','consortium',
			'start_date' => new Zend_Db_Expr ('DATE_FORMAT(start_date,"%d/%m/%Y")'),
			'end_date' => new Zend_Db_Expr ('DATE_FORMAT(end_date,"%d/%m/%Y")')))
 			->order('MONTH(start_date) desc')
 			->order('DAY(start_date) desc');
		return $calendar->fetchAll($select);
	}

	/**
	*	Delete a date in cell calendar.
	*	
	* @param int $id - cell calendar's id
	* @access public
	* @return integer
	*/
	public function deleteCellCalendar($id)
	{
		$calendar = new Application_Model_DbTable_CellCalendar();
		$calendarRow = $calendar->fetchRow($calendar->select()->where('id = ?',$id));
		if(count($calendarRow))
		{
			return $calendarRow->delete();
		}
		return false;
	}

	/**
	* Export qco data of certain date.
	*
	* @access public
	* @return array 
	*/
	public function exportQcoData()
	{
		$qco = new Application_Model_DbTable_Qco();
		$select = $qco->select()->setIntegrityCheck(false);
		$select	->from(array('q' => 'qco'), array('number_communication'))
						->joinInner(array('qh' => 'qco_hour'),'qh.qco_id=q.id',array('id_type_day','name','hour','pc','id_type_journey'))
						->order(array('number_communication','id_type_day','hour'));
		return $qco->fetchAll($select);
	}

}

