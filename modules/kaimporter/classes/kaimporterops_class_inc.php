<?php
/**
 *
 * Operations for Khan Academy Importer
 *
 * Operations for Khan Academy Importer. It provides a variety of
 * methods for working with the Khan Academy data.
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
 * @package   kaimporter
 * @author    Derek Keats derek@dkeats.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
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
 * Operations for Khan Academy Importer
 *
 * Operations for Khan Academy Importer. It provides a variety of
 * methods for working with the Khan Academy data.
*
* @package   kaimporter
* @author    Derek Keats derek@dkeats.com
*
*/
class kaimporterops extends dbtable
{

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
    * Intialiser for the kaimporter ops
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->objLanguage = $this->getObject('language', 'language');
        // Load Context Object
        $this->objContext = $this->getObject('dbcontext', 'context');
        // Store Context Code
        $this->contextCode = $this->objContext->getContextCode();
        if ($this->contextCode == NULL ||
          $this->contextCode == "" ||
          $this->contextCode == "Lobby") {
            $this->contextCode = FALSE;
        }
    }

    /**
     *
     * Create the form for choosing the content to import
     * 
     * @return string The rendered form
     * @access public
     * 
     */
    public function buildForm()
    {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('dropdown', 'htmlelements');
        $this->loadClass('htmltable', 'htmlelements');
        $table = $this->newObject('htmltable', 'htmlelements');
        $dropDown = $this->getChaptersDropdown();
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_kaimporter_selchap',
          'kaimporter'));
        $table->addCell($dropDown);
        $table->endRow();
        $textinput = new textinput('path');
        $textinput->size = 40;
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_kaimporter_entpath',
          'kaimporter'));
        $table->addCell($textinput->show());
        $table->endRow();
        $buttonTitle = $this->objLanguage->languageText('mod_kaimporter_load',
          'kaimporter');
        $button = new button('submitUser', $buttonTitle);
        $button->setToSubmit();
        $table->startRow();
        $table->addCell($this->objLanguage->languageText('mod_kaimporter_clickbut',
          'kaimporter'));
        $table->addCell($button->show());
        $table->endRow();
        $formAction = $this->uri(array(
          'action'=>'load'), 'kaimporter');
        $form = new form('sendvideos', NULL);
        $form->setAction($formAction);
        $form->addToForm($table->show());
        return $form->show();

    }
    
    /**
     *
     * Load the data into the chapter as pages, each page
     * will have a video FILTER in it
     * 
     * @return boolean 
     * @access public
     */
    public function loadData()
    {
        $rootPath = $this->getUrlRoot();
        $khanPath = $this->getParam('path', FALSE);
        if ($khanPath) {
            // Test with KhanAcademy_maths
            $basePath = $rootPath . $khanPath . '/files/';
            $fullPath = $rootPath . $khanPath . '/files/data_master.txt';
            $data = $this->readData($fullPath, $basePath);
            return $this->doLoad($data, $basePath);
            //return $this->testTheData($data, $basePath);
        } else {
            return FALSE;
        }
    }

    //@REMOVE
    public function buildArrayOfPages()
    {
        $rootPath = $this->getUrlRoot();
        $fullPath= $rootPath . 'KhanAcademy_maths/files/data_master.txt';
        $data = $this->readData($fullPath);
        return $this->getChaptersDropdown();
        //return $this->testTheData($data);
    }
    
    /**
     *
     * Do the actual loading of the chapter page data
     * 
     * @param string array $data Array of content
     * @param string $basePath The base path to the files
     * @access private
     * 
     */
    private function doLoad($data, $basePath)
    {
        ini_set('max_execution_time', 300);
        $objFlow = $this->getObject('flowplayer', 'files');
        $objContextChapters = $this->getObject('db_contextcontent_contextchapter', 'contextcontent');
        $objContentTitles = $this->getObject('db_contextcontent_titles', 'contextcontent');
        $objContentOrder = $this->getObject('db_contextcontent_order', 'contextcontent');
        $chapter = stripslashes($this->getParam('chapter'));
        $language = 'en'; // NEED TO FIX THIS
        $chapterTitle = $objContextChapters->getContextChapterTitle($chapter);
        $headerscripts = NULL;
        foreach ($data as $item=>$value) {
            $videoUri = $basePath . $value['video'];
            $menutitle = $value['title'];
            //$preView = $objFlow->show($videoUri);
            $preView = $this->makeFilter($videoUri);
            $pagecontent = $value['body'] . "<br />"
              . $preView . "<br />";
            $parent = 'root';
            $titleId = $objContentTitles->addTitle('', $menutitle, $pagecontent, $language, $headerscripts);
            $pageId = $objContentOrder->addPageToContext($titleId, $parent, $this->contextCode, $chapter);

        }
    }

    // @DELETE
    public function testTheData($data, $basePath)
    {
        $ret = "";
        $objFlow = $this->getObject('flowplayer', 'files');
        foreach ($data as $item=>$value) {
            $videoUri = $basePath . $value['video'];
          
            $preView = $objFlow->show($videoUri);
            $video = '<a href="' . $videoUri . '">'
              . $value['video'] . '</a>';
            $ret .= $item . "<br />"
              . $value['title'] . "<br />"
              . $value['body'] . "<br />"
              . $video . "<br /><br />";
              //. $preView . "<br /><br />";
        }
        return $preView . $ret;
    }
    
    /**
     * 
     * Make the filter for FLV file and centre it
     * 
     * @param string $video The Video URL
     * @return string $video The Video URL as a filter 
     */
    private function makeFilter($video)
    {
        return "<p style=\"text-align: center;\">[FLV]" . $video . "[/FLV]</p>";
    }

    /**
     *
     * Create a dropdown list of chapters from which to choose
     * 
     * @return string Rendered dropdown (or boolean FALSE if not in context)
     * @access public
     *  
     */
    public function getChaptersDropdown()
    {
        if ($this->contextCode) {
            $objChapts = $this->getObject('dbkaimporter', 'kaimporter');
            $arChapts = $objChapts->getChapters($this->contextCode);
            $chDropdown = new dropdown('chapter');
            foreach ($arChapts as $chapt) {
                $titleForDd = $chapt['chaptertitle'];
                $paramForDd = $chapt['chapterid'];
                $chDropdown->addOption($paramForDd,$titleForDd);
            }
            return $chDropdown->show();
        } else {
            return FALSE;
        }

    }

    /**
     *
     * Get the URL root, which is the base for all the videos
     * 
     * @return string The root path
     * @access public
     * 
     */
    public function getUrlRoot()
    {
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        return $this->objSysConfig->getValue('KAIMPORTER_URLROOT', 'kaimporter');
    }

    /**
     *
     * Read the data file (JSON) from the video directory
     * 
     * @param string $fullPath The full URL path to the file
     * @return string array An array of items
     * @access public
     * 
     */
    public function readData($fullPath)
    {
        $jsonTxt = file_get_contents($fullPath);
        $data = json_decode($jsonTxt, true);
        return $data['items'];
    }

}
?>