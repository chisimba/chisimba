<?php
set_time_limit(0);
/**
 *
 * Portal importer
 *
 * Portal importer was developed to import the static content from the UWC portal 
 * into Chisimba. Portal importer is not an end user module, but rather a tool for 
 * developers to work with to import a large volume of structured web content from 
 * static HTML into the Chisimba CMS. Do not have this module installed on a 
 * production server as it has NO SECURITY!
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
 * @package   helloforms
 * @author    Derek Keats _EMAIL
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 11929 2008-12-29 21:15:36Z charlvn $
 * @link      http://avoir.uwc.ac.za
 */

// security check - must be included in all scripts
if (!
				/**
				 * The $GLOBALS is an array used to control access to certain constants.
				 * Here it is used to check if the file is opening in engine, if not it
				 * stops the file from running.
				 *
				 * @global entry point $GLOBALS['kewl_entry_point_run']
				 * @name   $kewl_entry_point_run
				 *
				 */
				$GLOBALS['kewl_entry_point_run'])
{
		die("You cannot view this page directly");
}
// end security check

/**
 *
 * Controller class for Chisimba for the module codetesting
 *
 * @author Derek Keats
 * @package codetesting
 *
 */
class portalimporter extends controller
{

		/**
		 *
		 * @var string $objConfig String object property for holding the
		 * configuration object
		 * @access public;
		 *
		 */
		public $objConfig;

		/**
		 *
		 * @var string $objLanguage String object property for holding the
		 * language object
		 * @access public
		 *
		 */
		public $objLanguage;
		/**
		 *
		 * @var string $objLog String object property for holding the
		 * logger object for logging user activity
		 * @access public
		 *
		 */
		public $objLog;

		public $depth;
		public $sitepath;
		public $objUtils;

		/**
		 *
		 * Intialiser for the codetesting controller
		 * @access public
		 *
		 */
		public function init()
		{
				$this->objContent = $this->getObject('dbcontent', 'cmsadmin');
				$this->objConfig = $this->getObject('altconfig', 'config');
				$this->objRegex = $this->getObject('regexes','utilities');
				$this->objLog = $this->getObject('dblog', 'portalimporter');
				$this->objContent = $this->getObject('dbcontent', 'cmsadmin');
				$this->objUser = $this->getObject('user', 'security');
				$this->objLanguage = $this->getObject('language', 'language');
				// Create the configuration object
				//$this->objConfig = $this->getObject('config', 'config');
				//$this->config = $this->getObject('altconfig','config');
				$this->sConfig = $this->getObject('dbsysconfig', 'sysconfig');
				$this->depth = $this->sConfig->getValue('mod_portalimporter_parsedepth', 'portalimporter');
				$this->sitepath = $this->sConfig->getValue('mod_portalimporter_sitepath', 'portalimporter');
				$this->objUtils = $this->getObject('portalfileutils', 'portalimporter');

		}


		/**
		 *
		 * The standard dispatch method for the codetesting module.
		 * The dispatch method uses methods determined from the action
		 * parameter of the  querystring and executes the appropriate method,
		 * returning its appropriate template. This template contains the code
		 * which renders the module output.
		 *
		 */
		public function dispatch()
		{
				//Get action from query string and set default to view
				$action=$this->getParam('action', 'default');
				// Convert the action into a method
				$method = $this->__getMethod($action);
				//Return the template determined by the method resulting from action
				return $this->$method();
		}

		private function __default()
		{
				$str = "Working here";
				$this->setVarByRef('str', $str);
				return "default_tpl.php";
		}

		private function __readportal()
		{
				$rP = $this->getObject('portalfileutils', 'portalimporter');
				$start_dir = "start";
				$level=1;  // level is the first level started at
				$last=$this->depth; // Go deeper baby
				$dirs = array();  // SET dirs as an ARRAY so it can be read
				$files = array(); //SET files as an ARRAY so it can be read
				$rP->readpath($start_dir,$level, $last, $dirs,$files);
				//$str .= $rP->showDirs();
				$str .= nl2br(htmlentities($rP->showFilesAsXML()));
				$str .= "<hr />";
				$str .= "<br />Directory size: ";
				$str .= $rP->getSize();
				$this->setVarByRef('str', $str);
				return "dump_tpl.php";
		}

