<?php
/**
 * Class to handle context tool elements.
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface.
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
 * @version    0.001
 * @package    schools
 * @author     Kevin Cyster kcyster@gmail.com
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
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
// end security check

/**
 * Class to handle blog elements
 *
 * This object can be used elsewhere in the system to render certain aspects of the interface
 *
 * @version    0.001
 * @package    schools
 * @author     Kevin Cyster kcyster@gmail.com
 * @copyright  2011 AVOIR
 * @license    http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @link       http://www.chisimba.com
 */
class ckeditorcontextpluginops extends object
{
    /**
     * 
     * Variable to hold the script string
     * 
     * @access public
     * @var string
     */
    public $script;
    
    /**
     *
     * Method to initialize the class
     * 
     * @access public
     * @return VOID 
     */
    public function init()
    {
        try {
            // Load core system objects.
            $this->objContext =  $this->getObject('dbcontext', 'context');
            $this->objUserContext = $this->getObject('usercontext', 'context');
            $this->objContextModules = $this->getObject('dbcontextmodules', 'context');
            $this->objContextContent = $this->getObject('db_contextcontent_contextchapter', 'contextcontent');
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objUser = $this->getObject('user', 'security');
            $this->userId = $this->objUser->userId();
            $this->objUserAdmin = $this->getObject('useradmin_model2', 'security');
            $this->objConfig = $this->getObject('altconfig', 'config');
            
            // Load html elements.
            $this->objIcon = $this->newObject('geticon', 'htmlelements');
            $this->objInput = $this->loadClass('textinput', 'htmlelements');
            $this->objTable = $this->loadClass('htmltable', 'htmlelements');
            $this->objFieldset = $this->loadClass('fieldset', 'htmlelements');
            $this->objDropdown = $this->loadClass('dropdown', 'htmlelements');
            $this->objForm = $this->loadClass('form', 'htmlelements');
        }
        catch(customException $e) {
            echo customException::cleanUp();
            die();
        }
    }

    /**
     *
     * Method to generate an error string for display
     * 
     * @access private
     * @param string $errorText The error string
     * @return string $string The formated error string
     */
    private function error($errorText)
    {
        $error = $this->objLanguage->languageText('word_error', 'system', 'WORD: word_error, not found');
        
        $this->objIcon->title = $error;
        $this->objIcon->alt = $error;
        $this->objIcon->setIcon('exclamation', 'png');
        $errorIcon = $this->objIcon->show();
        
        $string = '<span style="color: red">' . $errorIcon . '&nbsp;<b>' . $errorText . '</b></span>';
        return $string;
    }
    
