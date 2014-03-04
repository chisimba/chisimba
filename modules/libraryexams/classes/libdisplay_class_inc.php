<?php
    // security check - must be included in all scripts
    if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
    }
    // end security check

/**
 * This object holds all the template display forms
 *
 * @package cmsadmin
 * @category chisimba
 * @copyright AVOIR
 * @license GNU GPL
 * @author Charl Mert
 */

    class libdisplay extends object
    {
      /**
        * The Skin object
        *
        * @access private
        * @var object
        */
        protected $objSkin;

      /**
        * The User object
        *
        * @access private
        * @var object
        */
        protected $_objUser;

      /**
        * The user model
        *
        * @access private
        * @var object
        */
        protected $_objUserModel;

      /**
        * The config object
        *
        * @access private
        * @var object
        */
        protected $_objConfig;

      /**
        * The blocks object
        *
        * @access private
        * @var object
        */
        protected $_objBlocks;

      /**
        * Feature box object
        *
        * @var object
        */
        public $objFeatureBox;

      /**
        * Class Constructor
        *
        * @access public
        * @return void
        */
        public function init()
        {
            try {
                $this->_objConfig =$this->newObject('altconfig', 'config');
                $this->_objSysConfig =$this->newObject('dbsysconfig', 'sysconfig');
                $this->objSkin =$this->newObject('skin', 'skin');
                $this->_objUser =$this->newObject('user', 'security');
                $this->_objLanguage =$this->newObject('language', 'language');
                $this->objFeatureBox = $this->newObject('featurebox', 'navigation');
                $this->objModule=&$this->getObject('modules','modulecatalogue');
                $this->objDateTime = $this->getObject('dateandtime', 'utilities');

                $this->loadClass('textinput', 'htmlelements');
                $this->loadClass('checkbox', 'htmlelements');
                $this->loadClass('radio', 'htmlelements');
                $this->loadClass('dropdown', 'htmlelements');
                $this->loadClass('form', 'htmlelements');
                $this->loadClass('button', 'htmlelements');
                $this->loadClass('link', 'htmlelements');
                $this->loadClass('label', 'htmlelements');
                $this->loadClass('hiddeninput', 'htmlelements');
                $this->loadClass('textarea','htmlelements');
                $this->loadClass('htmltable','htmlelements');
                $this->loadClass('layer', 'htmlelements');

            } catch (Exception $e){
                throw customException($e->getMessage());
                exit();
            }
        }

       
      /**
        * Gets the main display template
        *
        * @access public
        * @return Display Template Contents
        */
        public function getMainTemplate() {

            //initiate objects
            $table =  $this->newObject('htmltable', 'htmlelements');
            $objH = $this->newObject('htmlheading', 'htmlelements');
            $objH3 = $this->newObject('htmlheading', 'htmlelements');
            $link =  $this->newObject('link', 'htmlelements');
            $objIcon =  $this->newObject('geticon', 'htmlelements');
            $this->loadClass('form', 'htmlelements');
            $objLayer =$this->newObject('layer','htmlelements');
            $this->loadClass('dropdown', 'htmlelements');
            $this->loadClass('textinput', 'htmlelements');
            $this->loadClass('checkbox', 'htmlelements');
            $this->loadClass('button', 'htmlelements');
            $this->loadClass('htmltable', 'htmlelements');

			$result = '';
			
            $lblHeading = $this->_objLanguage->languageText('mod_libraryexams_header', 'libraryexams');

            $tbl = $this->newObject('htmltable', 'htmlelements');
            $tbl->cellpadding = 3;
            $tbl->align = "left";

            //create a heading
            $objH->type = '1';

            //counter for records
            $cnt = 1;
            //Heading box
            $objIcon->setIcon('user', 'png', 'icons/cms/');
            $objIcon->title = $lblHeading;
            $objH->str =  $objIcon->show().'&nbsp;'. $lblHeading;

            $hdr = $objH->show();
            
            $objH3->type = '3';
            $objH3->str = $lblHeading;
            $hdr .= $objH3->show();

            $tbl->startRow();
            $tbl->addCell($hdr, '', 'center');
            $tbl->endRow();

            $objLayer->str = $objH->show();
            $objLayer->id = 'cms_header_left';
            $header = $objLayer->show();

            $objLayer->str = $topNav;
            $objLayer->id = 'cms_header_right';
            $header .= $objLayer->show();
			
            $objLayer->str = '';
            $objLayer->cssClass = 'clearboth';
            $headShow = $objLayer->show();
			
            $objLayer->str = '&nbsp;';
            $objLayer->id = 'cmsvspacer';
            $vspacer = $objLayer->show();
			
            $result .= $header.$headShow.$vspacer;//$tbl->show());
			
            $frm_select = new form('select', $this->uri(array('action' => 'select'), 'cmsadmin'));
            $frm_select->id = 'select';
            $frm_select->addToForm($table->show());
			
            $result .= "<hr />";
            $result .= $frm_select->show();
            $result .= '&nbsp;'.'<br/>';
            $result .= "<hr />";
        
            return $result;
        }

    }

?>
