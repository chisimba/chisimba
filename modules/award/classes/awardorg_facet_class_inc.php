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
* @author Kevin Cyster
*/

/**
* The lrs organisation facet class is responsible for processing and managing the
* database tables for the lrsorg module.
*
* @author Kevin Cyster
*/
class awardorg_facet extends dbTable
{
    /**
    * @var bargaining unit table _UnitDb an object reference.
    */
    var $_objUnitDb;

    /**
    * @var party type table _TypeDb an object reference.
    */
    var $_objTypeDb;

    /**
    * @var party table _PartyDb an object reference.
    */
    var $_objPartyDb;

    /**
    * @var party branch table _BranchDb an object reference.
    */
    var $_objBranchDb;

    /**
    * @var group table _GroupDb an object reference.
    */
    var $_objGroupDb;

    /**
    * @var user _User an object reference.
    */
    var $_objUser;

    /**
    * Method to initialize the area facet object
    *
    * @access private
    */
    function init()
    { 
        $this -> _objUnitDb = $this -> getObject('dbunit', 'awardapi');
        $this -> _objTypeDb = $this -> getObject('dbindex', 'awardapi');
        $this -> _objPartyDb = $this -> getObject('dbparty', 'awardapi');
        $this -> _objBranchDb = $this -> getObject('dbunitbranch', 'awardapi');
        $this -> _objUser = $this -> getObject('user', 'security');
    }

    // -------------- tbl_lrs_org_unit methods -------------//
    /**
    * Method for adding a bargaining unit to the database.
    *
    * @param string $name The name of the district
    */
    function addBargainingUnit($name)
    {
        $creatorId = $this -> _objUser -> userId();
        return $this -> _objUnitDb -> addRecord($name, $creatorId);
    }

    /**
    * Method for editing a bargaining unit on the database.
    *
    * @param string $unitId The id of the bargaining unit
    * @param string $name The name of the district
    */
    function editBargainingUnit($unitId, $name)
    {
        $modifierId = $this -> _objUser -> userId();
        return $this -> _objUnitDb -> editRecord($unitId, $name, $modifierId);
    }

    /**
    * Method for deleting a bargaining unit
    *
    * @param string $unitId The id of the bargaining unit being deleted
    */
    function deleteBargainingUnit($unitId)
    {
        return $this -> _objUnitDb -> deleteRecord($unitId);
    }

    /**
    * Method for listing all bargaining units
    *
    * @return array $data All bargaining unit information.
    */
    function listBargainingUnits()
    {
        return $this -> _objUnitDb -> listRecords();
    }

    /**
    * Method for retreiving a bargaining unit
    *
    * @param string $unitId The id of the bargaining unit to retrieve
    * @return array $data The bargaining unit data
    */
    function getBargainingUnit($unitId)
    {
        return $this -> _objUnitDb -> getRecord($unitId);
    }

    // -------------- tbl_lrs_org_type methods -------------//
    /**
    * Method for adding a party type to the database.
    *
    * @param string $name The name of the party type
    */
    function addPartyType($name)
    {
        $creatorId = $this -> _objUser -> userId();
        return $this -> _objTypeDb -> addRecord($name, $creatorId);
    }

    /**
    * Method for editing a party type on the database.
    *
    * @param string $typeId The id of the party type being edited.
    * @param string $name The name of the party type
    */
    function editPartyType($typeId, $name)
    {
        $modifierId = $this -> _objUser -> userId();
        return $this -> _objTypeDb -> editRecord($typeId, $name, $modifierId);
    }

    /**
    * Method for deleting a party type
    * @param string $typeId The id of the party type being deleted
    */
    function deletePartyType($typeId)
    {
        return $this -> _objTypeDb -> deleteRecord($typeId);
    }

    /**
    * Method for listing all party types
    *
    * @return array $data All party type information.
    */
    function listPartyTypes()
    {
        return $this -> _objTypeDb -> listRecords();
    }

    /**
    * Method for retreiving a party type
    *
    * @param string $typeId The id of the party type to retrieve
    * @return array $data The party type data
    */
    function getPartyType($typeId)
    {
        return $this -> _objTypeDb -> getRecord($typeId);
    }

    // -------------- tbl_award_party methods -------------//
    /**
    * Method for adding a party to the database.
    *
    * @param string $typeId The id of the party type
    * @param string $name The name of the party
    * @param string $abbreviation The abbreviation for the party
    * @param string $regNo The registration number of the party
    */
    function addParty($typeId, $name, $abbreviation, $regNo)
    {
        $creatorId = $this -> _objUser -> userId();
        return $this -> _objPartyDb -> addRecord($typeId, $name, $abbreviation, $regNo, $creatorId);
    }