		private function __genxml()
		{
				$rP = $this->getObject('portalfileutils', 'portalimporter');
				$start_dir = "start";
				$level=1;
				$last=$this->depth;
				$dirs = array();
				$files = array();
				$rP->readpath($start_dir,$level, $last, $dirs,$files);
				$str = $rP->xmlToFile();
				$this->setVarByRef('str', $str);
				return "dump_tpl.php";
		}

		private function strTfToBool($str)
		{
				if (strtolower($str) == "true") {
						return TRUE;
				} else {
						return FALSE;
				}
		}

		private function __showstructured()
		{
				$rP = $this->getObject('portalfileutils', 'portalimporter');
				$rP->hideDuds = $this->strTfToBool($this->getParam('hideduds', 'false'));
				$rP->hideLegacy = $this->strTfToBool($this->getParam('hidelegacy', 'false'));
				$rP->hideStructured = $this->strTfToBool($this->getParam('hidestructured', 'false'));
				$start_dir = "start";
				$level=1;
				$last=$this->depth;
				$dirs = array();
				$files = array();
				$rP->readpath($start_dir,$level, $last, $dirs,$files);
				$str = $rP->listFilesWithDelimiters();
				$this->setVarByRef('str', $str);
				return "dump_tpl.php";
		}

		public function __showfiles()
		{
				$rP = $this->getObject('portalfileutils', 'portalimporter');
				$start_dir = "start";
				$level=1;
				$last=$this->depth;
				$dirs = array();
				$files = array();
				$rP->readpath($start_dir,$level, $last, $dirs,$files);
				$str = $rP->showFiles();
				$this->setVarByRef('str', $str);
				return "dump_tpl.php";
		}

		public function __showdirs()
		{
				$rP = $this->getObject('portalfileutils', 'portalimporter');
				$start_dir = "start";
				$level=1;
				$last=$this->depth;
				$dirs = array();
				$files = array();
				$rP->readpath($start_dir,$level, $last, $dirs,$files);
				$str = $rP->showDirs();
				$this->setVarByRef('str', $str);
				return "dump_tpl.php";
		}

		public function __findwordcrud()
		{
				$rP = $this->getObject('portalfileutils', 'portalimporter');
				$start_dir = "start";
				$level=1;
				$last=$this->depth;
				$dirs = array();
				$files = array();
				$rP->readpath($start_dir,$level, $last, $dirs,$files);
				$str = $rP->detectWordCrap();
				$this->setVarByRef('str', $str);
				return "dump_tpl.php";
		}

		public function __storedata()
		{
				$rP = $this->getObject('portalfileutils', 'portalimporter');
				$start_dir = "start";
				$level=1;
				$last=$this->depth;
				$dirs = array();
				$files = array();
				$rP->readpath($start_dir,$level, $last, $dirs,$files);
				$str = $rP->storeData();
				$this->setVarByRef('str', $str);
				return "dump_tpl.php";
		}

		public function __imagemove()
		{
				$rP = $this->getObject('portalfileutils', 'portalimporter');
				$start_dir = "start";
				$level=1;
				$last=$this->depth;
				$dirs = array();
				$files = array();
				$rP->readpath($start_dir,$level, $last, $dirs,$files);
				$str = $rP->moveImagesToRepository();
				$this->setVarByRef('str', $str);
				return "dump_tpl.php";
		}

		public function __dummy()
		{
				$contents='Now is the time for all good images <img src="img.gif"> to <IMG src="uppercasetest.gif"> come to the <img src=noquotes.gif> aid of the image <img src="dummy.gif" alt="Dummy">';
				$rP = $this->getObject('portalfileutils', 'portalimporter');
				$str = htmlentities($rP->resetImages($contents));
				$this->setVarByRef('str', $str);
				return "dump_tpl.php";

		}


