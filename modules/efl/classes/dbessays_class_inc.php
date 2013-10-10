<?php

/**
 * This class provides functionality to access the essays table
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

 * @author
 * @copyright  2009 AVOIR
 */
// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

class dbessays extends dbtable {
    function init() {
        $this->objUser=$this->getObject('user','security');
         parent::init('tbl_efl_proposedessaytopics');
    }

    public function addEssay($title,$content,$contextcode,$active,$multisubmit){

        $data = array(
            'title'=>$title,
            'userid' => $this->objUser->userId(),
            'content' => $content,
            'contextcode' => $contextcode,
            'active' => $active,
            'multiplesubmit' => $multisubmit,
           
        );

        $essayTopicId = $this->insert($data);
        return $essayTopicId;
    }

    function getEssays() {
        $data=$this->getAll();
        return $data;
    }
    
    function getSubmittedEssays($essayId, $user=null) {
        if($user == null) {
            $sql = "select * from tbl_efl_submittedessays where essayid = '".$essayId."'";
        }
        else {
            $sql = "select * from tbl_efl_submittedessays where userid= $user";
        }
        return $this->getArray($sql);

    }

    function getTitle($essayid) {
        $sql = "select title from tbl_efl_proposedessaytopics where id = '".$essayid."'";

        return $this->getArray($sql);
       }

    function getEssayContent($storyid) {
        $sql="select content from tbl_efl_proposedessaytopics where id= '".$storyid."'";

        return $this->getArray($sql);
    }
}
?>