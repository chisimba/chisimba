<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* 
* The class provides a base content block for use by all
* contentblocks to render their contents
*
* @author Paul Mungai
*
*/
class contentblockbase extends object
{
    public $title;
    private $objDb;
    private $objLanguage;
    public $blockContents;
    
    /**
    * Constructor for the class
    */
    public function init()
    {
    	//Create an instance of the contentblocks DBtable object
        $this->objDb = $this->getObject("dbcontentblocks", "contentblocks");
        //Create an instance of the language object
        $this->objLanguage = &$this->getObject("language", "language");
        // Don't wrap content block titles.
        $this->wrapStr = FALSE;
    }
    
    /**
    * Method to render the block with the text item
    * @param string $textItem The content to render
    * @access public
    *
    */
    public function setData($textItem)
    {
        $ar = $this->objDb->getRow("blockid", $textItem);
        
        if (count($ar) > 0 ) {
            $this->showTitle = $ar['show_title'];
            if ($this->showTitle=="1") {
                $this->title = $ar['title'];
            } else {
                $this->title = FALSE;
            }
            $cssId="";
            $cssClass="";
            $divEnd ="";
            $divStart = "";
            $useDiv = FALSE;
            $this->cssId = $ar['css_id'];
            if ($this->cssId !== "" && $this->cssId !== NULL) {
                $cssId = " id='$this->cssId' ";
                $useDiv = TRUE;
            }
            $this->cssClass = $ar['css_class'];
            if ($this->cssClass !==""  && $this->cssClass !== NULL) {
                $cssClass=" class='$this->cssClass'";
                $useDiv = TRUE;
            }
            $objWashout = $this->getObject("washout", "utilities");
            $ret = $objWashout->parseText($ar['blocktext']);
            if ($useDiv) {
                $ret = "<div $cssClass>$ret</div>";
            }
            $this->blockContents = $ret;
        } else {
            $this->title = $textItem;
            $this->blockContents = $this->objLanguage->languageText("mod_contentblocks_nocontent", "contentblocks");
        }
        return TRUE;
    }
    /**
     * Method to render the block with the text item
     * @param string $id Unique identifier of the content to render
     * @access public
     *
     */
    public function setDataArr($id) {
        $arrData = array();
        $arrData = $this->objDb->getBlockById($id);        
        
        if (count($arrData) > 0) {
            $ar = $arrData[0];
            $this->showTitle = $ar['show_title'];
            if ($this->showTitle == "1") {
                $this->title = $ar['title'];
            } else {
                $this->title = FALSE;
            }
            $cssId = "";
            $cssClass = "";
            $divEnd = "";
            $divStart = "";
            $useDiv = FALSE;
            $this->cssId = $ar['css_id'];
            if ($this->cssId !== "" && $this->cssId !== NULL) {
                $cssId = " id='$this->cssId' ";
                $useDiv = TRUE;
            }
            $this->cssClass = $ar['css_class'];
            if ($this->cssClass !== "" && $this->cssClass !== NULL) {
                $cssClass = " class='$this->cssClass'";
                $useDiv = TRUE;
            }
            $objWashout = $this->getObject("washout", "utilities");
            $ret = $objWashout->parseText($ar['blocktext']);
            if ($useDiv) {
                $ret = "<div $cssClass>$ret</div>";
            }
            $this->blockContents = $ret;
            $dataArr = array(
                "cssId" => $this->cssId,
                "cssClass" => $this->cssClass,
                "title" => $this->title,
                "blockContents" => $this->blockContents,
                "show_title" => $ar["show_title"],
                "blockType" => $ar["show_title"],
            );
        } else {
            $this->title = $id;
            $this->blockContents = $this->objLanguage->languageText("mod_contentblocks_nocontent", "contentblocks");
            $dataArr = array(
                "title" => $this->title,
                "blockContents" => $this->blockContents
            );
        }
        return $dataArr;
    }
}
?>