		/**
		 *  Import the content from import location:
		 *  currently /var/www/static_content/
		 *  into the sections and content tables.
		 *
		 *  The Sections structure is based on the directory structure
		 *  The Content Menu Names rely on the page title to be formatted lik '<title> Something -- THE_PAGE_NAME </title>'
		 *
		 *  Where the filenames can be used as displayable names even if they look like:
		 * 'home_page.htm' they can be run thrrough the fixdata action to fix the titles accordingly
		 *  After this the goportal can be run.
		 *
		 *  The imported items are logged to tbl_portalimporter_log table for use with further correction routines
		 *  like fixPageRef, fixDocRef, fixMediaRef
		 *
		 * @author Paul Scott, Charl Mert
		 *
		 */
		public function __goPortal()
		{
				$time_start = microtime(true);
				$this->objStdlib = $this->getObject('splstdlib', 'files');
				$this->objCmsDb = $this->getObject('dbcmsadmin', 'cmsadmin');
				// clean the file tree
				$this->objStdlib->frontPageDirCleaner($this->sitepath);
				//die();
				// create the sections and subsections
				$sections = $this->objStdlib->dirFilterDots($this->sitepath);
				$secname = NULL;
				foreach($sections as $subsections)
				{
						// create the section
						$sec = $this->objUtils->doSection($subsections, $secname);
						$secid = $sec['secid'];
						$secname = $sec['secname'];
						// subsections
						$this->objUtils->doSubSections($subsections, $secname, $secid);
						// add the top level page to the cms section

						unset($subsection);
						unset($secid);
				}
				$time_end = microtime(true);
				$time = $time_end - $time_start;

				echo "Portal import completed in $time seconds\n";
				die();
		}

		/**
		 *
		 *  This function will iterate static_content/* htm files
		 *  and do inline replacement of <title>..</title> to conform
		 *  to a format for the goportal action
		 *
		 *  The <title> renaming will be based on the file name of the current file
		 *  Algo : Uppercase the first letter of each work,
		 *         Replace underscores with spaces.
		 *
		 * @author Charl Mert
		 */

		public function __fixdata()
		{
				$time_start = microtime(true);

				$this->objStdlib = $this->getObject('splstdlib', 'files');

				$files = $this->objStdlib->recDir($this->sitepath);

				$filtered_files = array();

				//Files that match the following patterns are ignored
				$ignored_files = array(
								'/index.*/i',
								'/(.*)footer(.*)/i',
								'/(.*)\.txt$/i',
								'/(.*)banner(.*)/i'
								);

				//Only the following file types will be considered
				$file_types = array(
								'htm',
								'html'
								);

				//Shredding the files list through a matsarella cheese filter
				//mmmmm is naays
				foreach ($files as $file){

						$file_name = end(explode('/', $file));
						$file_ext = end(explode('.', $file_name));

						//Filtering ignored files
						$pass_filter = true;
						foreach ($ignored_files as $ignore){
								if (preg_match($ignore, $file_name)){
										$pass_filter = false;
								}
						}

						//Filtering supported types
						$pass_type = false;
						foreach ($file_types as $type){
								if ($file_ext == $type){
										$pass_type = true;
								}
						}

						//Adding the filtered element to the filtered array
						if ($pass_filter && $pass_type){
								array_push($filtered_files, $file);
						}
				}

				//var_dump($filtered_files); exit;
				foreach ($filtered_files as $file){
						$contents = file_get_contents($file);

						$title = $this->objRegex->get_doc_title($contents);

						//$title = $tresults[1][0];
						$title = explode("--", $title);

						$must_add_title = false;

						if(isset($title[1]))
						{
								$title = $title[1];
								$must_add_title = true;
						} else {
							log_debug("FixData : Missing Title Corrected $file");
						}
					
						//Only fixing the titles of the files that missed them
						if (!$must_add_title){
						
								$content_path = $subsect.$page;
								// grok the title
								//preg_match_all('/\<title>(.*)\<\/title\>/U', $contents, $tresults, PREG_PATTERN_ORDER);
								//$title = $this->objRegex->get_doc_title($contents);

								$tmp_file = str_replace($this->sitepath, '', $file);
								$title_items = explode('/', $tmp_file);

								/* This includes the current path structure in the title
								   foreach ($title_items as $section){
								   $title .= $this->str_first_upper($this->strip_ext($section), '_').' ';
								   }
								 */

								//Only using the filename to determine the title (This will form the content items display name)
								$file_name = end($title_items);
								$title = 'The University of the Western Cape -- '.$this->str_first_upper($this->strip_ext($file_name), '_').' -- Page';

								//Only repairing titles with docs that have titles
								if (preg_match('/\<title>(.*)\<\/title>/U', $contents)){
										$contents = preg_replace('/\<title>(.*)\<\/title>/U', "<title>$title</title>", $contents);
										//echo $contents; exit;
								}

								echo "Repairing $file";

								$fp = fopen($file, 'w');
								if (!$fp){
										die('Error Could not write to '. $file. "\nYou need to chmod -R 777 $this->sitepath");
								}

								fwrite($fp, $contents);
								fclose($fp);
						}
				}
			
				$time_end = microtime(true);
                $time = $time_end - $time_start;

                echo "<br/>Fixed Missing Page Titles in $time Seconds.";

		}





