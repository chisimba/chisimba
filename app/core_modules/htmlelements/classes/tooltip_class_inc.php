<?php

/**
* Class for tooltips.
* Note: This tooltip JS lib is compatible with and requires prototype 1.5.0.
*
* This class loads the JavaScript for the tooltip JS lib and allows developers to
* insert their own tooltips into their modules.
*
* @author Jeremy O'Connor
* @package htmlelements
*/
class tooltip extends object
{
	 // Incremented each time the show function is called...
	 private $id = 0;

	 private $caption;
	 private $text;

    /**
	* The constructor.
	*/
	public function init()
    {
		// Include the JS tooltip lib.
		$this->appendArrayVar('headerParams',$this->getJavascriptFile('tooltip.js','htmlelements'));
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
	* Method display a tooltip.
	*
	* @param string $mimetype Mime Type of Page
    * @return string Scriptaculous JavaScript
	*/
	public function show()
	{
		++$id;
		$_text = nl2br($this->text);
		return <<<EOB
<div id="tooltip_div_{$id}" style="display:none; margin: 5px; background-color: yellow;">{$_text}</div>
<span id="tooltip_span_{$id}" style="cursor: default;">{$this->caption}</span>
<script type="text/javascript" language="JavaScript">
new Tooltip('tooltip_span_{$id}', 'tooltip_div_{$id}');
</script>
EOB;
	}
}

?>