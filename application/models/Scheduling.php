<?php

class Application_Model_Scheduling
{

	public function newSchedule($data)
	{
		try 
		{
			$scheduling = new Application_Model_DbTable_Scheduling();
			$data['str_hour'] = $this->returnSpecificHour($data['hour']);
			$schedulingNew = $scheduling->createRow($data);
			return $schedulingNew->save();
		}catch(Zend_Exception $e) {
			echo $e->getMessage();
			return false;
		}
	}

	public function saveLog($scheduling_new_id,$scheduling_old_id,$action_type)
	{
		try { 
			$data['registration_date'] = date('Y-m-d H:i:s');
			$data['scheduling_new_id'] = $scheduling_new_id;
			$data['scheduling_old_id'] = $scheduling_old_id;
			$data['action_type'] = $action_type;
			$schedulingLog = new Application_Model_DbTable_SchedulingLog();
			$schedulingLogNew = $schedulingLog->createRow($data);
			$schedulingLogNew->save();
		}catch(Zend_Exception $e) {

		}
	}

	protected function amountHour()
	{
		$schedulingHourAllowed = new Application_Model_DbTable_SchedulingHourAllowed();
		$schedulingHourAllowedRow = $schedulingHourAllowed->fetchAll();
		return count($schedulingHourAllowedRow);
	}

	public function returnAllEvents()
	{
		$amountHour = $this->amountHour();
		$scheduling = new Application_Model_DbTable_Scheduling();
		$select = $scheduling->select()->setIntegrityCheck(false);
		$select	->from(array('scheduling'), array('events' => 'COUNT(*)', 'date') )
						->where('status = 1')
						->where('date > ?', new Zend_Db_Expr('NOW()'))
						->group('date')
						->having('events >= '.$amountHour);
		$schedulingRow = $scheduling->fetchAll($select);
		return $schedulingRow->toArray();
	}

	public function returnEvents($date)
	{
		$scheduling = new Application_Model_DbTable_Scheduling();
		$select = $scheduling->select()->setIntegrityCheck(false);
		$select	->from(array('s' => 'scheduling'), array('hour') )
						->where('s.date = ?',$date)
						->where('s.status = 1');
		$schedulingUserRow = $scheduling->fetchAll($select);
		return $schedulingUserRow->toArray();
	}

	public function newHour($data)
	{
		$data['hour'] = $data['hour'].':00';
		$schedulingHour = new Application_Model_DbTable_SchedulingHourAllowed();
		$schedulingHourNew = $schedulingHour->createRow($data);
		$newHour = $schedulingHourNew->save();
		$this->addHourHolidays($newHour,$data['hour']);
	}

	public function returnHour()
	{
		$schedulingHour = new Application_Model_DbTable_SchedulingHourAllowed();
		$select = $schedulingHour->select()->setIntegrityCheck(false);
		$select	->from(array('scheduling_hour_allowed'), array('id','hour' => 'TIME_FORMAT(hour,"%H:%i")') )
						->order('hour');
		return $schedulingHour->fetchAll($select);
	}

	public function deleteHour($id)
	{
		$schedulingHour = new Application_Model_DbTable_SchedulingHourAllowed();
		$schedulingHourRow = $schedulingHour->fetchRow($schedulingHour->select()->where('id = ?',$id) );
		$this->removeHourHolidays($id);
		$deletedHour = $schedulingHourRow->delete();
		return $deletedHour;
	}

	protected function addHourHolidays($newHour,$strHour)
	{
		$schedulingHoliday = new Application_Model_DbTable_SchedulingHoliday();
		$schedulingHolidayAll = $schedulingHoliday->fetchAll($schedulingHoliday->select()->where('date >= ?',new Zend_Db_Expr('NOW()')) );
		foreach($schedulingHolidayAll as $schedulingHoliday)
		{
			$scheduling = new Application_Model_DbTable_Scheduling();
			$schedulingNew = $scheduling->createRow();
			$schedulingNew->name = 'Recesso';
			$schedulingNew->date = $schedulingHoliday->date;
			$schedulingNew->hour = $newHour;
			$schedulingNew->str_hour = $strHour;
			$schedulingNew->save();
			unset($schedulingNew);
		}
	}

