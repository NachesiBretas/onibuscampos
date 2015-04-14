<?php

class Application_Model_Gsection{


	/**
	*	Load the file uploaded by the user.
	*	
	*	@param var $file - uploaded file of fares
	* @access public
	* @return gsectionId
	*/
	public function loadFile($file){
		if (($handle = fopen($file['gsection_file']['tmp_name'], "r")) !== FALSE) {
			for ($i=1; $i<9 ; $i++) { 
				fgetcsv($handle);
			}
			$head = fgetcsv($handle,2000, ";");
			$old = strstr($head[11], ' ', true);
			$new = strstr($head[12], ' ', true);
			$this->registerUpload($old, $new);
			while (($data = fgetcsv($handle, 2000, ";")) !== FALSE){
				if (strlen($data[2]) > 4) {
					$this->registerData($data);
				}
			}
		}
		fclose($handle);
		return $this->gsectionId;
	}


	/**
	*	Register the time, user and year of reference of the file.
	*	
	*	@param var $old - value of old fare
	*	@param var $new - value of new fare
	* @access public
	*/
	public function registerUpload($old, $new){
		try{
			$gsection = new Application_Model_DbTable_Gsection();
			$authNamespace = new Zend_Session_Namespace('userInformation');
			$file_time = new Application_Model_DbTable_Gsection();
			$file_upload = $file_time->createRow();
			$file_upload->user = $authNamespace->user_id; 
			$file_upload->upload_time = new Zend_Db_Expr('NOW()');
			$file_upload->old_fare_year = $old;
			$file_upload->new_fare_year = $new;
			$this->gsectionId = $file_upload->save();
			if($this->gsectionId)
				{
					return true;
				}
		}catch(Zend_Exception $e) {
        	echo $e->getMessage();
    }
  }


	/**
	*	Register the data of the group section lines.
	*	
	*	@param var $data - date from the uploaded file
	* @access public
	*/
	public function registerData($data){
		try{
			$file = new Application_Model_DbTable_GsectionFares();
			$file_new = $file->createRow();
			$file_new->num_comunication = $data[2];
			$file_new->line_name = utf8_encode($data[5]);
			$file_new->old_fare = str_replace(",", ".", $data[11]);
			$file_new->new_fare = str_replace(",", ".", $data[12]);
			$file_new->gsection_id = $this->gsectionId;
			$file_new->save();
		}
		catch(Zend_Exception $e) {
        	echo $e->getMessage();
    }
	}


	/**
	*	Return the id of the most recent uploaded file.
	*	
	*	@param var $old - value of old fare
	*	@param var $new - value of new fare
	* @access public
	* @return gsection id (int)
	*/
	public function lastUpload(){
		$section = new Application_Model_DbTable_Gsection();
		$select = $section->select()->setIntegrityCheck(false);
		$select->from(array('g' => "gsection"),array('last' => new Zend_Db_Expr("MAX(id)")));
		return $section->fetchRow($select);		
	}


	/**
	*	Register the year of reference of the most recent uploaded file.
	*	
	*	@param var $id - the id of the most recent uploaded file
	* @access public
	* @return the year of reference of the collumns 'new_fare_year' and 'old fare_year'
	*/
	public function yearReference($id){
		$section = new Application_Model_DbTable_GsectionFares();
		$select = $section->select()->setIntegrityCheck(false);
		$select->from(array('g' => "gsection"),array('new_fare_year', 'old_fare_year'))
					 ->where('g.id = ?', $id);
		return $section->fetchRow($select);
	}


	/**
	*	Register the year of reference of the most recent uploaded file.
	*	
	*	@param var $id - the id of the most recent uploaded file
	* @access public
	* @return the year of reference of the collumns 'new_fare_year' and 'old fare_year'
	*/
	public function findAll($id){

		$section = new Application_Model_DbTable_GsectionFares();
		$select = $section->select()->setIntegrityCheck(false);
		$select->from(array('gf' => "gsection_fares"))
					 ->joinInner(array('g' => 'gsection'), 'g.id = gf.gsection_id')
					 ->where('gf.gsection_id = ?', $id);
		return $section->fetchAll($select);
	}


	/**
	*	Search and filter the file by the num_comunication (comunication number).
	*	
	*	@param var $num comunication - comunication number of the line
	* @param var $last - id of the most recent uploaded file
	* @access public
	* @return  the file data filtered by comunication number 
	*/
	public function findByNum($num_comunication, $last){
		$section = new Application_Model_DbTable_GsectionFares();
		$select = $section->select()->setIntegrityCheck(false);
		$select	->from(array('gf' => 'gsection_fares'))
						->joinInner(array('g' => 'gsection'), 'g.id = gf.gsection_id', array('section_id' => 'id'))
					  ->where('gf.gsection_id = ?', $last)
						->where('gf.num_comunication LIKE ?','%'.$num_comunication.'%');
		return $section->fetchAll($select);
	}
	

	/**
	*	Search and filter the file by the line_name (name of the line).
	*	
	*	@param var $line_name - name of the line
	* @param var $last - id of the most recent uploaded file
	* @access public
	* @return  the file data filtered by name of the line
	*/
	public function findByName($line_name, $last){
		$section = new Application_Model_DbTable_GsectionFares();
		$select = $section->select()->setIntegrityCheck(false);
		$select	->from(array('gf' => 'gsection_fares'))
						->joinInner(array('g' => 'gsection'), 'g.id = gf.gsection_id', array('section_id' => 'id'))
					 	->where('gf.gsection_id = ?', $last)
						->where('gf.line_name LIKE ?','%'.$line_name.'%');
		return $section->fetchAll($select);
	}


	/**
	*	Search and filter the file by the old_fare (value of the old fare of the line).
	*	
	*	@param var $old_fare - value of the old fare of the line
	* @param var $last - id of the most recent uploaded file
	* @access public
	* @return  the file data filtered by the value of old fare
	*/
	public function findByOld($old_fare, $last){
		$section = new Application_Model_DbTable_GsectionFares();
		$select = $section->select()->setIntegrityCheck(false);
		$select	->from(array('gf' => 'gsection_fares'))
						->joinInner(array('g' => 'gsection'), 'g.id = gf.gsection_id', array('section_id' => 'id'))
					 	->where('gf.gsection_id = ?', $last)
						->where('gf.old_fare = ?', str_replace(",", ".", $old_fare));
		return $section->fetchAll($select);
	}


	/**
	*	Search and filter the file by the new_fare (value of the new fare of the line).
	*	
	*	@param var $new_fare - value of the new fare of the line
	* @param var $last - id of the most recent uploaded file
	* @access public
	* @return  the file data filtered by the value of new fare
	*/
	public function findByNew($new_fare, $last){
		$section = new Application_Model_DbTable_GsectionFares();
		$select = $section->select()->setIntegrityCheck(false);
		$select	->from(array('gf' => 'gsection_fares'))
						->joinInner(array('g' => 'gsection'), 'g.id = gf.gsection_id', array('section_id' => 'id'))
					 	->where('gf.gsection_id = ?', $last)
						->where('gf.new_fare = ?', str_replace(",", ".", $new_fare));
		return $section->fetchAll($select);
	}
}