<?php
  /**
   * Class utility extends object.
   * @package contextstats
   * @filesource utility_class_inc.php
   */
  
  // security check - must be included in all scripts
  if (!$GLOBALS['kewl_entry_point_run']) {
      die("You cannot view this page directly");
  }
  
  /**
   * Utility Menu Class
   *
   * @author Qhamani Fenama <qfenama@uwc.ac.za>
   * @copyright (c) 2010 University of the Western Cape
   * @package contextstats
   * @version 1
   */
  class utility extends object
  {
      public $wsdl;
      /**
       * Constructor method to instantiate objects and get variables
       */
      public function init()
      {
          $this->objLanguage = $this->getObject('language', 'language');
          $this->moduleCheck = $this->newObject('modules', 'modulecatalogue');
          $this->objUser = $this->getObject('user', 'security');
          $this->globalTable = $this->newObject('htmltable', 'htmlelements');
          $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
          require_once('nusoap.php');
      }
      
      /**
       * This method returns the finished menu
       *
       * @return string $menu - the finished menu
       */
      public function getFaculty($contextcode, $year)
      {
          $wsdl = $this->objSysConfig->getValue('WSDL', 'contextstats');
          $client = new nusoap_client($wsdl, 'wsdl');
          
          //Get Context data
          $param = array('Module' => $contextcode, 'Year' => $year);
          $params = array('Get_ModuleRequest' => new soapval('', '', $param, false, ''));
          $modresult = $client->call('Get_Module', $params, '', '');
          
          $facultycde = $modresult['Get_ModuleResult']['Faculty'];
          $departmentcde = $modresult['Get_ModuleResult']['Departmnt'];
          
          //Get Faculty data
          $param = array('Faculty' => $facultycde);
          $params = array('Get_FacultyRequest' => new soapval('', '', $param, false, ''));
          $facresult = $client->call('Get_Factulty', $params, '', '');
          $faculty = $facresult['Get_FacultyResult']['Name'];
          
          //Return the faculty and dept
          $result = array('faculty' => $faculty);
          return $result;
      }
      
      public function getDeptartment($contextcode, $year = '')
      {
          $wsdl = $this->objSysConfig->getValue('WSDL', 'contextstats');
          $client = new nusoap_client($wsdl, 'wsdl');
          //Get Context data
          $param = array('Module' => $contextcode, 'Year' => $year);
          $params = array('Get_ModuleRequest' => new soapval('', '', $param, false, ''));
          $modresult = $client->call('Get_Module', $params, '', '');
          
          $facultycde = $modresult['Get_ModuleResult']['Faculty'];
          $departmentcde = $modresult['Get_ModuleResult']['Departmnt'];
          
          //Get Department data
          $param = array('Faculty' => $facultycde, 'Department' => $departmentcde);
          $params = array('Get_DepartmentRequest' => new soapval('', '', $param, false, ''));
          $deptresult = $client->call('Get_Department', $params, '', '');
          $department = $deptresult['Get_DepartmentResult']['DeptName'];
          
          //Return the faculty and dept
          $result = array('department' => $department);
          return $result;
      }
  }
?>
