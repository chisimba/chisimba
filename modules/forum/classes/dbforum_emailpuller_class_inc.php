<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of db_forum_emailpuller
 *
 * @author monwabisi
 */
class dbforum_emailpuller extends dbtable {
        //put your code here
        function init(){
                parent::init('tbl_forum_mailjobs');
        }
}

?>
