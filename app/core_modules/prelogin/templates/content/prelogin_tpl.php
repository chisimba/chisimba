<?php
/*Template for prelogin*/

/************ LEFT COLUMN **************/
$leftContent ='';
$leftBlocks = $this->objPLBlocks->getVisibleBlocks('left');
if (isset($leftBlocks)) {
	if (is_array($leftBlocks)) {
		foreach ($leftBlocks as $block) {
			if ($block['isblock']== $this->TRUE) {
					$leftContent .= $this->objBlocks->showBlock($block['blockname'], $block['blockmodule']);
			} else {
				$objFeatureBox = &$this->newObject('featurebox', 'navigation');
                $leftContent .= $objFeatureBox->show($block['title'], html_entity_decode($block['content'],ENT_QUOTES));
			}
		}
	}
}

/************ MIDDLE COLUMN **************/
$middleContent = '';
$middleBlocks = $this->objPLBlocks->getVisibleBlocks('middle');
if (isset($middleBlocks)) {
	if (is_array($middleBlocks)) {
		foreach ($middleBlocks as $block) {
			if ($block['isblock']==$this->TRUE) {
					$middleContent .= $this->objBlocks->showBlock($block['blockname'], $block['blockmodule'],'none');
			} else {
				$midContent = html_entity_decode($block['content'],ENT_QUOTES);
				$middleContent .= "<h3>{$block['title']}</h3>$midContent";
			}
		}
	}
}

/************ RIGHT COLUMN **************/
$rightContent ='';
$rightBlocks = $this->objPLBlocks->getVisibleBlocks('right');
if (isset($rightBlocks)) {
	if (is_array($rightBlocks)) {
		foreach ($rightBlocks as $block) {
			if ($block['isblock']==$this->TRUE) {
					$rightContent .= $this->objBlocks->showBlock($block['blockname'], $block['blockmodule']);
			} else {
				$objFeatureBox = &$this->newObject('featurebox', 'navigation');
                $rightContent .= $objFeatureBox->show($block['title'], html_entity_decode($block['content'],ENT_QUOTES));
			}
		}
	}
}

$cssLayout = &$this->newObject('csslayout', 'htmlelements');
$cssLayout->setNumColumns(3);
$cssLayout->putThreeColumnFixInHeader();
$cssLayout->setLeftColumnContent($leftContent);
$cssLayout->setMiddleColumnContent($middleContent);
$cssLayout->setRightColumnContent($rightContent);

echo $cssLayout->show();

?>