		/**
		 *	Will iterate through content items to find Document Refs and replace them with refs
		 *	to imported content items.
		 *
		 * 	Currently only supports hrefs that reference documents in the same folder
		 *	TODO : building support for 
		 *			1. "../../relative/file.html" relative to current path
		 *			2. "/public/.../file.htm" absolute from root
		 *
		 *	e.g. pages that referenced other pages within the same directory will now be made 
		 * 	to reference the content item of it's imported equivalent.
		 *
		 * @author Charl Mert
		 */

		public function __fixDocumentRef()
		{
				//echo "Updating Page References to point to CMS content items now.<br/>";
				$time_start = microtime(true);

				//Only the following file types will be considered
				$file_types = array(
								'doc',
								'odt',
								'ods',
								'xls',
								'ppt',
								'pdf',
								'jpg',
								'png',
								'gif',
								'jpeg',
								'mp3',
								'mp4',
								'wav',
								'ogg',
								'pdf',
								'zip',
								'gz',
								'tar'
								);

				//Getting a list of content records that contain href= attributes
				$content_records = $this->objContent->getHrefContentRecords();

				$count = 0;
				foreach ($content_records as $content){

						$count++; // for debug purposes

						$content_id = $content['id'];
						$section_id = $content['sectionid'];
						$body = $content['body'];
						$body = stripslashes($body);

						//Tidying the body without the htmlentities replacements 
						//so that the regex will be garenteed to work
						/*
						   $options = array('indent' => TRUE,
						   'output-xhtml' => TRUE,
						   'wrap' => 20000);

						   $this->objUtils->unCrapify($body, $options);
						 */

						//$hrefs = $this->objRegex->get_a_href($body); //not exactly what i need

						//Matching for all between quotes lazy I know ... :-|
						$pattern = '/(\"|\')(.*?)(\"|\')/i';
						$match_count = preg_match_all($pattern, $body, $matches);
						$hrefs = $matches[2];	

						//Cleaning up the captured hrefs Goal is to grab ONLY the filename and filter for supported types
						$clean_hrefs = array();
						foreach ($hrefs as $href){
								$href = stripslashes($href);
								$href = trim($href);

								$file_ext = end(explode('.', $href));
								//Filtering supported types
								$pass_type = false;
								foreach ($file_types as $type){
										if ($file_ext == $type){
												$pass_type = true;
										}
								}

								if ($pass_type){
										array_push($clean_hrefs, $href);
								}

						}

						//if($count > 1){
						//if ($content_id == 'gen11Srv53Nme12_5768_1208260400') {
								//The new link should look lik:
								//http://localhost/svn/index.php?module=cms&action=showfulltext&id=gen11Srv53Nme12_6472_1207831625&sectionid=gen11Srv53Nme12_2996_1207831625
								//var_dump($hrefs);	
								var_dump($clean_hrefs);	
								//Have the matching content id's will replace the current body with the correct link	
								foreach ($clean_hrefs as $href){
										$cid = $content_id;
										$sid = $section_id;
										$fname = end(explode('/', $href));

										//Constructing the link
										$link = $this->objConfig->getsiteRoot()."usrfiles/importcms/$sid/$fname";
										$local_path = $this->objConfig->getsiteRootPath()."usrfiles/importcms/$sid/$fname";
										//var_dump($link);
										var_dump($local_path);
										if (file_exists($local_path)){
												$pattern = "/(\"|\')(.*?)".addslashes($fname)."(.*?)(\"|\')/i";
												if (!($body = preg_replace($pattern, $link, $body))){
														log_debug("FixDocumentRef Failed : Bad Regex Pattern $pattern");
														//exit;
												}
										} else {
												log_debug("FixDocumentRef Failed : File Not Found : $link");
												//exit;
										}
										//var_dump($link);
								}

								//Updating the content_item with a tyt body
								$this->objContent->updateContentBody($content_id, $body);
								//echo "$body<br/>";
								//echo "Updated ContentID : $content_id <br/>";
						//		exit;
						//}


				}

				$time_end = microtime(true);
				$time = $time_end - $time_start;

				echo "Fixed Page References in $time Seconds.";

				}









