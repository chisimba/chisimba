<?php
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
/**
 * An module to format the logged data in a user friendly manner.
 *
 * @author    Megan Watson
 * @copyright (c) 2007 University of the Western Cape
 * @package   logger
 * @version   0.1
 */
class logdisplay extends object
{
    /**
     * Constructor method
     */
    public function init()
    {
        try {
            $this->logShow = $this->getObject('logshow', 'logger');
            
            $this->objUser = $this->getObject('user', 'security');
            $this->objLanguage = $this->getObject('language', 'language');
            $this->objCatalogue = $this->getObject('catalogueconfig', 'modulecatalogue');
            
            $this->objFeatureBox = $this->newObject('featurebox', 'navigation');
            $this->loadClass('htmlheading', 'htmlelements');
            $this->loadClass('htmltable', 'htmlelements');
            $this->loadClass('link', 'htmlelements');
            $this->loadClass('windowpop', 'htmlelements');
        }catch(Exception $e) {
            throw customException($e->getMessage());
            exit();
        }
    }
    
    /**
    * Method to display the list of modules and their usage
    *
    * @access public
    * @return string html
    */
    public function show()
    {
        $data = $this->logShow->showStatsByModule();
        
        $hdModules = $this->objLanguage->languageText('mod_logger_statisticsbymodule', 'logger');
        $lbModule = $this->objLanguage->languageText('phrase_modulename');
        $lbHits = $this->objLanguage->languageText('word_hits');
        $lbUsers = $this->objLanguage->languageText('phrase_numberofusers');
        $lnDescription = $this->objLanguage->languageText('phrase_viewmoduledescription');
        
        $objHead = new htmlheading();
        $objHead->str = ucwords($hdModules);
        $objHead->type = 1;
        $str = $objHead->show();
        
        if(!empty($data)){
            $headerParams=$this->getJavascriptFile('new_sorttable.js','htmlelements');
            $this->appendArrayVar('headerParams', $headerParams);
            
            $objTable = new htmltable();
            $objTable->cellpadding = '5';
            $objTable->id = 'newtable';
            $objTable->css_class = 'sorttable';
            $objTable->row_attributes = 'name="row_'.$objTable->id.'"';
            
            $objTable->startRow();
            $objTable->addCell($lbModule, '60%', '','', 'heading');
            $objTable->addCell($lbHits, '10%', '','', 'heading');
            $objTable->addCell($lbUsers, '10%', '','', 'heading');
            $objTable->addCell('', '20%', '','', 'heading');
            $objTable->endRow();
                        
            foreach($data as $item){
                $module = $item['module'];
                
                $objPop = new windowpop();
                $objPop->set('location', $this->uri(array('action' => 'showmoduleinfo', 'mod' => $module)));
                $objPop->set('linktext', $lnDescription);
                $objPop->set('width', '250');
                $objPop->set('height', '300');
                $objPop->set('left', '300');
                $objPop->set('top', '400');
                $objPop->set('resizable', 'yes');
                $link = $objPop->show();
                
                $row = array();
                $row[] = $module;
                $row[] = $item['calls'];
                $row[] = $item['users'];
                $row[] = $link;
                
                $objTable->row_attributes = "name='row_".$objTable->id."' onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className=''; \"";
                $objTable->addRow($row);
            }
            $str .= $objTable->show();
        }
        
        return $str.'<br />';
    }
    
    /**
    * Method to display the list of modules and their usage
    *
    * @access public
    * @return string html
    */
    public function statsByUser()
    {
        $data = $this->logShow->showStatsByModule();
        
        $hdModules = $this->objLanguage->languageText('mod_logger_statisticsbymodule', 'logger');
        $lbModule = $this->objLanguage->languageText('phrase_modulename');
        $lbHits = $this->objLanguage->languageText('word_hits');
        $lbUsers = $this->objLanguage->languageText('phrase_numberofusers');
        $lnDescription = $this->objLanguage->languageText('phrase_viewmoduledescription');
        
        $objHead = new htmlheading();
        $objHead->str = ucwords($hdModules);
        $objHead->type = 1;
        $str = $objHead->show();
        
        if(!empty($data)){
            $headerParams=$this->getJavascriptFile('new_sorttable.js','htmlelements');
            $this->appendArrayVar('headerParams', $headerParams);
            
            $objTable = new htmltable();
            $objTable->cellpadding = '5';
            $objTable->id = 'newtable';
            $objTable->css_class = 'sorttable';
            $objTable->row_attributes = 'name="row_'.$objTable->id.'"';
            
            $objTable->startRow();
            $objTable->addCell($lbModule, '60%', '','', 'heading');
            $objTable->addCell($lbHits, '10%', '','', 'heading');
            $objTable->addCell($lbUsers, '10%', '','', 'heading');
            $objTable->addCell('', '20%', '','', 'heading');
            $objTable->endRow();
                        
            foreach($data as $item){
                $module = $item['module'];
                
                $objPop = new windowpop();
                $objPop->set('location', $this->uri(array('action' => 'showmoduleinfo', 'mod' => $module)));
                $objPop->set('linktext', $lnDescription);
                $objPop->set('width', '250');
                $objPop->set('height', '300');
                $objPop->set('left', '300');
                $objPop->set('top', '400');
                $objPop->set('resizable', 'yes');
                $link = $objPop->show();
                
                $row = array();
                $row[] = $module;
                $row[] = $item['calls'];
                $row[] = $item['users'];
                $row[] = $link;
                
                $objTable->row_attributes = "name='row_".$objTable->id."' onmouseover=\"this.className='tbl_ruler';\" onmouseout=\"this.className=''; \"";
                $objTable->addRow($row);
            }
            $str .= $objTable->show();
        }
        
        return $str.'<br />';
    }
    
    /**
    * Method to display the module description
    *
    * @access public
    * @return string html
    */
    public function moduleInfo($module)
    {
        $lnClose = $this->objLanguage->languageText('word_close');
        $modArr = $this->objCatalogue->getModuleDescription($module);
        $modArr2 = $this->objCatalogue->getModuleName($module);
        
        $description = $modArr[0];
        $modName = $modArr2[0];
        
        $objLink = new link('#');
        $objLink->link = $lnClose;
        $objLink->extra = 'onclick = "javascript: window.close()"';
        $description .= '<p align="center">'.$objLink->show().'</p>';
        
        return $this->objFeatureBox->showContent($modName, $description);
    }
    
    /**
    * Method to display the left menu with the index
    *
    * @access public
    * @return string html
    */
    public function leftMenu()
    {
        $hdMenu = $this->objLanguage->languageText('word_menu');
        $lnModules = $this->objLanguage->languageText('mod_logger_statisticsbymodule', 'logger');
        $lnUser = $this->objLanguage->languageText('mod_logger_statisticsbyuser', 'logger');
        $lnPages = $this->objLanguage->languageText('mod_logger_pagespermodule', 'logger');
        
        $str = '<ul>';
        
        $objLink = new link($this->uri(''));
        $objLink->link = $lnModules;
        $str .= '<li>'.$objLink->show().'</li>';
        /*
        $objLink = new link($this->uri(array('action' => 'userstats')));
        $objLink->link = $lnUser;
        $str .= '<li>'.$objLink->show().'</li>';
        
        $objLink = new link($this->uri(''));
        $objLink->link = $lnPages;
        $str .= '<li>'.$objLink->show().'</li>';
        */
        $str .= '</ul>';
        
        return $this->objFeatureBox->show($hdMenu, $str);
    }
}
?>