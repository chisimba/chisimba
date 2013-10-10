<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* The class to display a list of fomat codes
*
* @author Kevin Cyster
*/
class block_formatting extends object
{
    /*
    * @var object $objIcon: The geticon class in the htmlelements module
    * @access private
    */
    private $objIcon;

    /*
    * @var object $objLanguage: The language class in the language module
    * @access private
    */
    private $objLanguage;

    /*
    * @var string $title: The title of the block
    * @access public
    */
    public $title;

    /*
    * @var string $heading: The heading of the block
    * @access private
    */
    private $heading;

    /**
    * Constructor for the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        // load html element classes
        $this->loadClass('htmltable', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->loadClass('layer', 'htmlelements');
        $this->objIcon = $this->getObject('geticon', 'htmlelements');
        
        // system classes
        $this->objLanguage = $this->getObject('language', 'language');

        // language items
        $title = $this->objLanguage->languageText('mod_messaging_wordformatting', 'messaging');
        $label = $this->objLanguage->languageText('mod_messaging_formatting', 'messaging');  
        $help = $this->objLanguage->languageText('mod_messaging_help', 'messaging');
        
        // help icon
        $this->objIcon->setIcon('help_small');
        $this->objIcon->align = 'top';
        $this->objIcon->title = $help;
        $this->objIcon->extra = 'style="cursor: help;" onclick="javascript:
            var el_Div = $(\'formatHelpDiv\');
            jsShowHelp(el_Div);"';
        $helpIcon = $this->objIcon->show();
        
        // help layer
        $objLayer = new layer();
        $objLayer->id = 'formatHelpDiv';
        $objLayer->display = 'none';
        $objLayer->addToStr('<font size="1">'.$label.'</font>');
        $helpLayer = $objLayer->show();
        
        // title
        $this->title = $title.'&nbsp;'.$helpIcon.$helpLayer;                
    }

    /**
    * Method to output a block with format codes
    * 
    * @access public
    * @return string $str: The output string
    */
    public function show()
	{
        // language items
        $bold = $this->objLanguage->languageText('mod_messaging_bold', 'messaging');
        $underline = $this->objLanguage->languageText('mod_messaging_underline', 'messaging');
        $italics = $this->objLanguage->languageText('mod_messaging_italics', 'messaging');
        $colour = $this->objLanguage->languageText('mod_messaging_colour', 'messaging');
        $size = $this->objLanguage->languageText('mod_messaging_size', 'messaging');        
        $apply = $this->objLanguage->languageText('mod_messaging_apply', 'messaging');
        $colourTitle = $this->objLanguage->languageText('mod_messaging_colourtitle', 'messaging');
        $fontSize = $this->objLanguage->languageText('mod_messaging_fontsize', 'messaging');
        $fontTitle = $this->objLanguage->languageText('mod_messaging_fonttitle', 'messaging');
        $fontStyle = $this->objLanguage->languageText('mod_messaging_fontstyle', 'messaging');
        $styleTitle = $this->objLanguage->languageText('mod_messaging_styletitle', 'messaging');
        
        // style expander
        $objLink = new link('#');
        $objLink->link = $fontStyle;
        $objLink->title = $styleTitle;
        $objLink->extra = ' onclick="javascript:jsExpandStyle()"';
        $styleLink = $objLink->show();
        
        $str = $styleLink;
        
        // style links
        $objLink = new link('#');
        $objLink->link = $apply;
        $objLink->extra = ' onclick="javascript:jsGetSelText(\'bold\')"';
        $boldLink = $objLink->show();
        
        $objLink = new link('#');
        $objLink->link = $apply;
        $objLink->extra = ' onclick="javascript:jsGetSelText(\'underline\')"';
        $underlineLink = $objLink->show();
        
        $objLink = new link('#');
        $objLink->link = $apply;
        $objLink->extra = ' onclick="javascript:jsGetSelText(\'italics\')"';
        $italicLink = $objLink->show();
        
        // Format table
        $objTable = new htmltable();
        $objTable->cellspacing = '2';
        $objTable->cellpadding = '2';
        
        $objTable->startRow();
        $objTable->addCell('[B]&nbsp;<b>'.$bold.'</b>&nbsp;[/B]', '', '', '', '', '');
        $objTable->addCell($boldLink, '5%', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('[U]&nbsp;<u>'.$underline.'</u>&nbsp;[/U]', '', '', '', '', '');
        $objTable->addCell($underlineLink, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('[I]&nbsp;<i>'.$italics.'</i>&nbsp;[/I]', '', '', '', '', '');
        $objTable->addCell($italicLink, '', '', '', '', '');
        $objTable->endRow();
        
        $styleTable = $objTable->show();
        
        $objLayer = new layer();
        $objLayer->id = 'styleDiv';
        $objLayer->addToStr($styleTable);
        $objLayer->display = 'none';
        $styleLayer = $objLayer->show();
        $str .= $styleLayer;

        
        // Colour expander
        $objLink = new link('#');
        $objLink->link = $colour;
        $objLink->title = $colourTitle;
        $objLink->extra = ' onclick="javascript:jsExpandColour()"';
        $colourLink = $objLink->show();
        
        $str .= '<br/>'.$colourLink;
        
        // Colour links
        $objLink = new link('#');
        $objLink->link = $apply;
        $objLink->extra = ' onclick="javascript:jsGetSelText(\'red\')"';
        $redLink = $objLink->show();
        
        $objLink = new link('#');
        $objLink->link = $apply;
        $objLink->extra = ' onclick="javascript:jsGetSelText(\'orange\')"';
        $orangeLink = $objLink->show();
        
        $objLink = new link('#');
        $objLink->link = $apply;
        $objLink->extra = ' onclick="javascript:jsGetSelText(\'yellow\')"';
        $yellowLink = $objLink->show();
        
        $objLink = new link('#');
        $objLink->link = $apply;
        $objLink->extra = ' onclick="javascript:jsGetSelText(\'green\')"';
        $greenLink = $objLink->show();
        
        $objLink = new link('#');
        $objLink->link = $apply;
        $objLink->extra = ' onclick="javascript:jsGetSelText(\'blue\')"';
        $blueLink = $objLink->show();
        
        $objLink = new link('#');
        $objLink->link = $apply;
        $objLink->extra = ' onclick="javascript:jsGetSelText(\'purple\')"';
        $purpleLink = $objLink->show();
        
        $objLink = new link('#');
        $objLink->link = $apply;
        $objLink->extra = ' onclick="javascript:jsGetSelText(\'pink\')"';
        $pinkLink = $objLink->show();
        
        // Colour table
        $objTable = new htmltable();
        $objTable->cellspacing = '2';
        $objTable->cellpadding = '2';
        
        $objTable->startRow();
        $objTable->addCell('[RED]&nbsp;<font style="color: red;">'.$colour.'</font>&nbsp;[/RED]', '', '', '', '', '');
        $objTable->addCell($redLink, '5%', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('[ORANGE]&nbsp;<font style="color: orange;">'.$colour.'</font>&nbsp;[/ORANGE]', '', '', '', '', '');
        $objTable->addCell($orangeLink, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('[YELLOW]&nbsp;<font style="color: yellow;">'.$colour.'</font>&nbsp;[/YELLOW]', '', '', '', '', '');
        $objTable->addCell($yellowLink, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('[GREEN]&nbsp;<font style="color: green;">'.$colour.'</font>&nbsp;[/GREEN]', '', '', '', '', '');
        $objTable->addCell($greenLink, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('[BLUE]&nbsp;<font style="color: blue;">'.$colour.'</font>&nbsp;[/BLUE]', '', '', '', '', '');
        $objTable->addCell($blueLink, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('[PURPLE]&nbsp;<font style="color: purple;">'.$colour.'</font>&nbsp;[/PURPLE]', '', '', '', '', '');
        $objTable->addCell($purpleLink, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('[PINK]&nbsp;<font style="color: pink;">'.$colour.'</font>&nbsp;[/PINK]', '', '', '', '', '');
        $objTable->addCell($pinkLink, '', '', '', '', '');
        $objTable->endRow();
        
        $colourTable = $objTable->show();
        
        $objLayer = new layer();
        $objLayer->id = 'colourDiv';
        $objLayer->addToStr($colourTable);
        $objLayer->display = 'none';
        $colourLayer = $objLayer->show();
        $str .= $colourLayer;

        // font expander
        $objLink = new link('#');
        $objLink->link = $fontSize;
        $objLink->title = $fontTitle;
        $objLink->extra = ' onclick="javascript:jsExpandFont()"';
        $fontLink = $objLink->show();
        
        $str .= '<br />'.$fontLink;
        
        // font links
        $objLink = new link('#');
        $objLink->link = $apply;
        $objLink->extra = ' onclick="javascript:jsGetSelText(\'s1\')"';
        $s1Link = $objLink->show();
        
        $objLink = new link('#');
        $objLink->link = $apply;
        $objLink->extra = ' onclick="javascript:jsGetSelText(\'s2\')"';
        $s2Link = $objLink->show();
        
        $objLink = new link('#');
        $objLink->link = $apply;
        $objLink->extra = ' onclick="javascript:jsGetSelText(\'s3\')"';
        $s3Link = $objLink->show();
        
        $objLink = new link('#');
        $objLink->link = $apply;
        $objLink->extra = ' onclick="javascript:jsGetSelText(\'s4\')"';
        $s4Link = $objLink->show();
        
        // Colour table
        $objTable = new htmltable();
        $objTable->cellspacing = '2';
        $objTable->cellpadding = '2';
        

        $objTable->startRow();
        $objTable->addCell('[S1]&nbsp;<font size="2">'.$size.'</font>&nbsp;[/S1]', '', '', '', '', '');
        $objTable->addCell($s1Link, '5%', '', '', '', '');
        $objTable->endRow();

        $objTable->startRow();
        $objTable->addCell('[S2]&nbsp;<font size="3">'.$size.'</font>&nbsp;[/S2]', '', '', '', '', '');
        $objTable->addCell($s2Link, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('[S3]&nbsp;<font size="4">'.$size.'</font>&nbsp;[/S3]', '', '', '', '', '');
        $objTable->addCell($s3Link, '', '', '', '', '');
        $objTable->endRow();
        $objTable->startRow();
        $objTable->addCell('[S4]&nbsp;<font size="5">'.$size.'</font>&nbsp;[/S4]', '', '', '', '', '');
        $objTable->addCell($s4Link, '', '', '', '', '');
        $objTable->endRow();

        $fontTable = $objTable->show();
        
        $objLayer = new layer();
        $objLayer->id = 'fontDiv';
        $objLayer->addToStr($fontTable);
        $objLayer->display = 'none';
        $fontLayer = $objLayer->show();
        $str .= $fontLayer;

        return $str.'<br/>';
    }
}
?>