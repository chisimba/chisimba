<?php


// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}

/**
* Class for building a article box for Chisimba.
*
* The class builds a css style feature box 
*
* @package cmsadmin
* @category chisimba
* @copyright AVOIR
* @license GNU GPL
* @author Wesley  Nitsckie
*/

class articlebox extends object
{
        /**
         * Method to initialise the articlebox object
         * 
         * @access public 
         */
        public function init()
        {
        
        }

        /**
         * Method to show the article
         * 
         * @param string $content The content to appear in the article box
         * @access publc
         * @return string Html for the article box
         */
        public function show($content = null)
        {
            $article = '<div class="featurebox">';
            $article .= '<small>'.$content.'</small>';
            $article .= '</div>';
            return $article;
        }
}
?>
