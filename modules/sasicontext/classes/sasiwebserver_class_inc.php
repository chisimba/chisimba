<?php

/**
 * Sasiwebserver class
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
 * @package   sasicontext
 * @author    Qhamani Fenama <qfenama@gmail.com>
 * @copyright 2010 Qhamani Fenama
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link      http://avoir.uwc.ac.za
 */
class sasiwebserver extends object {
    /**
     * WSDL object
     *
     * @var wsdl
     */
    public $wsdl = null;

    /**
     * Constructor method to instantiate objects and get variables
     */
    public function init() {
        try {
            $this->objLanguage      = $this->getObject('language', 'language');
            $this->objUser          = $this->getObject('user', 'security');
            $this->dbsasicontext    = $this->getObject('dbsasicontext', 'sasicontext');
            $this->objSysConfig     = $this->getObject('dbsysconfig', 'sysconfig');
            $this->wsdlenable       = $this->objSysConfig->getValue('ENABLE_SASIWS', 'sasicontext');
            $this->wsdl             = $this->objSysConfig->getValue('WSDL', 'sasicontext');
            $this->currentYear      = date('Y', time());
            require_once ('nusoap.php');
        } catch (customException $e) {
            echo customException::cleanUp();
            die();
        }
    }

    /*
     * Method that get all the list of faculties is the SASI webserver
     *
     * @access public
     * @return array @arr
    */
    public function getFaculties() {
        $param = array('FacultyLst' => '*');
        $data = $this->getData('Browse_Faculty', $param);
        $simpledata = $data['Browse_FacultyResult']['FacultyLst_List']['row'];
        $arr = array();
        foreach ($simpledata as $smdata) {
            $arr2 = array();
            $arr2['id'] = $smdata['column'][0];
            $arr2['title'] = $smdata['column'][1];
            $arr[] = $arr2;
        }
        return $arr;
    }

    /*
     * Method that get the list of department is the SASI webserver
     *
     * @access public
     * @param string $faculty
     * @return array @arr
    */
    public function getDepartments($faculty) {
        $param = array('Faculty' => $faculty, 'Year' => date('Y'), 'Active' => 'y');
        $data = $this->getData('Browse_Department', $param);
        $simpledata = $data['Browse_DepartmentResult']['Department_List']['row'];
        $arr = array();
        foreach ($simpledata as $smdata) {
            $arr2 = array();
            $arr2['id'] = $smdata['column'][0];
            $arr2['title'] = $smdata['column'][1];
            $arr[] = $arr2;
        }
        return $arr;
    }

    /*
     * Method that get the list of subject is the SASI webserver
     *
     * @access public
     * @param string $faculty
     * @param string $dept
     * @return array @arr
    */
    public function getModules($faculty, $dept) {
        $param = array('Faculty' => $faculty, 'Department' => $dept, 'Year' => date('Y'), 'Active' => 'y');
        $data = $this->getData('Browse_Module', $param);
        $simpledata = $data['Browse_ModuleResult']['ModLst_List']['row'];
        $arr = array();
        if(empty($simpledata[0])) {
            return false;
        }

        foreach ($simpledata as $smdata) {
            $arr2 = array();
            $arr2['id'] = $smdata['column'][0];
            $arr2['title'] = $smdata['column'][1];
            $arr[] = $arr2;
        }
        return $arr;
    }

    /*
     * Method that get data from the SASI webserver
     *
     * @access public
     * @param string $method
     * @param array $param
     * @return array $results
    */
    public function getData($method, $param) {
        $client = new nusoap_client($this->wsdl, 'wsdl');
        $params = array($method . 'Request' => new soapval('', '', $param, false, ''));
        $result = $client->call($method, $params, '', '');
        return $result;
    }


    /*
     * Method that get link SASI webserver and content code
     *
     * @access public
     * @param string $contextCode
     * @param string $faculty
     * @param string $department
     * @param string $sasiCode
     * @return boolean
    */
    public function addData($contextcode, $faculty, $department, $subject) {
        //check if it linkup doesn't already exists
        $sasidata = $this->dbsasicontext->getSasicontextByField('contextcode', $contextcode);

        //GET faculty title
        $facultytitle = $this->getFacultyName($faculty);

        //GET department title
        $departmenttitle = $this->getDeptName($faculty, $department);

        //GET subject title
        $subjecttitle = $this->getSubjectName($subject);

        if(!$sasidata) {
            if($this->dbsasicontext->addSasicontext($contextcode, $faculty, $facultytitle, $department, $departmenttitle, $subject, $subjecttitle)) {
                return true;
            }
        }
        else {
            $this->dbsasicontext->updateSasicontext($sasidata['id'], $contextcode, $faculty, $facultytitle, $department, $departmenttitle, $subject, $subjecttitle);
            return true;
        }
        return false;
    }

    /*
     * Method that get the faculty name
     *
     * @param string $faculty
     * @return string facultyname
    */
    public function getFacultyName($faculty) {

        //GET faculty title
        $param = array('Faculty' => $faculty);
        $data = $this->getData('Get_Faculty', $param);
        return $data['Get_FacultyResult']['Name'];

    }