	protected function removeHourHolidays($hour)
	{
		$schedulingHoliday = new Application_Model_DbTable_SchedulingHoliday();
		$schedulingHolidayAll = $schedulingHoliday->fetchAll($schedulingHoliday->select()->where('date >= ?',new Zend_Db_Expr('NOW()')) );
		foreach($schedulingHolidayAll as $schedulingHoliday)
		{
			$scheduling = new Application_Model_DbTable_Scheduling();
			$schedulingRow = $scheduling->fetchRow($scheduling->select()->where('date = ?',$schedulingHoliday->date)->where('hour = ?',$hour));
			$schedulingRow->delete();
			unset($schedulingRow);
		}
	}

	public function returnSpecificHour($id)
	{
		$schedulingHour = new Application_Model_DbTable_SchedulingHourAllowed();
		$schedulingHourRow = $schedulingHour->fetchRow($schedulingHour->select()->where('id = ?',$id) );
		return substr($schedulingHourRow->hour, -8, 5);
	}

	public function getInfo($id)
	{
		$scheduling = new Application_Model_DbTable_Scheduling();
		$select = $scheduling->select()->setIntegrityCheck(false);
		$select	->from(array('p' => 'scheduling'), array('id', 'name', 'email', 'date') )
						->joinInner(array('h' => 'scheduling_hour_allowed'),'p.hour=h.id',array('hour' => 'TIME_FORMAT(h.hour,"%H:%i")') )
						->where('p.id = ?',$id);
		return $scheduling->fetchRow($select);
	}

	public function saveError()
	{
		try { 
			$schedulingError = new Application_Model_DbTable_SchedulingError();
			$schedulingErrorNew = $schedulingError->createRow();
			$schedulingErrorNew->error = 'Error Captcha';
			$schedulingErrorNew->date = new Zend_Db_Expr('NOW()');
			$schedulingErrorNew->save();
		}catch(Zend_Exception $e) {

		}
	}

	public function returnEventsCalendar()
	{
		$scheduling = new Application_Model_DbTable_Scheduling();
		$select = $scheduling->select()->setIntegrityCheck(false);
		$select	->from(array('p' => 'scheduling'), array('title' => 'p.name', 'start' => 'CONCAT(date,"T",p.str_hour,":00Z")') )
						->where('date > DATE_SUB(NOW(),INTERVAL 1 MONTH)')
						->where('p.status = 1');
		$schedulingAll = $scheduling->fetchAll($select);

		return $schedulingAll->toArray();
	}

	/**
	*	Register a new absence in the scheduling system.
	*
	*	@param array $data - absence's data
	*	@access public
	*	@return boolean
	*/
	public function newAbsence($data)
	{
		try{
			$data['date'] =  Application_Model_General::dateToUs($data['date']);
			$schedulingHoliday = new Application_Model_DbTable_SchedulingHoliday();
			$schedulingHolidayRow = $schedulingHoliday->fetchRow($schedulingHoliday->select()->where('date = ?',$data['date']));
			if(!count($schedulingHolidayRow))
			{
				$schedulingHolidayNew = $schedulingHoliday->createRow($data);
				$schedulingHolidayNew->save();

				if($data['hour_start'] != '' && $data['hour_end'] != ''){
					$schedulingHour = new Application_Model_DbTable_SchedulingHourAllowed();
					$schedulingHourRow = $schedulingHour->fetchAll($schedulingHour->select()
														->where('hour >= ?',$data['hour_start'])
														->where('hour <= ?',$data['hour_end']));
				}
				else{
					$schedulingHour = new Application_Model_DbTable_SchedulingHourAllowed();
					$schedulingHourRow = $schedulingHour->fetchAll();
				}

				foreach($schedulingHourRow as $hour)
				{
					try{
					$scheduling = new Application_Model_DbTable_Scheduling();
					$schedulingNew = $scheduling->createRow();
					$schedulingNew->name = 'Recesso';
					$schedulingNew->date = $data['date'];
					$schedulingNew->hour = $hour->id;
					$schedulingNew->str_hour = $this->returnSpecificHour($hour->id);
					$schedulingNew->save();
					unset($schedulingNew);
					unset($scheduling);
					}catch(Zend_Exception $e){
						print_r($e);exit;
					}
				}
				return true;
			}
			return false;
		}catch(Zend_Exception $e) {
			return false;
		}
	}

