<?php

/**
 * this creates the control panel
 *
 * @author davidwaf
 */
class cpanel extends object {

    function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objConfig = $this->getObject('altconfig', 'config');
    }

    /**
     * create the control panel
     * @return type 
     */
    function createPanel() {
        $skinName = $this->objConfig->getdefaultSkin();
        $cPanelTable = $this->getObject("htmltable", "htmlelements");

        $cPanelTable->startRow();

        $cplink = new link($this->uri(array(),"toolbar"));
        $cplink->link = '<img  src="skins/' . $skinName . '/images/controlpanel.png" align="bottom"><br/>' . $this->objLanguage->languageText('mod_oer_siteadministration', 'oer');
        $cPanelTable->addCell($cplink->show(), null, "top");

        
        $cplink = new link($this->uri(array("action" => "viewthemes")));
        $cplink->link = '<img  src="skins/' . $skinName . '/images/product_theme.png" align="bottom"><br/>' . $this->objLanguage->languageText('mod_oer_productthemes', 'oer');
        $cPanelTable->addCell($cplink->show(), null, "top");

        $cplink = new link($this->uri(array("action" => "viewkeywords")));
        $cplink->link = '<img  src="skins/' . $skinName . '/images/keyword.png" align="bottom"><br/>' . $this->objLanguage->languageText('mod_oer_keywords', 'oer');
        $cPanelTable->addCell($cplink->show(), null, "top");



        $cPanelTable->endRow();

        $cPanelTable->startRow();

        
        $cplink = new link($this->uri(array(), "oeruserdata"));
        $cplink->link = '<img  src="skins/' . $skinName . '/images/useradmin.png" align="bottom"><br/>' . $this->objLanguage->languageText('mod_oer_users', 'oer');
        $cPanelTable->addCell($cplink->show(), null, "top");
        
        $cplink = new link($this->uri(array("action" => "institutionlisting")));
        $cplink->link = '<img  src="skins/' . $skinName . '/images/institutions.png" align="bottom"><br/>' . $this->objLanguage->languageText('mod_oer_institutions', 'oer');
        $cPanelTable->addCell($cplink->show(), null, "top");

        $cPanelTable->endRow();




        return $cPanelTable->show();
    }

}

?>
