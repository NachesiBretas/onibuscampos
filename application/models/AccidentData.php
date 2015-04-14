<?php

class Application_Model_AccidentData
{
	
	/**
	*	Erase a file data.
	*	
	*	@param var $id - Id of the file that is required to be deleted
	* @access public
	*/

	public function deleteFile($id)
	{
		$accidentData = new Application_Model_DbTable_AccidentData();
		$file = $accidentData->fetchAll($accidentData->select()->where('accident_id = ?',$id));
		if($file)
		{
			foreach ($file as $files) {
				$files->delete();
			}
			
		}
		return true;
	}
}