    /**
     *
     * Method to generate the html for the user display template
     * 
     * @access public
     * @return string $string The html string to be sent to the template 
     */
    public function showHome()
    {
        $contextsLabel = $this->objLanguage->code2Txt('word_contexts', 'system', NULL, 'ERROR: word_contexts');
        $contetxtListLable = $this->objLanguage->code2Txt('mod_ckeditorcontextplugin_contextlist', 'ckeditorcontextplugin', NULL, 'ERROR: mod_ckeditorcontextplugin_contextlist');
        $linksLable = $this->objLanguage->code2Txt('mod_ckeditorcontextplugin_contextlinks', 'ckeditorcontextplugin', NULL, 'ERROR: mod_ckeditorcontextplugin_contextlinks');
        $selectContextLabel = $this->objLanguage->code2Txt('mod_ckeditorcontextplugin_selectcontext', 'ckeditorcontextplugin', NULL, 'ERROR: mod_ckeditorcontextplugin_selectcontext');
        $insertLabel = $this->objLanguage->languageText('word_insert', 'system', 'ERROR: word_insert');
        $cancelLabel = $this->objLanguage->languageText('word_cancel', 'system', 'ERROR: word_cancel');
        $filterLabel = $this->objLanguage->languageText('word_filters', 'system', 'ERROR: word_filters');
        $noteLabel = $this->objLanguage->languageText('mod_ckeditorcontextplugin_filternote', 'ckeditorcontextplugin', 'ERROR: mod_ckeditorcontextplugin_filternote');
        $filterListLabel = $this->objLanguage->languageText('mod_ckeditorcontextplugin_filterlist', 'ckeditorcontextplugin', 'ERROR: mod_ckeditorcontextplugin_filterlist');
        $selectFilterLabel = $this->objLanguage->languageText('mod_ckeditorcontextplugin_selectfilter', 'ckeditorcontextplugin', 'ERROR: mod_ckeditorcontextplugin_selectfilter');
        $applyLabel = $this->objLanguage->languageText('mod_ckeditorcontextplugin_applyfilter', 'ckeditorcontextplugin', 'ERROR: mod_ckeditorcontextplugin_applyfilter');
        
        $contexts = $this->objUserContext->getUserContext($this->userId);
        if (count($contexts) > 0)
        {
            $contextTab = array();
            $contextArray = array();
            foreach ($contexts as $contextCode)
            {
                $contextDetails = $this->objContext->getContextDetails($contextCode);
                if ($contextDetails['status'] != 'Unpublished')
                {
                    $contextArray[$contextCode] = $contextDetails['title'];
                }
            }
            $objDrop = new dropdown('contextcode');
            $objDrop->addOption('', $selectContextLabel);
            $objDrop->addFromArray($contextArray);
            $contextDrop = $objDrop->show();

            $objButton = new button('insert', $insertLabel);
            $objButton->setId('insert');
            $insertButton = $objButton->show();

            $objButton = new button('cancel', $cancelLabel);
            $objButton->setId('context_cancel');
            $cancelButton = $objButton->show();

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startRow();
            $objTable->addCell(ucfirst(strtolower($contetxtListLable)) . ': ', '200px', '', '', '', '', '');
            $objTable->addCell($contextDrop, '', '', '', '', '', '');
            $objTable->endRow();
            $formTable = $objTable->show();
            
            $formTable .= '<div id="plugins"></div>';
            $formTable .= '<div id="contextcontent"></div>';
            $formTable .= '<div id="viewcontent"></div>';
            
            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startRow();
            $objTable->addCell($insertButton . '&nbsp' . $cancelButton, '', '', '', '', '', '');
            $objTable->endRow();
            $formTable .= $objTable->show();

            $objForm = new form('contexts', $this->uri(array(
                'action' => 'insertcontext',
            )));
            $objForm->extra = ' enctype="multipart/form-data"';
            $objForm->addToForm($formTable);
            $form = $objForm->show();

            $objFieldset = new fieldset();
            $objFieldset->legend = '<b>' . ucfirst(strtolower($linksLable)) . '</b>';
            $objFieldset->contents =  $form;
            $contextFieldset = $objFieldset->show();

            $contextTab = array(
                'title' => ucfirst(strtolower($contextsLabel)),
                'content' => $contextFieldset,
            );
        }
        
        $filtersArray = $this->getFilters();
        $objDrop = new dropdown('filter');
        $objDrop->addOption('', $selectFilterLabel);
        $objDrop->addFromArray($filtersArray);
        $filterDrop = $objDrop->show();
        
        $objButton = new button('apply', $applyLabel);
        $objButton->setId('apply');
        $applyButton = $objButton->show();
        
        $objButton = new button('cancel', $cancelLabel);
        $objButton->setId('filters_cancel');
        $cancelButton = $objButton->show();
        
        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($this->error($noteLabel), '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($filterListLabel . ': ', '200px', '', '', '', '', '');
        $objTable->addCell($filterDrop, '', '', '', '', '', '');
        $objTable->endRow();
        $formTable = $objTable->show();
        
        $formTable .= '<div id="parameters"></div>';

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell($applyButton . '&nbsp' . $cancelButton, '', '', '', '', 'colspan="2"', '');
        $objTable->endRow();
        $formTable .= $objTable->show();

        $objForm = new form('filters', $this->uri(array(
            'action' => 'applyfilter',
        )));
        $objForm->extra = ' enctype="multipart/form-data"';
        $objForm->addToForm($formTable);
        $form = $objForm->show();
        
        $objFieldset = new fieldset();
        $objFieldset->legend = '<b>' . $filterLabel . '</b>';
        $objFieldset->contents =  $form;
        $filterFieldset = $objFieldset->show();

        $filterTab = array(
            'title' => $filterLabel,
            'content' => $filterFieldset,
        );

        $objTabs = $this->newObject('tabs', 'jquerycore');
        if (count($contexts) > 0)
        {
            $objTabs->addTab($contextTab);
        }
        $objTabs->addTab($filterTab);
        $string = $objTabs->show();
        $this->script = $objTabs->script;
         
        return $string;
    }
    
    /**
     *
     * Method to get the plugins for an ajax call
     * 
     * @access public
     * @param string $contextCode The code of the context to get plugins for
     * @return VOID
     */
    public function ajaxGetPlugins($contextCode)
    {
        $selectPluginLabel = $this->objLanguage->languageText('mod_ckeditorcontextplugin_selectplugin', 'ckeditorcontextplugin', 'ERROR: mod_ckeditorcontextplugin_selectplugin');
        $pluginsLabel = $this->objLanguage->languageText('word_plugins', 'system', 'ERROR: word_plugins');

        $plugins = $this->objContextModules->getContextModules($contextCode);
        if (!empty($plugins))
        {
            $pluginArray = array();
            foreach ($plugins as $plugin)
            {
                $name = $this->objContextModules->getModuleName($plugin);
                $pluginArray[$plugin] = ucfirst(strtolower($name));
            }            
            $objDrop = new dropdown('plugins');
            $objDrop->addOption('', $selectPluginLabel);
            $objDrop->addFromArray($pluginArray);
            $pluginDrop = $objDrop->show();

            $objTable = new htmltable();
            $objTable->cellpadding = '4';
            $objTable->startRow();
            $objTable->addCell(ucfirst(strtolower($pluginsLabel)) . ': ', '200px', '', '', '', '', '');
            $objTable->addCell($pluginDrop, '', '', '', '', '', '');
            $objTable->endRow();
            $pluginTable = $objTable->show();
            
            echo $pluginTable;
            die();
        }
        echo NULL;
        die();
    }

    /**
     *
     * Method to get the context content options for an ajax call
     * 
     * @access public
     * @return VOID
     */
    public function ajaxGetContentOptions()
    {
        $selectOptionLabel = $this->objLanguage->languageText('mod_ckeditorcontextplugin_selectoption', 'ckeditorcontextplugin', 'ERROR: mod_ckeditorcontextplugin_selectoption');
        $optionLabel = $this->objLanguage->code2Txt('mod_ckeditorcontextplugin_contentoptions', 'ckeditorcontextplugin', NULL, 'ERROR: mod_ckeditorcontextplugin_contentoptions');
        $listLabel = $this->objLanguage->languageText('mod_ckeditorcontextplugin_listchapters', 'ckeditorcontextplugin', 'ERROR: mod_ckeditorcontextplugin_listchapters');
        $viewLabel = $this->objLanguage->languageText('mod_ckeditorcontextplugin_viewchapter', 'ckeditorcontextplugin', 'ERROR: mod_ckeditorcontextplugin_viewchapter');

        $objDrop = new dropdown('option');
        $objDrop->addOption('', $selectOptionLabel);
        $objDrop->addOption('list', $listLabel);
        $objDrop->addOption('view', $viewLabel);
        $optionDrop = $objDrop->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($optionLabel)) . ': ', '200px', '', '', '', '', '');
        $objTable->addCell($optionDrop, '', '', '', '', '', '');
        $objTable->endRow();
        $optionTable = $objTable->show();

