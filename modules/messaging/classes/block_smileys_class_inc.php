<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
	die("You cannot view this page directly");
}
// end security check

/**
* The class to display a block for smiley icons
*
* @author Kevin Cyster
*/
class block_smileys extends object
{
    /*
    * @var object $objPopup: The windowpop class in the htmlelements module
    * @access private
    */
    private $objPopup;

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
    * @var array $shortList: An associated array containg the smileys name and code
    * @access private
    */
    private $shortList;
    
    /**
    * Constructor for the class
    *
    * @access public
    * @return
    */
    public function init()
    {
        // load html element classes
        $this->loadClass('layer', 'htmlelements');
        $this->loadClass('htmltable', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
        $this->objPopup = $this->getObject('windowpop', 'htmlelements');
        $this->objIcon = $this->getObject('geticon', 'htmlelements');
        
        // system classes
        $this->objLanguage = $this->getObject('language', 'language');
        
        // language items
        $title = $this->objLanguage->languageText('mod_messaging_wordsmileys', 'messaging');
        $label = $this->objLanguage->languageText('mod_messaging_smileys', 'messaging');  
        $more = $this->objLanguage->languageText('mod_messaging_moresmileys', 'messaging');  
        $help = $this->objLanguage->languageText('mod_messaging_help', 'messaging');
        
        // help icon
        $this->objIcon->setIcon('help_small');
        $this->objIcon->align = 'top';
        $this->objIcon->title = $help;
        $this->objIcon->extra = 'style="cursor: help;" onclick="javascript:
            var el_Div = $(\'smileyHelpDiv\');
            jsShowHelp(el_Div);"';
        $helpIcon = $this->objIcon->show();
        
        // help layer
        $objLayer = new layer();
        $objLayer->id = 'smileyHelpDiv';
        $objLayer->display = 'none';
        $objLayer->addToStr('<font size="1">'.$label.$more.'</font>');
        $helpLayer = $objLayer->show();
        
        // title
        $this->title = $title.'&nbsp;'.$helpIcon.$helpLayer;        

        // smiley icon array
        $this->shortList = array(
            'angry' => 'X-(',
            'cheeky' => ':-P',
            'confused' => ':-/',
            'cool' => 'B-)',
            'evil' => '>:-)',
            'idea' => '*-:-)',
            'grin' => ':-D',
            'sad' => ':-(',
            'smile' => ':-)',
            'wink' => ';-)',
        );
    }

    /**
    * Method to output a block with smiley icons
    *
    * @access public
    * @return string $str: The output string
    */
    public function show()
	{
        // language items
        $moreLabel = $this->objLanguage->languageText('mod_messaging_wordmore', 'messaging');
        $moreTitleLabel = $this->objLanguage->languageText('mod_messaging_smileytitle', 'messaging');
        
        // popup link for more smiley icons
        $this->objPopup->windowPop();
        $this->objPopup->title = $moreTitleLabel;
        $this->objPopup->set('location', $this->uri(array(
            'action'=>'moresmileys',
        )));
        $this->objPopup->set('linktext', '[...'.$moreLabel.'...]');
        $this->objPopup->set('width', '650');
        $this->objPopup->set('height', '546');
        $this->objPopup->set('left', '100');
        $this->objPopup->set('top', '100');
        $this->objPopup->set('scrollbars', 'yes');
        $this->objPopup->putJs(); // you only need to do this once per page
        $morePopup = $this->objPopup->show();

        // main table
        $objTable = new htmltable();
        $objTable->cellspacing = '2';
        $objTable->cellpadding = '2';
        $array = array_chunk($this->shortList, '2', TRUE);
        foreach($array as $line){
            $objTable->startRow();
            foreach($line as $smiley => $code){
                $array = array(
                    'icon' => $smiley,
                );
                $iconLabel = $this->objLanguage->code2Txt('mod_messaging_icon', 'messaging', $array);

                $this->objIcon->setIcon($smiley, 'gif', 'icons/smileys/');
                $this->objIcon->title = $iconLabel;
                $this->objIcon->extra = ' id="'.$smiley.'" style="cursor: pointer;" onclick="javascript:jsInsertBlockSmiley(this.id);"';
                $smileyIcon = $this->objIcon->show();
                
                $objTable->addCell($smileyIcon, '', '', 'center', '', '');
                $objTable->addCell('<nobr>'.$code.'</nobr>', '', '', 'center', '', '');
            }
            $objTable->endRow();
        }
        $objTable->startRow();
        $objTable->addCell($morePopup, '', '', 'center', '', 'colspan="4"');
        $objTable->endRow();
        $smileyTable = $objTable->show();
        $str = $smileyTable;  
              
        return $str;
    }
}
?>