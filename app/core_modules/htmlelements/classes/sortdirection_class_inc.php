<?php
/* ----------- data class extends dbTable for tbl_brawam_reports------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Sort Direction Class
*
* This class is used to detect which column is being sorted by, and allow reverse sorting
* Explanation: Often you want to have links that sort columns, e.g.
*
* http://nextgen/index.php?module=something&sort=firstname
*
* Then you want to reverse it to
*
* http://nextgen/index.php?module=something&sort=firstname&direction= DESC
*
* This function will help you achieve that
*
*/
class sortdirection extends object
{
    
    /**
    * Method to check whether a column is being sorted, and then whether to reverse the direction ot not.
    * @param string $item The Item you are sorting on
    * @param string $module The module you are calling this class from
    * @param string $action Action to take
    * @retrun string A formed URL
    */
    public function sortItems($item, $module = NULL, $action = NULL)
    {
        if ($this->getParam('sort') == $item && strtoupper($this->getParam('direction')) == 'ASC') {
            $direction = 'DESC';
        } else {
            $direction = 'ASC';
        }
    
        return $this->uri(array('sort'=>$item, 'direction'=>$direction, 'action'=>$action), $module);
    }

}

?>