<?php
/**
 * Class for parsing smilies in a text string
 * tags such as 
 *   <code>
 *     $a="Hello";
 *     $b="World";
 *     echo $a . " " . $b .".";
 *   </code>
 * 
 * @author Derek Keats 
 * @version $Id$
 * @copyright 2003 GPL
 */
class parse4smileys extends object {
    /**
     * Method to take a string and return it with smiley
     * codes replaced by smiley icons
     * embeded in a layer that is designed for formatting code
     * 
     * The array is taken from the Moodle smiley parser, but
     * I chose to implement it in a different way using preg_repalce
     * with a regex to avoid doing it inside script blocks. Therefore
     * I also escaped the smiley part of the array for the test. Note
     * that this takes places of the preg_replace ability to deal with
     * arrays. I first make the parseSmiley array just to make it easy
     * to ensure that the items are in matching pairs, and then use this
     * array to build the two arrays to be parsed.
     * 
     * @param string $str The string to be parsed
     * @return the parsed string
     * 
     * @todo -cparse4smileys Implement do not parse if inside a HTML tag.
     * 
     */
    function parseSmiley($str)
    {
        static $smileyIcons = array(
        '\:\-\)'  => 'smiley',
        '\:\)'   => 'smiley',
        '\[smile\]'   => 'smiley',
        '\:\-D'  => 'biggrin',
        '\[biggrin\]'  => 'biggrin',
        ';\)'  => 'wink',
        ';\-\)'  => 'wink',
        '\[wink\]'  => 'wink',
        '\:\-\/'  => 'mixed',
        '\[mixed\]'  => 'mixed',
        'V\-\.'  => 'thoughtful',
        '\[thoughtful\]'  => 'thoughtful',
        '\[thinking\]'  => 'thoughtful',
        '\[think\]'  => 'thoughtful',
        '\:\-P'  => 'tongueout',
        '\[tongueout\]'  => 'tongueout',
        '\[tongue\]'  => 'tongueout',
        'B\-\)'  => 'cool',
        '\[cool\]'  => 'cool',
        '\^\-\)'  => 'approve',
        '\[approve\]'  => 'approve',
        '\[ok\]'  => 'approve',
        '8\-\)'  => 'wideeyes',
        '\[wideeyes\]'  => 'wideeyes',
        '\:o\)'  => 'clown',
        '\[clown\]'  => 'clown',
        '\:\-\('  => 'sad',
        '\:\('   => 'sad',
        '\[sad\]'  => 'sad',
        '8\-.'  => 'shy',
        '\[shy\]'  => 'shy',
        '\:\-I'  => 'blush',
        '\[blush\]'  => 'blush',
        '\:\-X'  => 'kiss',
        '\[kiss\]'  => 'kiss',
        '8\-o'  => 'surprise',
        '\[surprise\]'  => 'surprise',
        'P\-\|'  => 'blackeye',
        '\[blackeye\]'  => 'blackeye',
        '8\-\['  => 'angry',
        '\[angry\]'  => 'angry',
        'xx\-P' => 'dead',
        '\[dead\]'  => 'dead',
        '\|\-\.'  => 'sleepy',
        '\[sleepy\]'  => 'sleepy',
        '\}\-\]'  => 'evil',
        '\[evil\]'  => 'evil'
        );
        //Get an instance of the config object
        $objConfig = & $this->getObject('altconfig', 'config');
        /* 
        *  Loop through the array and make the arrays for 
        *  $test and $replace for the regex. This is done because
        *  it is otherwise hard to keep track of the smileys 
        */ 
        foreach ($smileyIcons as $smiley => $image){
            $test[] = "/".$smiley."/isU";
            $replace[] = "<img alt=\"$image\" width=\"15\" height=\"15\" 
              src=\"".$objConfig->getsiteRoot()."core_modules/filters/resources/smileys/$image.gif\" />";
        }
        
        return preg_replace($test, $replace, $str);

    } # end of function
       
} # end of class

?>
