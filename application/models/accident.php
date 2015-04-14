<?php

class Application_Model_Accident
{
	protected $accidentId;

	/**
	*	Register a new accident file on the system.
	*	
	*	@param array $file - accident data file
	* @access public
	*/
	public function loadFile($file)
	{
		$aux = explode("_",substr($file['accident_file']["name"],23,17)); // uploaded file have a default filename, so the data is always at same place, char num 23 to 40.
		$ini_date = substr($aux[0],0,4)."-".substr($aux[0],4,2)."-".substr($aux[0],6,2);
		$end_date = substr($aux[1],0,4)."-".substr($aux[1],4,2)."-".substr($aux[1],6,2);
		$confirm = new Application_Model_DbTable_Accident();
		$check = $confirm->fetchRow($confirm->select()->where('ini_date= ?',$ini_date)
																								->where('end_date= ?',$end_date));
		if($check == true){
			$code = "exists";
			return $code;
		}else{
		if (($handle = fopen($file['accident_file']['tmp_name'], "r")) !== FALSE) {
			fgetcsv($handle); // skip first line of csv (Headers)
			while (($data = fgetcsv($handle, 1000, "|")) !== FALSE){
				$is_code = new Application_Model_Ibge();
				$code = $is_code->isTrueByCode($data[5]);
				if(is_null($code["name"]) == FALSE) {
					$this->registerUpload($ini_date,$end_date);
        	$this->registerData($data);
				}
			}
				fclose($handle);
			}
			return $this->accidentId;
		}
	}

	/**
	*	Register the upload time and user of a new accident file data on the system table.
	*	
	*	@param var $ini_date - initial period of avaliation
	*	@param var $end_date - final period of avaliation
	* @access private
	*/
	public function registerUpload($ini_date, $end_date){
		$accident = new Application_Model_DbTable_Accident();
		$test = $accident->fetchRow($accident->select()->where('ini_date LIKE ?',$ini_date)
																									 ->where('end_date LIKE ?',$end_date)); // test if file has already been uploaded by the date
		if(!count($test))
		{
			$authNamespace = new Zend_Session_Namespace('userInformation');
			$file_time = new Application_Model_DbTable_Accident();
			$file_upload = $file_time->createRow();
			$file_upload->user = $authNamespace->user_id; 
			$file_upload->date_upload = new Zend_Db_Expr('NOW()');
			$file_upload->ini_date = $ini_date;
			$file_upload->end_date = $end_date;
			$this->accidentId = $file_upload->save();
			if($this->accidentId)
				{
					return true;
				}
		} 
			return false;
	}

	/**
	*	Register the accident data on the system table.
	*	
	*	@param array $data - accident data file
	* @access private
	*/
	public function registerData($data)
	{
		try{		
			$plate = $data[4][0].$data[4][1].$data[4][2].' '.$data[4][3].$data[4][4].$data[4][5].$data[4][6];
			if($this->existVehicle($plate))
			{
				$file = new Application_Model_DbTable_AccidentData();
				$file_new = $file->createRow();
				$file_new->num_reds = $data[0];
				$file_new->cause = $data[1];
				$file_new->accident_time = Application_Model_General::dateTimeToUs($data[2]);
				$file_new->accident_registration = Application_Model_General::dateTimeToUs($data[3]);
				$file_new->plate = $plate;
				$file_new->ibge_city_code = $data[5];
				$file_new->city = utf8_encode($data[6]);
				$file_new->neighborhood = utf8_encode($data[7]);
				$file_new->area_type = utf8_encode($data[8]);
				$file_new->area_name = utf8_encode($data[9]);
				$file_new->area_type_cross = utf8_encode($data[10]);
				$file_new->area_type_cross_name = utf8_encode($data[11]);
				$file_new->area_number = $data[12];
				$file_new->area_comple_info = $data[13];
				$file_new->area_comple_number = $data[14];
				$file_new->latitude = $data[15];
				$file_new->longitude = $data[16];
				$file_new->accident_id = $this->accidentId;
				$file_new->save();
			}
		}catch(Zend_Exception $e){
    }
	}

	/**
	*	Checks if vehicle occured in accident exists in the metropolitan system
	*	
	*	@param string $plate - vehicle's plate
	* @access private
	*	@return boolean
	*/
	private function existVehicle($plate)
	{
		$vehicle = new Application_Model_DbTable_Vehicle();
		$vehicleRow = $vehicle->fetchAll($vehicle->select()->where('plate = ?',$plate));
		return count($vehicleRow);
	}

	/**
	*	List all files on the system.
	*	
	* @access public
	*/
	public function lists()
	{
		try{
			$accident = new Application_Model_DbTable_Accident();
			$select = $accident->select()->setIntegrityCheck(false);
			$select	->from(array('a' => 'accident'),array("id", "ini_date", "end_date", "month_ini" => "MONTH(ini_date)", 
				"month_end" => "MONTH(end_date)", "year_ini" => "YEAR(ini_date)","year_end" => "YEAR(end_date)") )
							->order('ini_date desc');
			return $accident->fetchAll($select);
		}catch(Zend_Exception $e){
    }
	}

