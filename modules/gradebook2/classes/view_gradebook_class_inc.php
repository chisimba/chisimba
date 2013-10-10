<?php
class view_gradebook extends object
{
    public function init()
    {
        // language object.
        $this->objLanguage = $this->getObject('language', 'language');
    }
    private function loadElements()
    {
        //Load the link class
        $this->loadclass('link', 'htmlelements');
        //Load the altconfig class
        $objSysConfig = $this->getObject('altconfig', 'config');
        //Load DB Table weightedcolumn class
        $this->objWeightedColumn =& $this->getObject('dbgradebook2_weightedcolumn','gradebook2');
    }
    private function buildForm()
    {
        $this->appendArrayVar('headerParams', '
        <script type="text/javascript">
        var pageSize = 30;
        var uri = "' . str_replace('&amp;', '&', $this->uri(array(
            'module' => 'gradebook2',
            'action' => 'jsongetgrades','limit' => '30'
        ))) . '"; 
        var title= "'.ucWords($this->objLanguage->code2Txt('mod_gradebook2_gradecenter','gradebook2'))." ".ucWords($this->objLanguage->code2Txt('mod_gradebook2_wordfor', 'gradebook2'))." ".$this->objContext->getTitle( $this->contextCode ).' ('.$this->contextCode.')";
        var lang = new Array();
        lang["nologstodisplay"] =   "' . ucWords($this->objLanguage->code2Txt('mod_learningcontent_nologstodisplay', 'learningcontent')) . '";
        lang["displayingpage"] =   "' . $this->objLanguage->code2Txt('mod_learningcontent_displayingpage', 'learningcontent') . '";
        lang["wordof"] =   "' . $this->objLanguage->code2Txt('mod_learningcontent_wordof', 'learningcontent') . '";
        var baseuri = "' . $objSysConfig->getsiteRoot() . 'index.php";
         </script>');
        //Ext stuff
        $objExtJs = $this->getObject('extjs', 'ext');
        $objExtJs->show();

        //Get params column id and status if edit
        $id = $this->getParam('id', NULL);
        $action = $this->getParam('action', NULL);
        //Get column values if action is edit
        if (!empty($id)) {
          $colVals = $this->objWeightedColumn->listSingle($id);
          $colVals = $colVals[0];
        }
        //Create new table
        $objTable = new htmltable();
        $objTable->width = '100%';
        $objTable->attributes = " align='center' border='0'";
        $objTable->cellspacing = '5';
        //Add Heading
        $objHeading = &$this->getObject('htmlheading', 'htmlelements');
        $objHeading->type = 1;
        $objHeading->str = $this->objLanguage->languageText("mod_gradebook2_gradecenter","gradebook2");
        $objTable->startRow();
        $objTable->addCell($objHeading->show(), Null, 'top', 'left', '', 'colspan="6"');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell($this->objLanguage->languageText("mod_gradebook2_gradecenter","gradebook2"), Null, 'top', 'left');
        $objTable->endRow();

        return $objTable->show();
    }
    public function show()
    {
        return $this->buildForm();
    }
}
?>
