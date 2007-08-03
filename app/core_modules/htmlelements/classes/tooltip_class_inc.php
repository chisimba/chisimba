<?php

/**
* Class for tooltips.
* Note: This tooltip JS lib is compatible with and requires prototype 1.5.0.
*
* This class loads the JavaScript for the tooltip JS lib and allows developers to
* insert their own tooltips into their modules.
*
* Example:
*
* $tooltipHelp =& $this->getObject('tooltip','htmlelements');
* $tooltipHelp->setCaption('Help');
* $tooltipHelp->setText('Some help text...');
* $tooltipHelp->setCursor('help');
* echo $tooltipHelp->show();
*
* @author  Jeremy O'Connor
* @package htmlelements
*/
class tooltip extends object
{
	 /**
	 * @var $caption string The caption
	 */
	 private $caption;

	 /**
	 * @var $text string The text
	 */
	 private $text;

	 /**
	 * @var $cursor string The cursor for the caption
	 */
	 private $cursor = 'default';

    /**
	* The constructor.
	*/
	public function init()
    {
		// Include the JS tooltip lib if not already included
		if (!defined('CHISIMBA_TOOLTIP_JS_INCLUDED')) {

    /**
     * Description for define
     */
		  	define('CHISIMBA_TOOLTIP_JS_INCLUDED', TRUE);
			$this->appendArrayVar('headerParams',$this->getJavascriptFile('tooltip.js','htmlelements'));
			// Incremented each time the show function is called...
			$GLOBALS['CHISIMBA_TOOLTIP_ID'] = 0;
		}
	}

	/**
	* Set the caption.
	* @param string The caption
	* @return null
	*/
	public function setCaption($caption)
	{
		$this->caption = $caption;
	}

	/**
	* Set the text.
	* @param string The text
	* @return null
	*/
	public function setText($text)
	{
		$this->text = $text;
	}

	/**
	* Set the cursor.
	* @param string The cursor type
	* @return null
	*/
	public function setCursor($cursor)
	{
		$this->cursor = $cursor;
	}

	/**
	* Method display a tooltip.
    * @return string Tooltip
	*/
	public function show()
	{
		$id = $GLOBALS['CHISIMBA_TOOLTIP_ID']++;;
		$_text = nl2br($this->text);
		return <<<EOB
<div id="tooltip_div_{$id}" style="display:none; margin: 5px; background-color: yellow;">{$_text}</div>
<span id="tooltip_span_{$id}" style="cursor: {$this->cursor};">{$this->caption}</span>
<script type="text/javascript" language="JavaScript">
new Tooltip('tooltip_span_{$id}', 'tooltip_div_{$id}');
</script>
EOB;
	}
}

?>