	/**
	*	List all file on the system between given period, with the month and year separeted.
	*	
	*	@param var $ini - initial date of reference
	*	@param var $end - final date of reference
	* @access public
	*/
	public function returnByDate($ini, $end)
	{
		try{
			$accident = new Application_Model_DbTable_Accident();
			$select = $accident->select()->setIntegrityCheck(false);
			$select	->from(array('a' => 'accident'),array("id", "ini_date", "end_date", "month_ini" => "MONTH(ini_date)", 
				"month_end" => "MONTH(end_date)", "year_ini" => "YEAR(ini_date)","year_end" => "YEAR(end_date)") )
							->where('ini_date >= ?', $ini)
							->where('end_date <= ?', $end);
			return $accident->fetchAll($select);
		}catch(Zend_Exception $e){
    }
	}

	/**
	*	Get all data of accident's data.
	*	
	*	@param integer $id - initial date of reference
	* @access public
	* @return Zend_Db_Table_Row
	*/
	public function returnById($id)
	{
		$accident = new Application_Model_DbTable_AccidentData();
		return $accident->fetchAll($accident->select()->where('accident_id = ?',$id)->order('accident_time'));
	}

	/**
	*	List all data on the system between given period of a requested.
	*	
	*	@param var $ini - initial date of reference
	*	@param var $end - final date of reference
	* @access public
	*/
	public function returnInfoByDate($ini, $end)
	{
		try{
			$accident = new Application_Model_DbTable_Accident();
			$select = $accident->select()->setIntegrityCheck(false);
			$select	->from(array('a' => 'accident'),array("id", "ini_date", "end_date"))
							->joinInner(array('ac' => 'accident_data'), 'a.id = ac.accident_id')
							->where('a.ini_date >= ?', Application_Model_General::dateToUs($ini))
							->where('a.end_date <= ?', Application_Model_General::dateToUs($end));
			return $accident->fetchAll($select);
		}catch(Zend_Exception $e){
  	}
	}

	/**
	*	List all data on the system of a file by it id.
	*	
	*	@param var $id - Id of the file
	* @access public
	*/
	public function returnCityQuantById($startDate, $endDate)
	{
		$accident = new Application_Model_DbTable_Accident();
		$select = $accident->select()->setIntegrityCheck(false);
		$select	->from(array('ad' => 'accident_data'), array())
						->joinLeft(array('v' => 'vehicle'), 'v.plate = ad.plate', array('id'))
						->joinLeft(array('vh' => 'vehicle_historic'), 'v.id=vh.vehicle_id AND (vh.end_historic_date IS NULL)', 
									array('rit' => 'consortium', "amount" => new Zend_Db_Expr("count(*)")))
						->where('ad.accident_time >= ?',$startDate)
						->where('ad.accident_time <= ?',$endDate)
						->group("vh.consortium");
		return $accident->fetchAll($select);
	}

	/**
	*	List all data of accidents per consortium.
	*	
	* @access public
	* @return Zend_Db_Row_Table
	*/
	public function returnAllAccidents()
	{
		$accident = new Application_Model_DbTable_Accident();
		$select = $accident->select()->setIntegrityCheck(false);
		$select	->from(array('ad' => 'accident_data'), array())
						->joinInner(array('v' => 'vehicle'), 'v.plate = ad.plate', array())
						->joinInner(array('vh' => 'vehicle_historic'), 'v.id=vh.vehicle_id AND vh.end_historic_date IS NULL', 
									array('rit' => 'consortium', "amount" => new Zend_Db_Expr("count(*)")))
						->group("vh.consortium");
		return $accident->fetchAll($select);
	}

	/**
	*	List all data of accidents per city.
	*	
	* @access public
	* @return Zend_Db_Row_Table
	*/
	public function returnAccidentsCity($startDate, $endDate)
	{
		$accident = new Application_Model_DbTable_Accident();
		$select = $accident->select()->setIntegrityCheck(false);
		$select	->from(array('ad' => 'accident_data'), array())
						->joinInner(array('i' => 'ibge'), 'i.ibge_code = ad.ibge_city_code', array('name', "amount" => new Zend_Db_Expr("count(*)")))
						->joinLeft(array('v' => 'vehicle'), 'v.plate = ad.plate', array('id'))
						->joinLeft(array('vh' => 'vehicle_historic'), 'v.id=vh.vehicle_id AND (vh.end_historic_date IS NULL)', 
									array('rit' => 'consortium', "amount" => new Zend_Db_Expr("count(*)")))
						->where('ad.accident_time >= ?',$startDate)
						->where('ad.accident_time <= ?',$endDate)
						->group("i.ibge_code");
		return $accident->fetchAll($select);
	}

	/**
	*	Erase a file upload reference.
	*	
	*	@param var $id - Id of the file that is required to be deleted
	* @access public
	*/
	public function deleteFile($id)
	{
		$accident = new Application_Model_DbTable_Accident();
		$file = $accident->fetchRow($accident->select()->where('id = ?',$id));
		if($file)
		{
				$file->delete();
		}
		return true;
	}
}