	public function returnReport($startDate,$endDate)
	{
		$scheduling = new Application_Model_DbTable_Scheduling();
		$select = $scheduling->select()->setIntegrityCheck(false);
		$select	->from(array('p' => 'scheduling'), array('name', 'date') )
						->joinInner(array('h' => 'scheduling_hour_allowed'),'h.id=p.hour',array('hour' => 'TIME_FORMAT(h.hour,"%H:%i")') )
						->where('date >= ?',$startDate)
						->where('date <= ?',$endDate)
						->where('p.name NOT LIKE "Recesso"')
						->where('p.status = 0')
						->order('date ASC')
						->order('hour ASC');
		return $scheduling->fetchAll($select);
	}

	public function searchByCPF($cpf)
	{
		$scheduling = new Application_Model_DbTable_Scheduling();
		$select = $scheduling->select()->setIntegrityCheck(false);
		$select	->from(array('p' => 'scheduling'), array('id', 'name', 'email', 'date', 'str_hour') )
						->where('p.cpf = ?',$cpf)
						->where('p.date >= DATE_SUB(NOW(), INTERVAL 1 DAY)' )
						->where('p.status = 0');
		return $scheduling->fetchAll($select);
	}

	public function remove($id,$reschedule)
	{  
		$scheduling = new Application_Model_DbTable_Scheduling();
		$editscheduling = $scheduling->fetchRow($scheduling->select()->where('id = ?',$id) );
		$editscheduling->status = 1;	
		
		if($reschedule != 1){
			$this->saveLog(null,$id,3);
		}

		return $editscheduling->save(); 
	}

	public function reschedule($id, $hour, $date)
	{
		$scheduling = new Application_Model_DbTable_Scheduling();
		$schedulingOld = $scheduling->fetchRow($scheduling->select()->where('id = ?',$id));
		if($this->remove($id,1))
		{
			$schedulingNew = $scheduling->createRow();
			$schedulingNew->name = $schedulingOld->name;
			$schedulingNew->phone = $schedulingOld->phone;
			$schedulingNew->date = $date;
			$schedulingNew->hour = $hour;
			$schedulingNew->str_hour = $this->returnSpecificHour($hour);
			$new_id = $schedulingNew->save();
			$this->saveLog($new_id,$id,2);

			return $new_id;
		}
		return false;
	}

	public function addHoursPassed($events)
	{
		$schedulingHour = new Application_Model_DbTable_SchedulingHourAllowed();
		$select = $schedulingHour->select()->setIntegrityCheck(false);
		$select	->from(array('scheduling_hour_allowed'), array('id','hour' => 'TIME_FORMAT(hour,"%H:%i")') )
						->where('hour < DATE_SUB(NOW(), INTERVAL 2 HOUR)')
						->order('hour');
		$schedulingEvents = $schedulingHour->fetchAll($select);
		foreach($schedulingEvents as $schedulingEvent)
		{
			$aux = array('hour' => $schedulingEvent->id);
			array_push($events, $aux);
		}
		return $events;
	}

	public function checkScheduled($externalNumber)
	{
		$scheduling = new Application_Model_DbTable_Scheduling();
		$schedulingRow = $scheduling->fetchAll($scheduling->select()
												->where('name = ?',$externalNumber)
												->where('date >= ?', new Zend_Db_Expr('NOW()')));
		return count($schedulingRow);
	}

	public function returnScheduling($externalNumber)
	{
		$scheduling = new Application_Model_DbTable_Scheduling();
		return $scheduling->fetchRow($scheduling->select()
												->where('name = ?',$externalNumber)
												->where('date >= ?', new Zend_Db_Expr('NOW()')));
	}

	public function returnPrintCalendar($date){
		$scheduling = new Application_Model_DbTable_Scheduling();
		$select = $scheduling->select()->setIntegrityCheck(false);
		$select	->from(array('p' => 'scheduling'), array('name', 'date','str_hour') )
						->joinInner(array('h' => 'vehicle_historic'),'h.external_number=p.name')
						->joinInner(array('s' => 'vehicle_status'),'h.vehicle_id=s.vehicle_id',array('status') )
						->where('p.date like ?', $date)
						->where('h.end_historic_date is null')
						->order('p.str_hour');
		return $scheduling->fetchAll($select);
	}

}