				/**
				 *	Will iterate through content items to find STATIC Document Refs and replace them with refs
				 *	to imported content items.
				 *
				 *  This fix is fixed to the UWC Portal Data and will simply replace items that linked to
				 *  the /downloads and with the imported path
				 *
				 * @author Charl Mert
				 */

				public function __fixStaticRef()
				{
						//echo "Updating Page References to point to CMS content items now.<br/>";
						$time_start = microtime(true);

						//Only the following file types will be considered
						$file_types = array(
										'doc',
										'odt',
										'ods',
										'xls',
										'ppt',
										'pdf',
										'jpg',
										'png',
										'gif',
										'jpeg',
										'mp3',
										'mp4',
										'wav',
										'ogg',
										'pdf',
										'zip',
										'gz',
										'tar'
										);

						//Getting a list of content records that contain href= attributes
						$content_records = $this->objContent->getHrefContentRecords();

						$count = 0;
						foreach ($content_records as $content){

								$count++; // for debug purposes

								$content_id = $content['id'];
								$section_id = $content['sectionid'];
								$body = $content['body'];
								$body = stripslashes($body);

								$pattern = '/\<(a|img).*(href|src).*\=.*\".*(^\/ |)downloads/i';
						}

						//Updating the content_item with a tyt body
						$this->objContent->updateContentBody($content_id, $body);
						//echo "$body<br/>";
						//echo "Updated ContentID : $content_id <br/>";
						$time_end = microtime(true);
						$time = $time_end - $time_start;

						echo "Fixed Page References in $time Seconds.";

				}



















				/**
				 *	Will iterate through content items to find page refs and replace them with refs
				 *	to imported content items.
				 *
				 * 	Currently only supports hrefs that reference documents in the same folder
				 *	TODO : building support for 
				 *			1. "../../relative/file.html" relative to current path : DONE
				 *			2. "/public/.../file.htm" absolute from root
				 *			3. http://...../path/file
				 *
				 *	e.g. pages that referenced other pages within the same directory will now be made 
				 * 	to reference the content item of it's imported equivalent.
				 *
				 * @author Charl Mert
				 */

