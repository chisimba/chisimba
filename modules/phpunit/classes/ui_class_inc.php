<?php
    // security check - must be included in all scripts
    if (!$GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
    }
    // end security check

/**
 * This object hold all the utility method that the cms modules might need
 *
 * @package cmsadmin
 * @category chisimba
 * @copyright AVOIR
 * @license GNU GPL
 * @author Charl Mert <charl.mert@gmail.com>
 */

    class ui extends object
    {
       /**
        * The context  object
        *
        * @access private
        * @var object
        */
        protected $_objContext;	

      /**
        * The inContextMode  object
        *
        * @access private
        * @var object
        */
        protected $inContextMode;	

      /**
        * The sections  object
        *
        * @access private
        * @var object
        */
        protected $_objSections;

      /**
        * The Content object
        *
        * @access private
        * @var object
        */
        protected $_objContent;

      /**
        * The Skin object
        *
        * @access private
        * @var object
        */
        protected $objSkin;

      /**
        * The Content Front Page object
        *
        * @access private
        * @var object
        */
        protected $objFrontPage;

      /**
        * The User object
        *
        * @access private
        * @var object
        */
        protected $objUser;

      /**
        * The user model
        *
        * @access private
        * @var object
        */
        protected $objUserModel;

      /**
        * Code Analyzer Object
        *
        * @var object
        */
        public $objCodeAnalyzer;

      /**
        * Class Constructor
        *
        * @access public
        * @return void
        */
        public function init()
        {
            try {
                $this->objLog =$this->getObject('dblog', 'phpunit');
                $this->objCodeAnalyzer = $this->getObject('codeanalyzer', 'phpunit');

                $this->setVar('jquery_boxy_theme', 'default');

                $this->objBox = $this->getObject('jqboxy', 'jquery');
                $this->jQuery =$this->getObject('jquery', 'jquery');
                $this->objConfig =$this->getObject('altconfig', 'config');
                $this->objLanguage =$this->getObject('language', 'language');

                //Live Query
                $this->jQuery->loadLiveQueryPlugin();
                $this->jQuery->loadFormPlugin();

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
                $this->loadClass('jqboxy', 'jquery');

            } catch (Exception $e){
                throw customException($e->getMessage());
                exit();
            }
        }


       /**
		* Method to return the show the Create Test Case Form
		*
		* @access public
		* @return HTML
		*/
        public function showCreateTestCaseForm()
        {
            $h3 = $this->newObject('htmlheading', 'htmlelements');
            $link =  $this->newObject('link', 'htmlelements');
            $objIcon =  $this->newObject('geticon', 'htmlelements');


$script = <<<EDITCALLBACK
<script type="text/javascript">
    //Method to display the list of classes to select for PHPUnit test case creation

	function phpuSelectAll() {
		jQuery('.phpuCheckFiles').attr('checked','');
	}

	function phpuSelectNone() {
		jQuery('.phpuCheckFiles').removeAttr('checked');
	}

	function showClassList(select_list, target_div) {

		//Getting the selected module name
		mySel = document.getElementById('input_' + select_list);
	    var module_name = mySel.options[mySel.selectedIndex].value;

		jQuery('#' + target_div).load('?module=phpunit&action=getform&type=classform&modulename=' + module_name);
	
		//Adjustmenmts to fit the current skin layout engine's layout requirements TABLES!!!
		jQuery('.class_list_container').attr('height', '500px'); //adjusting the teable cell
		
		/*
		jQuery.ajax({
			url: 'phpunit.html',
            method: 'POST',
            dataType: 'html',
            data: {},
            beforeSend: function() {
				jQuery('#' + target_div).attr('background', 'url(http://localhost/fresh/skins/_common/alertbox/loading.gif)');
            },
            success: function(data) {
				alert('doping it');
            },
            error: function(data) {
            },
            complete: function(data) {
				jQuery('#' + target_div).append(data);
			}
		});
		*/

	}

</script>
EDITCALLBACK;
$this->appendArrayVar('headerParams', $script);

			$title = 'Create PHPUnit Test Case';

            $objForm = new form('addunittestfrm', $this->uri(array('action' => $action, 'id' => $contentId, 'frontman' => $frontMan), 'cmsadmin'));
            //$objForm->setDisplayType(3);

			// Get all modules
			$objModAdmin = $this->getObject('modules','modulecatalogue');
			$modules = $objModAdmin->getAll('ORDER BY module_id');
			$ddbModules = new dropdown('mod');
			//$ddbModules->extra = ' onchange="document.frmMod.submit();"';
			foreach ( $modules as $aModule ) {
			    $ddbModules->addOption( $aModule['module_id'], $aModule['module_id'] );
			}
			$ddbModules->setSelected( $this->getVar('mod') );
			$ddbModules->addOnChange('showClassList(\'mod\', \'class_list\');');			

			// Select a module:
			$lblSelectModule = "Select a module :";
			$objLabel = new label( $lblSelectModule, 'input_module_name' );
		
			//Submit Button
			//$objButton = new button('Submit', 'submit');

			//$objButton->setValue('Submit');
			//$objButton->setOnClick('alert(\'An onclick Event\')');
			//$objButton->setToSubmit();

			$objForm->addToForm( '<H1>'.$title.' </H1>' );
			$objForm->addToForm('<input type="hidden" name="action" value="updatemoduletext">');
			$objForm->addToForm( '<P><h4>'.$objLabel->show().'&nbsp;'.$ddbModules->show(). '</h4></P>');

            //Add validation for title            
            //$errTitle = $this->objLanguage->languageText('mod_cmsadmin_entertitle', 'cmsadmin');
            //$objForm->addRule('title', $errTitle, 'required');
            //$objForm->addToForm($tableContainer->show());
            
            //$objForm->addToForm($txt_action);
            
            //$display = $objForm->show();

			//Current Display Framwork kicks this out of the 1 column layout unless it's in a TABLE!!!!
	        $table_list = new htmlTable();
            $table_list->width = "100%";
            $table_list->cellspacing = "0";
            $table_list->cellpadding = "0";
            $table_list->border = "0";
            $table_list->attributes = "align ='center'";


			//$display = '<form method="POST" action="?module=phpunit&action=generatecases" >';
			//$display .= '<H1>'.$title.' </H1>';
			//$display .= '<input type="hidden" name="action" value="updatemoduletext">';
			//$display .= '<P><h4>'.$objLabel->show().'&nbsp;'.$ddbModules->show(). '</h4></P>';


			$table_list->startRow();	
			$table_list->addCell($objForm->show());	
			$table_list->endRow();

			$objLayer = new layer('class_list');
			$objLayer->id = 'class_list';
			$objLayer->str = "";

			$table_list->startRow();	
			$table_list->addCell($objLayer->show(), '', 'top', '', 'class_list_container');	
			$table_list->endRow();



			$display = $table_list->show(); 

            return $display;
        }

   /**
    * Method to return the Mapping Form
    *
    * @param string $mapId The id of the mapping to be edited.Default NULL for adding new mapping
    * @access public
    */
        public function getClassListForm($moduleName = '')
        {

            $table = new htmlTable();
            $table->width = "100%";
            $table->cellspacing = "0";
            $table->cellpadding = "10";
            $table->border = "0";
            $table->attributes = "align ='center'";

            $tbl = new htmlTable();
            $tbl->width = "100%";
            $tbl->cellspacing = "0";
            $tbl->cellpadding = "10";
            $tbl->border = "0";
            $tbl->attributes = "align ='center'";

			$modulePath = $this->objConfig->getModulePath() . $moduleName . '/classes';

            if (!is_dir($modulePath)) {
				//Trying core modules if current not found
				$modulePath = $this->objConfig->getsiteRootPath() . "core_modules/" . $moduleName . '/classes';
			}

			// Traversing the module/classes directory to find classes that PHPUnit test cases will be created for
            if (is_dir($modulePath)) {

				$filesArr = $this->objCodeAnalyzer->getAllClasses($modulePath);

				$maxCols = 4;

				$columnCount = count($filesArr) / $maxCols;
				$counter = 1;

				$tbl->startRow();

				foreach($filesArr as $file=>$fpath) {

					$chk[$file] = new checkbox($file);
					$chk[$file]->cssClass = 'phpuCheckFiles';
					$chk[$file]->setChecked(TRUE);

					$tbl->addCell($chk[$file]->show());
					$tbl->addCell($file);

					if (($counter % $maxCols) == 0) {
						$tbl->endRow();
						$tbl->startRow();
					}

					$counter++;
				}
				$tbl->endRow();
            } else {
					//Error: Mofule Path Doesn't Exist
					echo "Module Path ($modulePath) doesn't exist or isn't readable chmod 755 && chwon -R www-data:www-data /packages/<your-package>";
			}
	
            //Adding All to Container here
            $table->startRow();
            $table->addCell($tbl->show(), '', '', 'center', '', '','');
            $table->endRow();

            $objForm = new form('gentestcasefrm', $this->uri(array('action' => 'gentestcase'), 'phpunit'));

            //Stripping New Lines and preparing for boxy input = (Facebook style window)
			$display = '<p><H4>Select the classes to create PHPUnit Test Cases for:</H4></p>';

			$display .= 'Select: <a href="#select_all" name="select_all"  id="select_all" onclick="phpuSelectAll()">All</a> | <a href="#select_none" name="select_none" id="select_none" onclick="phpuSelectNone()">None</a>';

            $display .= str_replace("\n", '', $table->show());
            $display .= $action;

			$chk['dbmgmt'] = new checkbox($file);
			$chk['dbmgmt']->cssClass = 'phpuCheckFiles';
			$chk['dbmgmt']->setChecked(TRUE);

			$chk['util'] = new checkbox($file);
			$chk['util']->cssClass = 'phpuCheckFiles';
			$chk['util']->setChecked(TRUE);

			$chk['display'] = new checkbox($file);
			$chk['display']->cssClass = 'phpuCheckFiles';
			$chk['display']->setChecked(FALSE);
			$chk['display']->extra = ' disabled';

			$display .= '<p><H4>Select the method type patterns to build for (functions will be logically grouped according to this) </H4></p>';
			$display .= $chk['dbmgmt']->show() . ' Data Management Functions<br/>';
			$display .= $chk['util']->show() . ' Utility Functions <br/>';
			$display .= $chk['display']->show() . ' Display Functions (Disabled - Plans to integrate selinium for frontend testing) <br/>';

			$display .= '<input type="hidden" name="mod" value="' . $moduleName . '" />';
			$display .= '<input type="hidden" name="modpath" value="' . $modulePath . '" />';

			$display .= '<br/><input type="submit" value="Generate"/>';

			$objForm->addToForm($display);

			$display = $objForm->show();

            return $display;
        }

       /**
        * Method to return the Header + Top Navigation items
        *
        * @return string The top navigation header
        * @access public
        */
        public function showTopNav()
        {

            $objIcon = $this->newObject('geticon', 'htmlelements');
            $tbl = $this->newObject('htmltable', 'htmlelements');
            $tblH = $this->newObject('htmltable', 'htmlelements');
            $h3 = $this->getObject('htmlheading', 'htmlelements');
            //$Icon = $this->newObject('geticon', 'htmlelements');
            $objContainerLayer = $this->newObject('layer', 'htmlelements');
            $objLayer = $this->newObject('layer', 'htmlelements');
            //$Icon->setIcon('loading_circles_big');
            $objRound =$this->newObject('roundcorners','htmlelements');
            $objIcon->setIcon('phpunit_big', 'png', 'icons/modules/');

            $topNav = $this->getTopNav();

            //$strPhpUnit = $this->objLanguage->languageText('mod_phpunit_main', 'cmsadmin')
            $strPhpUnit = 'PHPUnit Test Case Builder';
            $h3->str = $strPhpUnit;

            $tblH->width = '300px';
            $tblH->startRow();
            $tblH->addCell($objIcon->show(), '50px');
            $tblH->addCell($h3->show(), '150px', 'center');
            $tblH->endRow();
            
            $objLayer->str = $tblH->show();
            $objLayer->border = '; float:left; align: left; margin:0px; padding:0px;';
            $header = $objLayer->show();
            
            $objLayer->str = $topNav;
            $objLayer->border = '; float:right; align:right; margin:0px; padding:0px;';
            $header .= $objLayer->show();
            
            $objLayer->str = '';
            $objLayer->border = '; clear:both; margin:0px; padding:0px;';
            $headShow = $objLayer->show();
            
            $objContainerLayer->str = $header.$headShow.'<hr />';
            $objContainerLayer->id = 'phpunit_header';
            
            return $objContainerLayer->show();
        }


       /**
        * Method to get the Top Navigation Icons for PHPUnit Test Case Builder
        * 
        * @return str the string top navigation
        *
        * @access public
        */
        public function getTopNav(){

            //Declare objects
            $tbl = $this->newObject('htmltable', 'htmlelements');
            $objIcon = $this->newObject('geticon', 'htmlelements');

            $iconList = '';

            //Setting up the Boxy Form
            
            $this->objBox->setHtml($this->getAddMappingForm());
            $this->objBox->setTitle('Add URL Mapping');
            $this->objBox->attachClickEvent('add_mapping_form');
            
            // New / Add
            $url = 'javascript:void(0)';
            $linkText = $this->objLanguage->languageText('word_new');
            $iconList .= $objIcon->getCleanTextIcon('add_mapping_form', $url, 'new', $linkText, 'png', 'icons/phpunit/');

            /*
            // Refresh Grid
            $url = 'javascript:void(0)';
            //$linkText = $this->objLanguage->languageText('word_refresh');
            $linkText = 'Refresh';
            $iconList .= $objIcon->getCleanTextIcon('refresh_grid', $url, 'refresh', $linkText, 'png', 'icons/phpunit/');
            */

            /* //Need to revisit multiple deletes using jqGrid - Need to fix this in the jqgrid.formedit.js
            // Delete
            $url = 'javascript:void(0)';
            //$linkText = $this->objLanguage->languageText('word_refresh');
            $linkText = 'Delete';
            $iconList .= $objIcon->getCleanTextIcon('delete_griditems', $url, 'delete', $linkText, 'png', 'icons/phpunit/');
            */

            return '<div style="align:right;">'.$iconList.'</div>';

            //return $tbl->show();

        }



    }

?>