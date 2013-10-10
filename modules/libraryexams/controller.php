<?php
    // security check - must be included in all scripts
    if (!$GLOBALS['kewl_entry_point_run'])
    {
        die("You cannot view this page directly");
    }
    // end security check

/**
 * The controller for the libraryexams module
 *
 * @package libraryexams
 * @category chisimba
 * @copyright AVOIR
 * @license GNU GPL
 * @author Charl Mert
 * @author Bravismo
 */

    class libraryexams extends controller
    {
        /**
         * The user object
         *
         * @access private
         * @var object
         */
        protected $_objUser;

        /**
         * The config object
         *
         * @access private
         * @var object
         */
        protected $_objConfig;

        /**
         * The language object
         *
         * @access private
         * @var object
         */
        public $objLanguage;

        /**
         * Class Constructor
         *
         * @access public
         * @return void
         */
        public function init()
        {
            try {       
                $this->_objDisplay =  $this->newObject('libdisplay', 'libraryexams');


                $this->_objCourseUnits =  $this->newObject('dbcourseunits', 'libraryexams');
                $this->_objDept =  $this->newObject('dbdepartments', 'libraryexams');
                $this->_objFaculty =  $this->newObject('dbfaculty', 'libraryexams');
                $this->_objPaper =  $this->newObject('dbpaper', 'libraryexams');
				
                $this->_objUser =  $this->newObject('user', 'security');
                $this->_objLanguage =  $this->newObject('language', 'language');
				
                //Get the activity logger class and log this module call
                $objLog = $this->getObject('logactivity', 'logger');
                $objLog->log();

                //Common Styles
                $this->appendArrayVar('headerParams', '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('_common.css'.'">'));

                //Common Styles IE <6 Fixes
				$loadie6 = '
				<!--[if lte IE 6]>
					<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('_common_ie6.css').'">
				<![endif]-->
				';
                $this->appendArrayVar('headerParams', $loadie6);

                //Common Styles IE 7> Fixes
				$loadie7 = '
				<!--[if gte IE 7]>
					<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('_common_ie7.css').'">
				<![endif]-->
				';
                $this->appendArrayVar('headerParams', $loadie7);

            } catch (customException $e){
                throw customException($e->getMessage());
                exit();
            }	    
        }

        /**
        *
        * This is a method that overrides the parent class to stipulate whether
        * the current module requires login. Having it set to false gives public
        * access to this module including all its actions.
        *
        * @access public
        * @return bool FALSE
        */
        public function requiresLogin()
        {

			//Will code login for granting access to UWC Lan User only
			$action = $this->getParam('action', '');
			switch ($action) {
				case 'ajaxforms':
					return FALSE;
				break;
                
                case 'flagcontent':
                    return FALSE;
                break;
                
				default :
					return TRUE;
				break;
			}
        }
		
		
       /**
        * Method to handle actions from templates
        * 
        * @access public
        * @param string $action Action to be performed
        * @return mixed Name of template to be viewed or function to call
        */
        public function dispatch()
        {
            $action = $this->getParam('action');
            $this->setLayoutTemplate('default_layout_tpl.php');
            $myid = $this->_objUser->userId();

            //The security class handles this now
            /*
            if (!($this->_objUser->inAdminGroup($myid,'CMSAuthors')) && !($this->_objUser->inAdminGroup($myid,'Site Admin'))) {
                            return 'cms_nopermissions_tpl.php';
            }
            */

            switch ($action) {

                default:
				
                return 'main_tpl.php';

				
				/* //Next Action Sample
                case 'deleteuserperm':
                    $id = $this->getParam('id');
                    $this->_objUserPerm->deleteRecord($id);

                return $this->nextAction('permissionsuser', array(NULL), 'libraryexams');
				*/
				
				/* Fixed Template Sample
				case 'flag':
                    $topNav = $this->_objUtils->topNav('flag');
                    $arrFlagOptions = $this->_objFlagOptions->getOptions();
                    $arrEmail = $this->_objFlagEmail->getAll();

                    if ($arrFlagOptions == FALSE) {
                        $arrFlagOptions = array();
                    }

                    if ($arrEmail == FALSE) {
                        $arrEmail = array();
                    }

                    $this->setVarByRef('topNav',$topNav);
                    $this->setVarByRef('arrFlagOptions', $arrFlagOptions);
                    $this->setVarByRef('arrEmail', $arrEmail);
					return 'cms_flag_list_tpl.php';
				break;
				*/
				
				/* Good Sample on How to capture data
                case 'createsection':
                    //Save the section
                    $parentId = $this->getParam('parent');
                    $title = $this->getParam('title');
                    $menuText = $this->getParam('menutext');
                    $access = $this->getParam('access');
                    $description = $this->getParam('introtext');
                    $published = $this->getParam('published');
                    $layout = $this->getParam('display');
                    $showIntroduction = $this->getParam('show_introduction');
                    $showTitle = $this->getParam('show_title');
                    $showAuthor = $this->getParam('show_author');
                    $showDate = $this->getParam('show_date');
                    $customNum = $this->getParam('customnumber');
                    $pageNum = $this->getParam('pagenum');
                    $pageOrder = $this->getParam('pageorder');
                    $imageUrl = '';
                    $contextCode = '';

                    
                    $this->_objSections->addSection($title,
                                       $parentId,
                                       $menuText,
                                       $access,
                                       $description,
                                       $published,
                                       $layout,
                                       $showIntroduction,
                                       $showTitle,
                                       $showAuthor,
                                       $showDate,
                                       $pageNum,
                                       $customNum,
                                       $pageOrder,
                                       $imageUrl,
                                       $contextCode);
                    
                    //$this->_objSections->add();

                    $parent = $this->getParam('parentid');
                    if (!empty($parent)) {
                        return $this->nextAction('viewsection', array('id' => $parent), 'libraryexams');
                    } else {
                        return $this->nextAction('sections');
                    }
				*/
    
            }

        }

    }

?>