    /*
     * Method that get the department name
     *
     * @param string $faculty
     * @param string $department
     * @return string departmentname
    */
    public function getDeptName($faculty, $department) {

        //GET department title
        $param = array('Faculty' => $faculty, 'Department' => $department);
        $data = $this->getData('Get_Department', $param);
        return $data['Get_DepartmentResult']['DeptName'];

    }

    /*
     * Method that get the department name
     *
     * @param string $faculty
     * @param string $department
     * @return string departmentname
    */
    public function getSubjectName($subject) {

        //GET subject title
        $param = array('Module' => $subject, 'Year' => date('Y'));
        $data = $this->getData('Get_Module', $param);
        return $data['Get_ModuleResult']['ModDsc'];

    }

    /*
     * Method that build the section menu
     *
     * @access public
     * @param array $data
     * @return string
    */
    public function buildLinks($data) {
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $starturi = $this->uri(array('action'=>'showfac'), 'sasicontext');
        if($data == 'faculty') {
            $simpledata = $this->getFaculties();
            $arr = '<h2>'.$this->objLanguage->code2Txt("mod_sasicontext_selectfaculty", "sasicontext").':</h2>';
            $dropdown = new dropdown('fac');
            $dropdown->size =  7;
            $dropdown->extra = ' ondblclick="javascript: var str = document.getElementById(\'input_fac\').value;
							loadData(\''.$this->uri(array('action' => 'showfac',  'get' => 'dept')).'&data='.'\'+str);"';
            foreach($simpledata as $smdata) {
                $dropdown->addOption($smdata['id'], '['. $smdata['id'].'] - '. $smdata['title']);
            }
            $dropdown->selected = $simpledata[0]['id'];
            return $arr.$dropdown->show();
        }

        if($data == 'dept') {
            $fac = $this->getParam('data');
            $simpledata = $this->getDepartments($fac);
            $arr = '<h2>'.$this->objLanguage->code2Txt("mod_sasicontext_selectdept", "sasicontext").':</h2>';
            $dropdown = new dropdown('dept');
            $dropdown->size =  7;
            $dropdown->extra = ' ondblclick="javascript: var str = document.getElementById(\'input_dept\').value;
							loadData(\''.$this->uri(array('action' => 'showfac', 'get' => 'mod', 'faculty' => $fac)).'&data='.'\'+str);"';
            foreach($simpledata as $smdata) {
                $dropdown->addOption($smdata['id'], '['. $smdata['id'].'] - '. $smdata['title']);
            }
            $dropdown->selected = $simpledata[0]['id'];
            $link = new link ("#");
            $link->link = 'Start Over';
            $link->rel = 'facebox';
            $link->href = 'javascript:loadData(\''.$starturi.'\')';

            $link2 = $this->getFacultyName($fac);

            $str = $link->show().'  >>  '.$link2;
            return $str.$arr.$dropdown->show();
        }
        if($data == 'mod') {
            $faculty = $this->getParam('faculty');
            $dept  = $this->getParam('data');
            $simpledata = $this->getModules($faculty, $dept);
            $arr = '<h2>'.$this->objLanguage->code2Txt("mod_sasicontext_selectsubject", "sasicontext").':</h2>';
            $dropdown = new dropdown('mod');
            $dropdown->size =  7;

            $objButton = new button('select', 'Done');
            $objButton->setToSubmit();
            $link = new link ('javascript:loadData(\''.$starturi.'\')');
            $link->link = 'Start Over';
            $link->rel = 'facebox';

            $link2 = new link ('#');
            $link2->link = $this->getFacultyName($faculty);
            $facuri = $this->uri(array('action' => 'showfac',  'get' => 'dept', 'data' => $faculty));
            $link2->href = 'javascript: loadData(\''.$facuri.'\')';
            $link2->rel = 'facebox';

            $link3 = $this->getDeptName($faculty, $dept);

            $str = $link->show().'  >>  '.$link2->show().'  >>  '.$link3;
            $this->loadClass('form', 'htmlelements');
            $submitform = new form('linkcourse', $this->uri(array('action' => 'adddata', 'dept' => $dept, 'faculty' => $faculty), 'sasicontext'));
            $submitform->addToForm($str);

            if(!$simpledata) {
                $submitform->addToForm('<p>'.$this->objLanguage->code2Txt("mod_sasicontext_nosubject", "sasicontext").'</p>');
                return $submitform->show();
            }
            foreach($simpledata as $smdata) {
                $dropdown->addOption($smdata['id'], '['. $smdata['id'].'] - '. $smdata['title']);
            }
            $dropdown->selected = $simpledata[0]['id'];
            $submitform->addToForm($arr);
            $submitform->addToForm($dropdown->show());
            $submitform->addToForm('<br/>');
            $submitform->addToForm($objButton->show());

            return $submitform->show();
        }
    }
}
?>
