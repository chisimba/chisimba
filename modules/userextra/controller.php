<?php

/**
 *
 *  PHP version 5
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
 * @author    david wafula
 *
  =
 */
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

// end security check

class userextra extends controller {

    function init() {
        $this->objDBContext = $this->getObject('dbcontext', 'context');
        $this->units = $this->getObject('dbunits');
        $this->objUser = $this->getObject("user", 'security');
        $this->objGroups = $this->getObject('groupadminmodel', 'groupadmin');
        $this->dbextra = $this->getObject('dbuserextra');
        $this->userinfoblock = $this->getObject('block_userinfo');
        $this->objAltConfig=$this->getObject("altconfig","config");
        $this->objLanguage = $this->getObject("language", "language");
    }

    /**
     * Standard Dispatch Function for Controller
     * @param <type> $action
     * @return <type>
     */
    public function dispatch($action) {
        if($action != 'activate'){
        if (!$this->objDBContext->isInContext()) {
            return "needtojoin_tpl.php";
        }}
        
        /*
         * Convert the action into a method (alternative to
         * using case selections)
         */
        $method = $this->getMethod($action);
        /*
         * Return the template determined by the method resulting
         * from action
         */
        return $this->$method();
    }

    /**
     *
     * Method to convert the action parameter into the name of
     * a method of this class.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return string the name of the method
     *
     */
    function getMethod(& $action) {
        if ($this->validAction($action)) {
            return '__' . $action;
        } else {
            return '__home';
        }
    }

    /**
     *
     * Method to check if a given action is a valid method
     * of this class preceded by double underscore (__). If it __action
     * is not a valid method it returns FALSE, if it is a valid method
     * of this class it returns TRUE.
     *
     * @access private
     * @param string $action The action parameter passed byref
     * @return boolean TRUE|FALSE
     *
     */
    function validAction(& $action) {
        if (method_exists($this, '__' . $action)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Method to show the Home Page of the Module
     */
    public function __home() {
        return "upload_tpl.php";
    }

    function __importusers() {
        $util = $this->getObject('userextrautils');
        $path =$this->objAltConfig->getSiteRootPath().'/usrfiles/'. $this->getParam('path');
        $coursecode = $this->getParam("coursecode");

        if($util->importStudents($path, $coursecode) == 'success'){
            return $this->nextAction('controlpanel', array(),"context");
        }else{
            echo "X";
        }
    }

    public function __activate() {
        $this->userinfoblock->activate();
        $this->nextAction('home', NULL, '_default');
    }

    public function __getUnits() {
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $unitsurl = $objSysConfig->getValue('UNITSURL', 'userextra');
        $susername = $objSysConfig->getValue('SUSERNAME', 'userextra');
        $spassword = $objSysConfig->getValue('SPASSWORD', 'userextra');
        $unitcode = $this->getParam('unitcode');
        $unitsurl = "$unitsurl/$unitcode";
        $ch = curl_init($unitsurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, "$susername:$spassword");
        $r = curl_exec($ch);
        curl_close($ch);
        /* $firsttime=$this->getParam('firsttime');
          $start=$this->getParam('start');
          $limit=$this->getParam('limit');
          $totalCount=0; */
        echo $r;
    }

    public function __addlecturer() {
        if ($this->objUser->isAdmin()) {
            $username = $this->getParam('username');
            $groupid = $this->objGroups->getId('Lecturers');
            $puid = $this->dbextra->getUserPuidByUsername($username);
            $res = $this->objGroups->addGroupUser($groupid, $puid);
            $this->nextAction('home', NULL, '_default');
        } else {
            echo 'This function can only be used by site admin';
            die();
        }
    }

    function __uploadfile() {
        $objFileUpload = $this->getObject('uploadinput', 'filemanager');
        $objFileUpload->enableOverwriteIncrement = TRUE;
        $results = $objFileUpload->handleUpload('fileupload');

        // Technically, FALSE can never be returned, this is just a precaution
        // FALSE means there is no fileinput with that name
        if ($results == FALSE) {
            $this->setVarByRef("message", "Unable to upload");
        } else {

            // If successfully Uploaded
            if ($results['success']) {
                $coursecode = $this->objDBContext->getContextCode();
                return $this->nextAction('importusers', array('path' => $results['path'], "coursecode" => $coursecode));
            } else {
                // If not successfully uploaded
                $this->setVarByRef("message", $results['reason']);
            }
        }
        return "result_tpl.php";
    }

    public function __getremoteuserinformation() {
        
    }

}