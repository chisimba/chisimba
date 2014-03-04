<?php
// security check - must be included in all scripts
if ( !$GLOBALS['kewl_entry_point_run'] ) {
die( "You cannot view this page directly" );
}
/**
*
* @copyright (c) 2000-2005, Kewl.NextGen ( http://kngforge.uwc.ac.za )
* @package lrs
* @version 0.1
* @since 20 September 2005
* @author Kevin Cyster
*/

/**
* The lrs index facet class is responsible for processing and managing the
* database tables for the lrsindex module.
*
* @author Kevin Cyster
*/
class awardindex_facet extends dbTable
{
    /**
    * @var types table _TypesDb an object reference.
    */
    var $_objTypesDb;

    /**
    * @var values table _ValuesDb an object reference.
    */
    var $_objValuesDb;

    /**
    * @var user _User an object reference.
    */
    var $_objUser;

    /**
    * Method to initialize the audit facet object
    *
    * @access private
    */
    function init()
    {
        $this -> _objTypesDb = $this -> getObject('dbindex', 'awardapi');
        $this -> _objValuesDb = $this -> getObject('dbindexvalues', 'awardapi');
        $this -> _objUser = $this -> getObject('user', 'security');
    }

    // -------------- tbl_lrs_index_types methods -------------//
    /**
    * Method for adding an index type to the database.
    *
    * @param string $shortName The name of the index (eg CPI)
    * @param string $name The name of the index (eg Consumer Price Index)
    * @param string $description The description of the index (eg The cost of a basket of goods)
    */
    function addIndexType($shortName, $name, $description)
    {
        $creatorId = $this -> _objUser -> userId();
        return $this -> _objTypesDb -> addRecord($shortName, $name, $description, $creatorId);
    }

    function getIndexes() {
    	return $this->_objTypesDb->getAll("WHERE display = 0");
    }

    function getIndexShortName($indexId) {
    	$result = $this->_objTypesDb->getRow('id',$indexId);
    	return $result['shortname'];
    }

    function getIndexName($indexId) {
    	$result = $this->_objTypesDb->getRow('id',$indexId);
    	return $result['name'];
    }

    /**
    * Method for editing an index type on the database.
    *
    * @param string $typeId The id of the index type being edited.
    * @param string $shortName The name of the index (eg CPI)
    * @param string $name The name of the index (eg Consumer Price Index)
    * @param string $description The description of the index (eg The cost of a basket of goods)
    */
    function editIndexType($typeId, $shortName, $name, $description)
    {
        $modifierId = $this -> _objUser -> userId();
        return $this -> _objTypesDb -> editRecord($typeId, $shortName, $name, $description, $modifierId);
    }

    /**
    * Method for deleting an index type
    *
    * @param string $typeId The id of the index type being deleted
    */
    function deleteIndexType($typeId)
    {
        return $this -> _objTypesDb -> deleteRecord($typeId);
    }

    /**
    * Method for listing all index types
    *
    * @return array $data All index types information.
    */
    function listIndexTypes()
    {
        return $this -> _objTypesDb -> listRecords();
    }

    /**
    * Method for retrieving an index type
    *
    * @param string $typeId The id of the index type to retrieve
    * @return array $data The index type information.
    */
    function getIndexType($typeId)
    {
        return $this -> _objTypesDb -> getRecord($typeId);
    }

    // -------------- tbl_lrs_index_values methods -------------//
    /**
    * Method for adding an index value to the database.
    *
    * @param string $typeId The id of the index type the value is being added to
    * @param string $indexDate The date of the index (eg Inflation at 01/12/2005)
    * @param string $indexValue The value of the index type at a specific date
    */
    function addIndexValue($typeId, $indexDate, $indexValue)
    {
        $creatorId = $this -> _objUser -> userId();
        return $this -> _objValuesDb -> addRecord($typeId, $indexDate, $indexValue, $creatorId);
    }

    /**
    * Method for editing an index value on the database.
    *
    * @param string $valueId The id of the index value being edited.
    * @param string $indexDate The date of the index (eg Inflation at 01/12/2005)
    * @param string $indexValue The value of the index type at a specific date
    */
    function editIndexValue($valueId, $indexDate, $indexValue)
    {
        $modifierId = $this -> _objUser -> userId();
        return $this -> _objValuesDb -> editRecord($valueId, $indexDate, $indexValue, $modifierId);
    }

    /**
    * Method for deleting an index value
    * @param string $id The id of the index value being deleted
    */
    function deleteIndexValue($valueId)
    {
        return $this -> _objValuesDb -> deleteRecord($valueId);
    }

    /**
    * Method for listing index values for an index type
    *
    * @param string $typeId The id of the index type to list values for
    * @return array $data All index values for an index type
    */
    function listIndexValues($typeId)
    {
        return $this -> _objValuesDb -> listRecords($typeId);
    }

    /**
    * Method for retreiving index value
    *
    * @param string $id The id of the index value
    * @return array $data All index value information
    */
    function getIndexValue($valueId)
    {
        return $this -> _objValuesDb -> getRecord($valueId);
    }

