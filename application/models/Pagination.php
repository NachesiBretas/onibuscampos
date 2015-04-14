<?php

/**
*	Model Pagination that execute functions of general pagination on system.
*
*	@author Andre Gonzaga
*	@date 2013-02-06
*/
class Application_Model_Pagination
{

	/**
	*	Generate pagination of certain Zend_Db result and a certain page and render it. 
	*
	*	@access public
	*	@param array $rows
	*	@param integer $page
	*	@param integer $itens
	*	@param integer $range
	*	@return Zend_Paginator
	*	@author Andre Gonzaga
	*	@date 2013-02-06
	*/
	public function generatePagination($rows,$page,$itens=10,$range=10)
	{
		$paginator = Zend_Paginator::factory($rows);
		$paginator->setItemCountPerPage($itens);
		$paginator->setCurrentPageNumber($page);
		$paginator->setPageRange($range);
		return $paginator;
	}
}

