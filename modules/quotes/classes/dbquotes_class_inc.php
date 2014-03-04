<?php
/* ----------- data class extends dbTable for tbl_quotes------------*/
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }


/**
* Model class for the table tbl_quotes
*
* @author Derek Keats
*
* $Id: dbquote_class_inc.php,v 1.1 2006/09/14 08:19:14 Abdurahim Ported to PHP5
*
*/
class dbquotes extends dbTable
{

    /**
    * Constructor method to define the table
    */
    public function init() {
        parent::init('tbl_quotes');
        $this->objUser = & $this->getObject("user", "security");
    }

    /**
    * Save method for editing a record in this table
    * @param string $mode: edit if coming from edit, add if coming from add
    */
    public function saveRecord($mode, $userId)
    {   try
        {
            $id=$this->getParam('id', NULL);
            $quote = $this->getParam('quote', NULL);
            $whosaidit = $this->getParam('whosaidit', NULL);

            // if edit use update
            if ($mode=="edit") {
                $this->update("id", $id, array(
                'quote' => $quote,
                'whosaidit' => $whosaidit,
                'datemodified' => $this->now(),
                'modified' => $this->now(),
                'modifierid' => $this->objUser->userId()));

            }//if
            // if add use insert
            if ($mode=="add") {
                $this->insert(array(
                'quote' => $quote,
                'whosaidit' => $whosaidit,
                'datecreated' => $this->now(),
                'creatorid' => $this->objUser->userId(),
                'modified' => $this->now()));

            }//if
        } catch (customException $e)
        {
        	echo customException::cleanUp($e);
        	die();
        }
    }//function

    public function getRandom()
    {   try{
            $res = $this->getAll();
            if(!empty($res))
            {
        	   $rand_keys = array_rand($res,1);
			 return $res[$rand_keys];
            }
            else {
        	   return NULL;
            }
        }catch (customException $e)
        {
        	echo customException::cleanUp($e);
        	die();
        }
    }


} //end of class
?>