				public function __fixPageRef()
				{
						//echo "Updating Page References to point to CMS content items now.<br/>";
						$time_start = microtime(true);

						//Only the following file types will be considered
						$file_types = array(
										'htm',
										'html'
										);

						//Getting a list of content records that contain href= attributes
						$content_records = $this->objContent->getHrefContentRecords();

						$count = 0;
						foreach ($content_records as $content){

								$count++; // for debug purposes

								$content_id = $content['id'];
								$section_id = $content['sectionid'];
								$body = $content['body'];
								$body = stripslashes($body);

								//Tidying the body without the htmlentities replacements 
								//so that the regex will be garenteed to work
								/*
								   $options = array('indent' => TRUE,
								   'output-xhtml' => TRUE,
								   'wrap' => 20000);

								   $this->objUtils->unCrapify($body, $options);
								 */

								//$hrefs = $this->objRegex->get_a_href($body); //not exactly what i need

								//Matching for all between quotes lazy I know ... :-|
								$pattern = '/(\"|\')(.*?)(\"|\')/i';
								$match_count = preg_match_all($pattern, $body, $matches);
								$hrefs = $matches[2];	

								//Cleaning up the captured hrefs Goal is to grab ONLY the filename and filter for supported types
								$clean_hrefs = array();
								foreach ($hrefs as $href){
										$href = stripslashes($href);
										$href = trim($href);

										$file_ext = end(explode('.', $href));
										//var_dump($file_ext);
										//checking for # refs

										$pos = strpos($file_ext, '#');
										if ($pos){
											
											$ext_parts = explode('#', $file_ext);
											$file_ext = $ext_parts[0];	
											//var_dump($file_ext);
										}
										//Filtering supported types
										$pass_type = false;
										foreach ($file_types as $type){
												if ($file_ext == $type){
														$pass_type = true;
												}
										}

										if ($pass_type){
												array_push($clean_hrefs, $href);
										}

								}

								$found_context_match = false;
								$used_relative_path = false;
								//Find the corresponding contentid if this href points to a file that was imported
								$content_href = array();
								foreach ($clean_hrefs as $href){
										//TODO: Adjusting the path for relative referencing	(../../)								
										$pos = preg_match('/\.\.\//', $href);
										if ($pos){
											//Determining how many times to go back in the dir struct
											$back_count = preg_match_all('/\.\.\//', $href, $matches);
											//var_dump($back_count.' '.$href);
										}
										//var_dump($back_count.' | '.$href);
										//Getting a list of possible content matches for the current section
										$href_content_id = array();

										$file_compare = end(explode('/', $href));	
										//Including # refs here									
										$file_compare = preg_replace('/#.*/', '', $file_compare);							
										//var_dump($file_compare);


										//var_dump($file_compare);
										$href_content_id = $this->objLog->getContentFileMatch($file_compare, $href_section_id);
										//var_dump($href_content_id);
		
										//var_dump($href_content_id);
										foreach ($href_content_id as $log){
																					//Try context sensitive match based on the current content item's section_id
												$path_compare = $log['filepath'];
												if ($back_count > 0){
													$path_parts = explode('/', $path_compare);
													$path_parts_count = count($path_parts);
					
													$new_path = '';
													//Getting the left part of the path according to the import log
													for ($i = 0; $i < $path_parts_count - ($back_count + 1); $i++){
														$new_path .= $path_parts[$i].'/';
													}	
													
													//Getting the right part of the path+filename according to the import href
													$right_path_parts = explode('/', $href);
                                                    $right_path_parts_count = count($right_path_parts);
													for ($i = $back_count; $i < $right_path_parts_count; $i++){
														$new_path .= $right_path_parts[$i].'/';
													}	
													$new_path = substr($new_path, 0, strlen($new_path)-1);
													//$new_path = rtrim($new_path,'/ ');
													$path_compare = $new_path;
												//	var_dump($href.' | '.$new_path);
													$used_relative_path = true;
												}

												//var_dump($path_compare);
												//Determining the href pages intended sectionid based on the path (for context sensitivity)
												$href_section_ids = $this->objLog->getSectionPathMatch($path_compare);
		
												/*
												if ($path_compare == '/var/www/static_content/faculty_department/afrikaans/index.htm'){
													var_dump($href_section_ids);	

												}*/

												//var_dump('looking for '.$href);

												//var_dump($log[filepath]);
												//var_dump($href_section_ids);
												//var_dump($section_id);

												//Match current context
												
												if (!$used_relative_path){
													foreach ($href_section_ids as $secid){
														if ($section_id == $secid[section_id]){
																//var_dump('Found Current Match');	
																//var_dump($secid);
																$found_context_match = true;
																$href_section_id = $section_id;
																$href_content_id = $log;
																break; //break on first match
														}
													}
												}
												else {
												//Matching relative context
													//var_dump($href_section_ids);
													if ($path_compare == $log[filepath]){
														//var_dump($log[filepath].$path_compare);
														$href_content_id = $log;
													}
													/*
													foreach ($log as $log_entry){
														if ($path_compare == $log_entry[filepath]){
															$href_content_id = $log;
															$href_section_id = $href_section_ids[0][section_id];
															$found_context_match = true;
															var_dump('Found Relative Match');
															var_dump($href_content_id);	
															//$href_section_id = $href_section_ids[0];
															break;
														}
													}
													*/
												}
										}

										//var_dump($href_content_id);	
										//If not then do a non context sensitive search
										/*
										if (!$found_context_match){
												
												//Report Broken Link
												
												if (!isset($href_content_id[0])){
													//Checking for the actual ref in the db
													//if doesnt exsist flag in the content with a broken icon that points to the edit page
													
													$href_content_id = ""; 
												}

											//Logging this as error to be flagged for manual review
											log_debug("FixPageRef Failed : Couldn't find matching content_id for page $href in body of $content_id");
										} else {
											if (!$used_relative_path){
												$href_content_id = $href_content_id[0];
											} else {

											}
										}
										*/

										//if (!$used_relative_path){
										//	$href_content_id = $href_content_id[0];
										//}
										//echo "Using : ".$href." | ContentID : $href_content_id<br/>";

										//var_dump($href_content_id);

										//var_dump($href);

										//if ($href_content_id != ''){
												
										//var_dump($href_content_id);

										if ($href_content_id['content_id'] != NULL){
                                                $h_content_id = $href_content_id['content_id'];
                                        } else {
                                                $h_content_id = $href_content_id[0]['content_id'];
                                        }

                                        if ($href_content_id['section_id'] != NULL){
                                                $h_section_id = $href_content_id['section_id'];
                                        } else {
                                                $h_section_id = $href_content_id[0]['section_id'];
                                        }

                                        $item = array($h_content_id, $h_section_id, $href);
                                        array_push($content_href, $item);
										//var_dump($item);
										//}
								}

								//if($count > 1){
								//if ($content_id == 'gen11Srv53Nme12_7147_1208282913'){
										//The new link should look lik:
										//http://localhost/svn/index.php?module=cms&action=showfulltext&id=gen11Srv53Nme12_6472_1207831625&sectionid=gen11Srv53Nme12_2996_1207831625

										//var_dump($content_href);
										//exit;

										$hasBrokenLinks = false;
										//Have the matching content id's will replace the current body with the correct link	
										foreach ($content_href as $href){
												$cid = $href[0];
												$sid = $href[1];
												$fname = end(explode('/', $href[2]));

												//Constructing the link
												if ($cid != ''){
													$link = "?module=cms&amp;action=showfulltext&amp;id=$cid&amp;sectionid=$sid";
													
													//Replacing the link
													/*	
													if (!$used_relative_path){
														
														$pattern = "/(\"|\')(.*?)".addslashes($fname)."(.*?)(\"|\')/i";
                                        			} else {
														$pattern = "/(\"|\')(.*?)".addslashes($href[2])."(.*?)(\"|\')/i";
														var_dump($pattern);
													}
                                               		if (!($body = preg_replace($pattern, $link, $body))){
                                                        log_debug("FixPageRef Failed : Bad Regex Pattern $pattern");
                                                        //exit;
                                                	}
													*/
													$body = str_replace($href[2], $link, $body);	
												} else {
													//Generating the editcontent link to be placed with a description as a message to
													//correct this content
													//module=cmsadmin&action=addcontent&id=theid
													//$link = "?module=cms&amp;action=showfulltext&amp;id=$cid&amp;sectionid=$sid";
													/*
													$pattern = "/(\"|\')(.*?)".addslashes($fname)."(.*?)(\"|\')/i";
													if (!($body = preg_replace($pattern, $link, $body))){
														log_debug("FixPageRef Failed : Bad Regex Pattern $pattern");
														//exit;
													}*/
													log_debug("FixPageRef Failed : Bad Page Ref $href[2]");
													//var_dump("FixPageRef Failed : Bad Page Ref $href[2]");

													$hasBrokenLinks = true;
												}
																								//var_dump($link);
										}
										
										if ($hasBrokenLinks){
											//$body = '<div class=\'error\'>Please correct the broken links on this page</div>'.$body;
										}
										//Updating the content_item with a tyt body
										$this->objContent->updateContentBody($content_id, $body);
									

										//echo "$body<br/>";
										//echo "Updated ContentID : $content_id <br/>";
										//exit;
								//}


						}

						$time_end = microtime(true);
						$time = $time_end - $time_start;

						echo "Fixed Page References in $time Seconds.";

				}



