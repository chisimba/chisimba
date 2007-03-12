<?php
/**
 * Class for parsing smilies in a text string
 * @author Kevin Cyster
 * @version $Id: parse4format_class_inc.php,v 1.0 2007/03/01
 * @copyright 2003 GPL
 */
class parse4format extends object {
    /**
     * Method to take a string and return it with font formating
     * @param string $str The string to be parsed
     * @return the parsed string
     * 
     * @todo -cparse4smileys Implement do not parse if inside a HTML tag.
     * 
     */
    function parseSmiley($str)
    {
        static $formats = array(
        '[B]' => '<b>',
        '[/B]' => '</b>',
        '[I]' => '<i>',
        '[/I]' => '</i>',
        '[U]' => '<u>',
        '[/U]' => '</u>',
        '[RED]' => '<font style="color: red">',
        '[/RED]' => '</font>',
        '[BLUE]' => '<font style="color: blue">',
        '[/BLUE]' => '</font>',
        '[YELLOW]' => '<font style="color: yellow">',
        '[/YELLOW]' => '</font>',
        '[GREEN]' => '<font style="color: green">',
        '[/GREEN]' => '</font>',
        '[S1]' => '<font size="2">',
        '[S2]' => '<font size="3">',
        '[S3]' => '<font size="4">',
        '[S4]' => '<font size="5">',
        '[S5]' => '<font size="6">',
        '[S6]' => '<font size="7">',
        '[/S]' => '</font>',
        );
 
        $objIcon = &$this->getObject('geticon', 'htmlelements');
        /* 
        *  Loop through the array and make the arrays for 
        *  $test and $replace for the regex. This is done because
        *  it is otherwise hard to keep track of the smileys 
        */ 
        foreach ($formats as $code => $tag){
            $test[] = $code;
            $replace[] = $tag;
        }
        return str_ireplace($test, $replace, $str);

    } # end of function
    
    public function parse($str)
    {
    	return $this->parseSmiley($str);
    }
       
} # end of class
?>