    /**
    * Method to return the id(pk) of an index
    * @param string $indexName The name of the index
    * @return string $indexId The id(pk) of the index
    */
    function getIndexId($indexName)
    {
       $sql = "SELECT * FROM tbl_award_indexes WHERE shortname LIKE '$indexName'";
       $rs = $this -> _objTypesDb -> getArray($sql);
       $index = current($rs);
       if(!empty($index)){
         $indexId = $index['id'];
       }

       if(isset($indexId)){
         return $indexId;
         } else {
             return FALSE;
       }
    }

    /**
     * Method to get the average of an index over the period of a year
     *
     * @param string $indexId Id of the index in question
     * @param string $year the year in question
     * @return float the average of the index for that year
     */
    function getIndexAverage($indexId,$year) {
    	$sql = "SELECT AVG(value) AS indexave FROM tbl_award_index_values
    			WHERE YEAR(indexdate) = '$year' AND typeid = '$indexId'";
    	$res = current($this->_objValuesDb->getArray($sql));
    	if (isset($res['indexave'])) {
    		return number_format($res['indexave'],2);
    	} else {
    		return false;
    	}
    }

    /**
     * Method to get the percentage the average of the index has increased
     * compared to the previous year
     *
     * @param string $indexId Id of the index in question
     * @param string $year the year in question
     * @return float the average increase of the index for that year
     */
    function getIndexIncrease($indexId,$year) {
    	$current = $this->getIndexAverage($indexId,$year);
    	$old = $this->getIndexAverage($indexId,$year-1);
    	if ($old && $current) {
    		$result = number_format(((float)(($current - $old)/$old)*100),2);
    	} else {
    		$result ='--';
    	}
    	return $result;
    }

    function getOrderedPairs($indexId,$year) {
    	for ($i=1;$i<13;$i++) {
    		$dTime = mktime(0,0,0,$i,1,$year);
    		$sqlTime = date('Y-m-d h:i:s',$dTime);
    		$coords[$i]['x'] = date('M y',$dTime);
    		$coords[$i]['y'] = $this->getPercentageDifference(date('m',$dTime),$year,$indexId);
    	}
    	//var_dump($coords);die;
    	return $coords;

    }

    /**
	 *Method to get the current index value
	 *
	 */
	function getCurrentIndexValue($month,$year,$id)
	{
		$sql = "WHERE typeid = '$id' AND MONTH(indexdate) = '$month' AND YEAR(indexdate) = '$year'";
		$result = $this->_objValuesDb->getAll($sql);

		$res = current($result);
		if (isset($res['value'])) {
			return number_format($res['value'],2);
		} else {
			return false;
		}
	}


	function getIndexIncreasePeriod($startDate, $months, $indexId) {
		$startTS = strtotime($startDate);
		$startYear = date('Y',$startTS);
		$startMonth = date('m',$startTS);
		$monthSum = (int)$startMonth+$months-1;
		$endTS = mktime(0,0,0,$monthSum,1,$startYear);
		$endYear = date('Y',$endTS);
		$endMonth = date('m',$endTS);
		$startVal = $this->getCurrentIndexValue($startMonth,$startYear,$indexId);
		$endVal = $this->getCurrentIndexValue($endMonth,$endYear,$indexId);
		while ($endVal == 0 && $endTS > $startTS) { 
		    $endTS = mktime(0,0,0,--$monthSum,1,$startYear);
		    $endYear = date('Y',$endTS);
		    $endMonth = date('m',$endTS);
		    $endVal = $this->getCurrentIndexValue($endMonth,$endYear,$indexId);
		    
		}
		if ($startVal == 0) {
			return false;
		}
		return (($endVal - $startVal)/$startVal)*100;
	}

	function getIndexIncreaseAgree($startDate, $months, $indexId) {
		$startTS = strtotime($startDate);
		$startYear = date('Y',$startTS);
		$startMonth = date('m',$startTS);
		$monthSum = (int)$startMonth+$months-1;
		$endTS = mktime(0,0,0,$monthSum,1,$startYear);
		$endYear = date('Y',$endTS);
		$endMonth = date('m',$endTS);
		$startVal = $this->getCurrentIndexValue($startMonth,$startYear,$indexId);
		$endVal = $this->getCurrentIndexValue($endMonth,$endYear,$indexId);
		if ($startVal == 0) {
			return false;
		}
		return (($endVal - $startVal)/$startVal)*100;
	}

	/**
	 *Method to get the percentage difference between the index values
	 *
	 */
	function getPercentageDifference($month, $year, $id)
	{
		$currentIndex = $this->getCurrentIndexValue($month,$year,$id);
		$previousIndex = $this->getCurrentIndexValue($month,$year-1,$id);
		if (($previousIndex == 0) || ($currentIndex == 0)) {
			$percentageDiff = '--';
		} else {
			$percentageDiff = number_format((($currentIndex - $previousIndex)/$previousIndex)*100,2);
		}
		return $percentageDiff;
	}
}
?>