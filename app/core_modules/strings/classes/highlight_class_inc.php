<?php
/**
 * This class provides some methods for highlighting strings without damaging
 * text inside html-tags.
 * @name		 Highlighter
 * @description	 Advanced keyword highlighter, keep HTML tags safe.
 * @author(s)    Bojidar Naydenov a.k.a Bojo (bojo2000@mail.bg) & Antony Raijekov 
 *   a.k.a Zeos (dev@strategma.bg) from Bulgaria and adapted to the KEWL.NextGen 
 *   framework by Derek Keats
 * 
 * @license GPL
 * 
 */

class highlight extends object
{
    var $keyword;
	var $replacement;
	var $hightlightBadTags = array("A","IMG");	//add here more, if you want to filter them	
	
	/**
    * Standard KEWL.NextGen constructor method
    */
	function init ()
	{
        $this->replacement = '<strong class="HighLightText">{keyword}</strong>';
	}
    
	/**
    * Standard KEWL.NextGen show method to return the string with the keyword
    * highlighted
    * 
    * @todo -chighlight Implement highlight based on an array of keywords.
    *   The approach will be a recursive call to show for the whole array
    * 
    * @param string $text: The text to parse
    * @param string $keyword: The keyword(s) to highlight
    * 
    */
	function show($text,$keyword = false)
	{
		//if there are specific keyword/replacement given
		if($keyword != false) {
            $this->keyword = $keyword;
        }
        
        $kAr = explode(" ", $this->keyword);
        $elems = count($kAr);
        
		//process text array(&$this, 'method_name'), 
		if((isset($this -> keyword)) AND (isset($this -> replacement))) {
            if ( $elems <= 1 ) {
                $text = preg_replace_callback("#(<([A-Za-z]+)[^>]*[\>]*)*(".$this -> keyword
                  .")\b(.*?)(<\/\\2>)*#si",array(&$this, '_highlighter'), $text);
            } else {
                for($counter=0; $counter < $elems; $counter++) {
                    $this -> keyword = $kAr[$counter];
                    $text = preg_replace_callback("#(<([A-Za-z]+)[^>]*[\>]*)*(".$this -> keyword
                      .")\b(.*?)(<\/\\2>)*#si",array(&$this, '_highlighter'), $text);
                }
            }
		}
	    return $text;
	} //end func process
    
    
    /*------------------------ PRIVATE METHODS BELOW THIS LINE -------------------- */

	/**
    * Method to parse matches and do the highlighting
    * 
    * @param string $matches The matched text
    */
    
	function _highlighter($matches)
	{		
		//check for bad tags and keyword					
		if (!in_array(strtoupper($matches[2]),$this -> hightlightBadTags))  
		{
			//put template [replacement]
			$proceed =  preg_replace("#\b(".$this -> keyword
              .")\b#si",str_replace("{keyword}",$matches[3],$this -> replacement),$matches[0]);
		}
		else	//return as-is
		{
			$proceed = $matches[0];
		}
		return stripslashes($proceed);
	} //end func hightlighter



} // end class