<?php

class Application_Model_RateCalculation
{
	/**
	*	Register a new fuel coefficient.
	*	
	* @param array $data - fuel coefficient's data
	* @access public
	* @return integer
	*/
	public function newRateCalculationFuel($data)
	{
		$fuel = new Application_Model_DbTable_RateCalculationFuel();
		$fuelNew = $fuel->createRow($data);
		return $fuelNew->save();
	}


	/**
	*	List the fuel coefficient.
	*	
	* @access public
	* @return integer
	*/
	public function listRateCalculateFuel()
	{
		$fuel = new Application_Model_DbTable_RateCalculationFuel();
		$fuelRow = $fuel->fetchAll($fuel->select());
		return $fuelRow;
	}


	/**
	*	Register a new lubricant coefficient.
	*	
	* @param array $data - lubricant coefficient's data
	* @access public
	* @return integer
	*/
	public function newRateCalculationLubricant($data)
	{
		$lubricant = new Application_Model_DbTable_RateCalculationLubricant();
		$lubricantNew = $lubricant->createRow($data);
		return $lubricantNew->save();
	}
	

	/**
	*	List the lubricant coefficient.
	*	
	* @access public
	* @return integer
	*/
	public function listRateCalculateLubricant()
	{
		$lubricant = new Application_Model_DbTable_RateCalculationLubricant();
		$lubricantRow = $lubricant->fetchAll($lubricant->select());
		return $lubricantRow;
	}


	/**
	*	Register a new tread coefficient.
	*	
	* @param array $data - tread coefficient's data
	* @access public
	* @return integer
	*/
	public function newRateCalculationTread($data)
	{
		$tread = new Application_Model_DbTable_RateCalculationTread();
		$treadNew = $tread->createRow($data);
		return $treadNew->save();
	}
	

	/**
	*	List the tread coefficient.
	*	
	* @access public
	* @return integer
	*/
	public function listRateCalculateTread()
	{
		$tread = new Application_Model_DbTable_RateCalculationTread();
		$treadRow = $tread->fetchAll($tread->select());
		return $treadRow;
	}


	/**
	*	Register a new acessories coefficient.
	*	
	* @param array $data - acessories coefficient's data
	* @access public
	* @return integer
	*/
	public function newRateCalculationAcessories($data)
	{
		$acessories = new Application_Model_DbTable_RateCalculationAcessories();
		$acessoriesNew = $acessories->createRow($data);
		return $acessoriesNew->save();
	}
	

	/**
	*	List the acessories coefficient.
	*	
	* @access public
	* @return integer
	*/
	public function listRateCalculateAcessories()
	{
		$acessories = new Application_Model_DbTable_RateCalculationAcessories();
		$acessoriesRow = $acessories->fetchAll($acessories->select());
		return $acessoriesRow;
	}


	/**
	*	Register a new km production coefficient.
	*	
	* @param array $data - km production coefficient's data
	* @access public
	* @return integer
	*/
	public function newRateCalculationKmProduction($data)
	{
		/*$kmProduction = new Application_Model_DbTable_RateCalculationKmProduction();
		$kmProductionNew = $kmProduction->createRow($data);
		return $kmProductionNew->save();
		*/
	}
	

	/**
	*	List the km production coefficient.
	*	
	* @access public
	* @return integer
	*/
	public function listRateCalculatekmProduction()
	{
		/*$kmProduction = new Application_Model_DbTable_RateCalculationKmProduction();
		$kmProductionRow = $kmProduction->fetchAll($kmProduction->select());
		return $kmProductionRow;
		*/
	}


	/**
	*	Register a new vehicle depreciation coefficient.
	*	
	* @param array $data - vehicle depreciation coefficient's data
	* @access public
	* @return integer
	*/
	public function newRateCalculationVehicleDepreciation($data)
	{
		$vehicleDepreciation = new Application_Model_DbTable_RateCalculationVehicleDepreciation();
		//$data['coefficient'] = Application_Model_General::NOME DA FUNÇÃO($data['coefficient']);
		$vehicleDepreciationNew = $vehicleDepreciation->createRow($data); 
		return $vehicleDepreciationNew->save();
	}
	

	/**
	*	List the vehicle depreciation coefficient.
	*	
	* @access public
	* @return integer
	*/
	public function listRateCalculateVehicleDepreciation()
	{
		$vehicleDepreciation = new Application_Model_DbTable_RateCalculationVehicleDepreciation();
		$vehicleDepreciationRow = $vehicleDepreciation->fetchAll($vehicleDepreciation->select());
		return $vehicleDepreciationRow;
	}


	/**
	*	Register a new vehicle remuneration coefficient.
	*	
	* @param array $data - vehicle remuneration coefficient's data
	* @access public
	* @return integer
	*/
	public function newRateCalculationVehicleRemuneration($data)
	{
		$vehicleRemuneration = new Application_Model_DbTable_RateCalculationVehicleRemuneration();
		$vehicleRemunerationNew = $vehicleRemuneration->createRow($data); 
		return $vehicleRemunerationNew->save();
	}
	

	/**
	*	List the vehicle remuneration coefficient.
	*	
	* @access public
	* @return integer
	*/
	public function listRateCalculateVehicleRemuneration()
	{
		$vehicleRemuneration = new Application_Model_DbTable_RateCalculationVehicleRemuneration();
		$vehicleRemunerationRow = $vehicleRemuneration->fetchAll($vehicleRemuneration->select());
		return $vehicleRemunerationRow;
	}




	/**
	*	Register a new operation crew coefficient.
	*	
	* @param array $data - operation crew coefficient's data
	* @access public
	* @return integer
	*/
	public function newRateCalculationOperationCrew($data)
	{
		$operationCrew = new Application_Model_DbTable_RateCalculationOperationCrew();
		$operationCrewNew = $operationCrew->createRow($data); 
		return $operationCrewNew->save();
	}
	

	/**
	*	List the operation crew coefficient.
	*	
	* @access public
	* @return integer
	*/
	public function listRateCalculateOperationCrew()
	{
		$operationCrew = new Application_Model_DbTable_RateCalculationOperationCrew();
		$operationCrewRow = $operationCrew->fetchAll($operationCrew->select());
		return $operationCrewRow;
	}
	
}