        echo $optionTable;
        die();
    }

    /**
     *
     * Method to get the chapters for an ajax call
     * 
     * @access public
     * @param string $contextCode The code of the context to get chapters for
     * @return VOID
     */
    public function ajaxGetChapters($contextCode)
    {
        $selectChapterLabel = $this->objLanguage->languageText('mod_ckeditorcontextplugin_selectchapter', 'ckeditorcontextplugin', 'ERROR: mod_ckeditorcontextplugin_selectchapter');
        $listChapterLabel = $this->objLanguage->languageText('mod_ckeditorcontextplugin_chapterlist', 'ckeditorcontextplugin', 'ERROR: mod_ckeditorcontextplugin_chapterlist');

        $chapters = $this->objContextContent->getContextChapters($contextCode);
        $objDrop = new dropdown('chapter');
        $objDrop->addOption('', $selectChapterLabel);
        $objDrop->addFromDB($chapters, 'chaptertitle', 'chapterid');
        $chapterDrop = $objDrop->show();

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($listChapterLabel)) . ': ', '200px', '', '', '', '', '');
        $objTable->addCell($chapterDrop, '', '', '', '', '', '');
        $objTable->endRow();
        $chapterTable = $objTable->show();

        echo $chapterTable;
        die();
    }
    
    /**
     *
     * Method to get the filters from the filters.xml file
     * 
     * @access public
     * @return array $filters The array of filters 
     */
    public function getFilters()
    {
        $xmlFile = $this->getResourcePath('filters.xml', 'ckeditorcontextplugin');
        $objFilters = simplexml_load_file($xmlFile);
        $filters = $objFilters->xpath("//filter");
        $filtersArray = array();
        foreach ($filters as $filter)
        {
            $filtersArray[(string) $filter->name] = (string) $filter->label;
        }
        return $filtersArray;
    }
    
    /**
     *
     * Method to get the filter parameters from the filters.xml file
     * 
     * @access public
     * @param string $filter The filter to get parameters for
     * @return array $params The array of parameters
     */
    public function getParams($filter)
    {
        $xmlFile = $this->getResourcePath('filters.xml', 'ckeditorcontextplugin');
        $objFilters = simplexml_load_file($xmlFile);
        $filters = $objFilters->xpath("//filter");
        $returnArray = array();
        foreach ($filters as $filterArray)
        {
            if ((string) $filterArray->name == $filter)
            {
                $params = $filterArray->params;
                $instructions = (string) $filterArray->instructions;
                $returnArray['instructions'] = $instructions;
                if (count($params->param) > 0)
                {
                    foreach ($params->param as $param)
                    {
                        $paramsArray[(string) $param['name']] = (string) $param;
                    }
                    $returnArray['params'] = $paramsArray;
                }
                else
                {
                    $param = $filterArray->input->inputparam;
                    $paramsArray[(string) $param['name']] = (string) $param;
                    $returnArray['params'] = $paramsArray;
                }
            }
        }
        return $returnArray;
    }
    
    /**
     *
     * Method to get the chapters for an ajax call
     * 
     * @access public
     * @param string $contextCode The code of the context to get chapters for
     * @return VOID
     */
    public function ajaxGetParams($filter)
    {
        $parameterLabel = $this->objLanguage->languageText('word_parameter', 'system', 'ERROR: word_parameter');
        $paramListLabel = $this->objLanguage->languageText('mod_ckeditorcontextplugin_parameterlist', 'ckeditorcontextplugin', 'ERROR: mod_ckeditorcontextplugin_parameterlist');
        $selectParamLabel = $this->objLanguage->languageText('mod_ckeditorcontextplugin_selectparameter', 'ckeditorcontextplugin', 'ERROR: mod_ckeditorcontextplugin_selectparameter');

        $params = $this->getParams($filter);

        if (count($params['params']) > 1)
        {
            $objDrop = new dropdown('param');
            $objDrop->addOption('', $selectParamLabel);
            $objDrop->addFromArray($params['params']);
            $param = $objDrop->show();
            
            $paramLabel = $paramListLabel;
        }
        else
        {
            $objInput = new textinput('param', '', '', '50');
            $param = $objInput->show();
            
            $paramLabel = $parameterLabel;
        }
        
        $string = '<strong>' . $params['instructions'] . '</strong><br />';

        $objTable = new htmltable();
        $objTable->cellpadding = '4';
        $objTable->startRow();
        $objTable->addCell(ucfirst(strtolower($paramLabel)) . ': ', '200px', '', '', '', '', '');
        $objTable->addCell($param, '', '', '', '', '', '');
        $objTable->endRow();
        $paramTable = $objTable->show();
        $string .= $paramTable;
        
        echo $string;
        die();
    }    
    
    /**
     *
     * Method to show the ckeditorcontextplugin dialog
     * 
     * @access public
     * @return string $string The ckeditorcontextplugin dialog 
     */
    public function ajaxCreateDialog()
    {
        $ckeditorcontextpluginLabel = ucfirst(strtolower($this->objLanguage->code2Txt('mod_ckeditorcontextplugin_toolbarname', 'ckeditorcontextplugin', NULL,'ERROR: mod_ckeditorcontextplugin_toolbarname')));
        
        $objDialog = $this->newObject('dialog', 'jquerycore');        
        $objDialog->setCssId('dialog_ckeditorcontextplugin');
        $objDialog->setTitle($ckeditorcontextpluginLabel);
        $objDialog->setContent($this->showHome());
        $objDialog->unsetButtons();
        $string = $objDialog->show();
        $this->script .= $objDialog->script;
        $this->script .= $this->getJavascriptFile('ckeditorcontextplugin.js', 'ckeditorcontextplugin');

        echo $string;
        echo $this->script;
        die();
    }
}
?>