<?php

/**
* A set of button objects to create interface buttons
*/
class navbuttons extends object
{

     var $objLanguage;
     var $objConfig;

	/**
	* Init method for the buttons class, used to
	* instantiate a local access to the language object
	*/
	function init() 
	{
		$this->objLanguage =& $this->getObject('language', 'language');
		$this->objConfig =& $this->getObject('config', 'config');
    }

	/**
	* Return an edit button as string
	* @param string $type: the type of icon to display, translates to the filename
	* @param string $filename: the filename if it is different from $type
	*/
	function button($type, $filename=NULL, $ext="gif",$alt=NULL) 
	{
		global $objLanguage, $objConfig;
		$key='word_'.$type;
		if (!$filename) {
			$filename=$type; //.".".$ext;
		}
		if (!$alt) {
			$alt=$this->objLanguage->languageText($key);
		}
                $icon=$this->newObject('geticon','htmlelements');
                $icon->setIcon($filename);
                $icon->alt=$alt;
                $icon->title=$alt;
                return $icon->show();

		$ret='<img src="'.$this->objConfig->defaultIconFolder().$filename.'" 
			alt="'.$alt.'" border="0" 
			align="absmiddle" valign="middle" />';
		return $ret;
	}
	
	/**
	* Print an edit button
	*/
	function putEditButton() 
	{
		return $this->button("edit");
	}
	
	/**
	* Print a delete button
	*/
	function putDeleteButton() 
	{
		return $this->button("delete");
	}
	
	/**
	* Print a info button
	*/
	function putInfoButton() 
	{
		return $this->button("info");
	}
	
	/**
	* Print a home button
	*/
	function putHomeButton() 
	{
		return $this->button("home");
	}
	
	/**
	* Print an edit button
	*/
	function linkedButton($type, $link, $target="_top",$alt=Null) 
	{
		$strout='<a href="'.$link.'" target="'.$target.'">'.$this->button($type,Null,'gif',$alt).'</a>';
		return $strout;
	}
	
	
	/**
	* Method to put a form field button
	*/
	function formButton($type, $label) 
	{
		global $objLanguage;
		$ret='<input type="'.$type
			.'" name="'.$label.'" value="'
			.$this->objLanguage->languageText("word_$label")
			.'" class="button" />';
		return $ret;
	}

	
	/** 
	* Method to put a save button
	*/
	function putSaveButton() 
	{
		return $this->formButton("submit", "save");
	}
	
	/** 
	* Method to put a search button
	*/
	function putSearchButton() 
	{
		return $this->formButton("submit", "search");
	}
	
	/**
	* Print a GO button
	*/
	function putGoButton() {
		return $this->formButton("submit", "go");
	}
	
	/**
	* Method to print a KEWL.NextGen button 
	* style link. Make sure that $linktext is translated
	* before passing it.
	* @param string $link: the URL to which the button links
	* @param string $linktext: the text to appear on the button
	* @param string $space: @values:TRUE|FALSE, whether to print a space after the button
	*/
	function pseudoButton($link, $linktext, $space=FALSE)
	{
		if (!$space==FALSE) {
		    $space="&nbsp;";
		}
		return $space."<a href='".$link."' class='pseudobutton'>".$linktext.'</a>'.$space."\n";
	}
}
?>
