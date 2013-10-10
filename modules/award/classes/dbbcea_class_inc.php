<?
/* ----------- data class extends dbTable for tbl_lrs_gender_bcea ----------*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}

/**
* Model class for the table tbl_award_gender_bcea
* @author Nic Appleby
*/

class dbbcea extends dbTable
{
    function init()
    {
        parent::init('tbl_award_gender_bcea');
	}

	function getData($category,$name) {
		$rec = $this->getAll("WHERE category = '$category' AND name = '$name'");
		return current($rec);
	}
}
?>