    /**
    * Method for editing a party on the database.
    *
    * @param string $partyId The id of the party being edited.
    * @param string $typeId The id of the party type
    * @param string $name The name of the party
    * @param string $abbreviation The abbreviation for the party
    * @param string $regNo The registration number of the party
    */
    function editParty($partyId, $typeId, $name, $abbreviation, $regNo)
    {
        $modifierId = $this -> _objUser -> userId();
        return $this -> _objPartyDb -> editRecord($partyId, $typeId, $name, $abbreviation, $regNo, $modifierId);
    }

    /**
    * Method for deleting a party
    * @param string $partyId The id of the party being deleted
    */
    function deleteParty($partyId)
    {
        return $this -> _objPartyDb -> deleteRecord($partyId);
    }

    /**
    * Method for listing all parties
    *
    * @return array $data All party information.
    */
        function listAllParties()
    {
        return $this -> _objPartyDb -> listAllRecords();
    }

    /**
    * Method for listing all parties for a party type
    *
    * @param string $typeId The id of the party type to list parties for
    * @return array $data All party information.
    */
        function listPartiesPerPartyType($typeId)
    {
        return $this -> _objPartyDb -> listTypeRecords($typeId);
    }


    /**
    * Method for retreiving a party
    *
    * @param string $partyId The id of the party to retrieve
    * @return array $data The party data
    */
        function getParty($partyId)
    {
        return $this -> _objPartyDb -> getRecord($partyId);
    }

    // -------------- tbl_lrs_org_branch methods -------------//
    /**
    * Method for adding a party branch to the database.
    *
    * @param string $partyId The id of the party the party branch is being added to
    * @param string $districtId The id of the district the party branch falls under
    * @param string $name The name of the party branch
    * @param string $tel The telephone number of the party branch
    * @param string $fax The fax number of the party branch
    * @param string $url The url of the party branch
    * @param string $email The email of the party branch
    * @param string $address1 The address line 1 of the party branch
    * @param string $address2 The address line 2 of the party branch
    * @param string $postal1 The postal line 1 of the party branch
    * @param string $postalTown The postal town of the party branch
    * @param string $postalCode The postal code of the party branch
    */
    function addPartyBranch($partyId, $districtId, $name, $tel, $fax, $url, $email, $address1, $address2, $postal1, $postalTown, $postalCode)
    {
        $creatorId = $this -> _objUser -> userId();
        return $this -> _objBranchDb -> addRecord($partyId, $districtId, $name, $tel, $fax, $url, $email, $address1, $address2, $postal1, $postalTown, $postalCode, $creatorId);
    }

    /**
    * Method for editing a party branch on the database.
    *
    * @param string $branchId The id of the branch being edited
    * @param string $districtId The id of the district the party branch falls under
    * @param string $name The name of the party branch
    * @param string $tel The telephone number of the party branch
    * @param string $fax The fax number of the party branch
    * @param string $url The url of the party branch
    * @param string $email The email of the party branch
    * @param string $address1 The address line 1 of the party branch
    * @param string $address2 The address line 2 of the party branch
    * @param string $postal1 The postal line 1 of the party branch
    * @param string $postalTown The postal town of the party branch
    * @param string $postalCode The postal code of the party branch
    * @param string $modifierId The id of the user editing the party branch record
    */
    function editPartyBranch($branchId, $districtId, $name, $tel, $fax, $url, $email, $address1, $address2, $postal1, $postalTown, $postalCode)
    {
        $modifierId = $this -> _objUser -> userId();
        return $this -> _objBranchDb -> editRecord($branchId, $districtId, $name, $tel, $fax, $url, $email, $address1, $address2, $postal1, $postalTown, $postalCode, $modifierId);
    }

    /**
    * Method for deleting a party branch
    * @param string $party branchId The id of the party branch being deleted
    */
    function deletePartyBranch($branchId)
    {
        return $this -> _objBranchDb -> deleteRecord($branchId);
    }

    /**
    * Method for listing all party branches
    *
    * @return array $data All party branch information.
    */
        function listPartyBranches()
    {
        return $this -> _objBranch -> listRecords();
    }

    /**
    * Method for listing all party branches per party
    *
    * @return array $data All party branch information.
    */
        function listPartyBranchesPerParty($partyId)
    {
        return $this -> _objBranchDb -> ListPartyRecords($partyId);
    }

    /**
    * Method for retreiving a party branch
    *
    * @param string $branchId The id of the party branch to retrieve
    * @return array $data The party branch data
    */
        function getPartyBranch($branchId)
    {
        return $this -> _objBranchDb -> getRecord($branchId);
    }

    function getUnitTradeUnion($unitId) {
    	$sql = "SELECT party.id AS id, party.abbreviation AS abbrev
                FROM tbl_award_party AS party, tbl_award_branch AS party_branch, tbl_award_unit_branch as bupb
    			WHERE bupb.unitid = '$unitId' AND bupb.branchid = party_branch.id
    			AND party_branch.partyid = party.id";
    	$arr = $this->_objPartyDb->getArray($sql);
    	return current($arr);
    }
}
?>
