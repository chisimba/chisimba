<?php

/**
 * Class used to search for users and add them to the groups
 * 
 * PHP version 5
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  Chisimba
 * @package   contextgroups
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id: onlinecount_class_inc.php 2835 2007-08-06 12:32:22Z paulscott $
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
 * Class used to search for users and add them to the groups
 * 
 * @category  Chisimba
 * @package   contextgroups
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class contextusers extends dbTable
{
    /**
     * @var string $sqlComplete SQL Query that gets generated and reused in many functions
     */
    private $sqlComplete="";
    
    /**
    * Constructor
    */
    public function init()
    {
        parent::init('tbl_users');
        $this->objLanguage = $this->getObject('language', 'language');
        
        //Ehb-added-begin
            $this->objGroups = $this->getObject('groupAdminModel', 'groupadmin');
            $this->objContext = $this->getObject('dbcontext', 'context');
            $this->sqlComplete="";
        //Ehb-added-end
    }
    
    /**
    * Method to search for users
    * @param string $search Item to search for
    * @param string $field Column to search in
    * @param string $order Column to order results by
    * @param int $numResults Number of Results per page
    * @param int $page Current Page of Results
    * @return array Results
    */
    public function searchUsers($search, $field, $order, $numResults, $page=0,$context='all',$group='all')
    {
        //Ehb-added-begin
        if(($context=='all')&&($group=='all')){
            //Ehb-added-end
            $sql='SELECT SQL_CALC_FOUND_ROWS* 
FROM tbl_users WHERE '.$field.' LIKE "'.$search.'%" GROUP BY userId ORDER BY '.$order;
        } else if(($context!='all')&&($group!='all')){
            
            //Ehb-added-begin
            $gid=$this->objGroups->getLeafId(array($context,$group));
                $sql="SELECT SQL_CALC_FOUND_ROWS DISTINCT userId,title,firstName,surname,emailAddress,sex ";
                $sql.="FROM tbl_groupadmin_groupuser INNER JOIN tbl_users "; 
                $sql.="ON ( ( user_id = tbl_users.id ) AND ( group_id = '$gid' ) ) WHERE $field LIKE '$search%' ORDER BY $order";
                    
        } else if(($context!='all')&&($group=='all')) {
            $gids=array();
                $gids[0]=$this->objGroups->getLeafId(array($context,'Guest'));
                $gids[1]=$this->objGroups->getLeafId(array($context,'Lecturers'));
                $gids[2]=$this->objGroups->getLeafId(array($context,'Site Admin'));
                $gids[3]=$this->objGroups->getLeafId(array($context,'Students'));
                $sql="SELECT SQL_CALC_FOUND_ROWS DISTINCT userId,title,firstName,surname,emailAddress,sex ";
                $sql.="FROM tbl_groupadmin_groupuser INNER JOIN tbl_users "; 
                $sql.="ON ( ( user_id = tbl_users.id ) AND ( (group_id = '$gids[0]') OR (group_id = '$gids[1]') OR (group_id = '$gids[2]') OR (group_id = '$gids[3]') ) ) WHERE $field LIKE '$search%' ORDER BY $order";
                    
        } else if(($context=='all')&&($group!='all')) {
            $contexts=$this->objContext->getListOfContext();
            $length=count($contexts);
            
            
            $sqlGroups="";
            
            for($i2=0;$i2 < $length ;$i2++){
            
                        
                        $sqlGroups.="group_id='".$this->objGroups->getLeafId(array($contexts[$i2]['contextCode'],$group))."'"." ";
                        if($i2<$length-1){
                            $sqlGroups.=" OR ";
                    }
                }
            
            
            $sql="SELECT SQL_CALC_FOUND_ROWS  DISTINCT userId,title,firstName,surname,emailAddress,sex ";
            $sql.="FROM tbl_groupadmin_groupuser INNER JOIN tbl_users "; 
            $sql.="ON ( ( user_id = tbl_users.id ) AND ( $sqlGroups) ) WHERE $field LIKE '$search%' ORDER BY $order"; 
        }
        
        //Ehb-added-end
                
        $this->sqlComplete=$sql;
        
        if ($numResults != 'all') {
            if ($page < 0) {
                $page = 0;
            }
            $page = $page * $numResults;
            $sql .= ' LIMIT '.$page.', '.$numResults;
        }
        
        return $this->getArray($sql);
    }
    
    
    
    /**
    * Method to get the number of total results for a search
    * @param string $search Item to search for
    * @param string $field Column to search in
    * @return int Number of Results
    */
    public function countResults()
    {
        /*$where = 'WHERE '.$field.' LIKE "'.$search.'%"';
            return $this->getRecordCount($where);
        */
        
        return count($this->getArray($this->sqlComplete));
    }
    
    /**
    * Method to generate paging for the search results
    * @param string $search Item to search for
    * @param string $field Column to search in
    * @param string $order Column to order results by
    * @param int $numResults Number of Results per page
    * @param int $page Current Page of Results
    * @return array Results
    */
    public function generatePaging($search, $field, $order, $numResults, $page,$module = null)
    {
     
        if ($numResults == 'all') {
            $output = $this->objLanguage->languageText('word_page').' 1';
        } else {
            
            $countResults = $this->countResults();
            
            $output = '';
            $divider = '';
            
            $this->loadClass('link', 'htmlelements');
            
            for ($i = 1; $i<=(($countResults - ($countResults%$numResults)) / $numResults); $i++)
            {
                if ($i == $page+1) {
                    $output .= $divider.'<em>'.$this->objLanguage->languageText('mod_toolbar_page', 'toolbar', 'Page').' '.$i.'</em>';
                } else {
                    $link = new link ($this->uri(array('action'=>'viewsearchresults', 'page'=>$i),$module));
                    
                    $link->link = $this->objLanguage->languageText('word_page').' '.$i;
                    
                    $output .= $divider.$link->show();
                }
                
                $divider = ' | ';
            }
            
            if ($countResults%$numResults != 0) {
                $count = ($countResults - ($countResults%$numResults)) / $numResults + 1;
                
                if ($count == $page+1) {
                    $output .= $divider.'<em>'.$this->objLanguage->languageText('mod_toolbar_page', 'toolbar', 'Page').' '.$count.'</em>';
                } else {
                    $link = new link ($this->uri(array('action'=>'viewsearchresults', 'page'=>$count),$module));
                    $link->link = $this->objLanguage->languageText('word_page').' '.$count;
                    
                    $output .= $divider.$link->show();
                }
            }
        
        }
        
        return $output;
    }
}

?>