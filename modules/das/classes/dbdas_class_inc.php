<?php
/**
 * message IM dbtable derived class
 *
 * Class to interact with the database for the popularity contest module
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
 * @category  chisimba
 * @package   im
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright 2008 Paul Scott
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
 * @link      http://avoir.uwc.ac.za
 * @see       api
 */
class dbdas extends dbTable
{

    /**
     * Constructor
     *
     */
    public function init()
    {
        parent::init('tbl_das_messagesarchive');
        //$this->objPresence = $this->getObject('dbimpresence','im');
    }

    /**
     * Public method to insert a record to the popularity contest table as a log.
     *
     * This method takes the IP and module_name and inserts the record with a timestamp for temporal analysis.
     *
     * @param array $recarr
     * @return string $id
     */
    public function addRecord($pl)
    {
        $userSplit = explode('/', $pl['from']);
        $userSplit2 = explode("/", $userSplit[0]);
        $times = $this->now();
        $recarr['datesent'] = $times;
        $recarr['msgtype'] = $pl['type'];
        $recarr['msgfrom'] = $userSplit2[0];
        $recarr['msgbody'] = $pl['body'];
        // Check for empty messages
        //var_dump($rearr);
        if($recarr['msgbody'] == "")
        {
            return;
        }
        else {
            return $this->insert($recarr, 'tbl_im');
        }
    }

    public function updateReply($msgId)
    {
        return $this->update('id', $msgId, array('msg_returned' => '1'));
    }

    public function getRange($start, $num)
    {
        $range = $this->getAll("ORDER BY datesent ASC LIMIT {$start}, {$num}");
        return array_reverse($range);
    }

    /**
     *Method to get the a user post
     *@param  int $start
     *@param int $max
     *@access public
     *@return array
     */
    public function getMessagesByActiveUser($userId)
    {
        $bigArr = array();
        $usersArr = $this->objPresence->getAllActiveUsers($userId);

        foreach($usersArr as $activeUser)
        {
            //get all messages for the user
            $activeUser['messages'] = $this->getPersonMessages($activeUser['person']);
            //add only if the user has messages
            if(count($activeUser['messages']) > 0)
            {
                array_push($bigArr, $activeUser);
            }
        }

        return $bigArr;
    }

    /**
     *Method to get all the users messages
     *@param string $person
     *@return array
     */
    public function getPersonMessages($person)
    {
        $ret = $this->getAll("WHERE msgfrom = '$person' ORDER BY datesent DESC LIMIT 10");
	
	return $ret;

    }
    
    /**
     * Method to search all messages
     * @param string $keyword
     * @return array
     */
    public function searchMessages($keyword)
    {
        $ret = $this->getAll("WHERE msgbody LIKE '%%$keyword%%' OR msgfrom LIKE '%%$keyword%%' ORDER BY datesent DESC LIMIT 50");
	    return $ret;
    }

    /**
     *Method save a reply message
     *@param string msgId
     *@param string $rplytext
     *
     */
    public function saveReply($msgId, $replytext)
    {
        $rec = $this->getAll("where id = '$msgId'");
        $rec = $rec[0];
        
        $fields = array('msgtype' => $rec['msgtype'],
                        'msgfrom' => $rec['msgfrom'],
                        'parentid' =>  $msgId,
                        'msgbody' => $replytext,
                        'msg_returned' => 'TRUE',
                        'datesent' => $this->now());
        //update presence table
        $this->update('person',$rec['msgfrom'] , array('datesent' => $this->now()), 'tbl_im_presence');
        return $this->insert($fields, 'tbl_im');
    }

    /**
     *Method to get a reply message
     *@param string $msgId
     *@return array
     */
    public function getReplies($msgId)
    {
        if($this->valueExists('parentid', $msgId))
        {
            return FALSE;
        } else {
            return $this->getAll("WHERE parentid = '$msgId'");
        }
    }

    /**
     *Method to get the archived messages
     *@param string personId
     *@return array
     */
    public function getArchivedMessages($personId)
    {
        return   $this->getAll("WHERE msgfrom='$personId' ORDER BY datesent DESC");        
    }

}
?>