				/**
				 *  Strips a files extention
				 */

				function strip_ext($str, $extensions = 'htm|html')
				{
						$ext_arr = explode('|', $extensions);
						foreach ($ext_arr as $ext)
						{
								$str_ext = end(explode('.', $str));
								if ($str_ext == $ext)
								{
										$ret_str = str_replace('.'.$ext, '', $str);
										echo $str."\n";
										return $ret_str;
								} else {
										return $str;
								}
						}
				}

				/*
				 * Makes a strings first character an uppercase character
				 *
				 */

				function str_first_upper($str, $delim = ' '){
						$str = explode($delim, strtolower($str));
						for($i = 0; $i < count($str); $i++){
								if (count($str) > 1){
										$str[$i] = strtoupper(substr($str[$i], 0, 1)) . substr($str[$i], 1) . ' ';
								} else {
										$str[$i] = strtoupper(substr($str[$i], 0, 1)) . substr($str[$i], 1);
								}
						}
						return implode('', $str);
				}


				/*------------- BEGIN: Set of methods to replace case selection ------------*/

				/**
				 *
				 * Method to return an error when the action is not a valid
				 * action method
				 *
				 * @access private
				 * @return string The dump template populated with the error message
				 *
				 */
				private function __actionError()
				{
						$this->setVar('str', "<h3>"
										. $this->objLanguage->languageText("phrase_unrecognizedaction")
										.": " . $action . "</h3>");
						return 'dump_tpl.php';
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
				function __validAction(& $action)
				{
						if (method_exists($this, "__".$action)) {
								return TRUE;
						} else {
								return FALSE;
						}
				}

				/**
				 *
				 * Method to convert the action parameter into the name of
				 * a method of this class.
				 *
				 * @access private
				 * @param string $action The acti


				 * This is a method to determine if the user has to
				 public function requiresLogin()
				 {
				 $action=$this->getParam('action','NULL');
				 switch ($action)
				 {
				 case 'view':
				 return FALSE;
				 break;
				 default:
				 return TRUE;
				 break;
				 }
				 }on parameter passed byref
				 * @return stromg the name of the method
				 *
				 */
				function __getMethod(& $action)
				{
						if ($this->__validAction($action)) {
								return "__" . $action;
						} else {
								return "__actionError";
						}
				}

				/*------------- END: Set of methods to replace case selection ------------*/



				/**
				 *
				 * This is a method to determine if the user has to
				 * be logged in or not. Note that this is an example,
				 * and if you use it view will be visible to non-logged in
				 * users. Delete it if you do not want to allow annonymous access.
				 * It overides that in the parent class
				 *
				 * @return boolean TRUE|FALSE
				 *
				 */
				public function requiresLogin()
				{
						$action=$this->getParam('action','NULL');
						switch ($action)
						{
								case 'view':
										return FALSE;
										break;
								default:
										return TRUE;
										break;
						}
				}
		}
		?>
