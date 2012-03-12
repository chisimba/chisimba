<?php

/**
 * jqdialogue class
 * 
 * Class to be used for generating jQuery UI Dialogues.
 * 
 * PHP version 5
 * 
 * This program is free software; you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation; either version 2 of the License, or 
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful, 
 * but WITHOUT ANY WARRANTY; without even the implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the 
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License 
 * along with this program; if not, write to the 
 * Free Software Foundation, Inc., 
 * 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 
 * @category  chisimba
 * @package   htmlelements
 * @author    Joke van Niekerk <jokevn@jokevn.za.net> and Charl van Niekerk <charlvn@charlvn.za.net>
 * @copyright 2009 FSIU
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: jqdialogue_class_inc.php 12561 2009-02-20 02:35:52Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 * @see       http://docs.jquery.com/UI/Dialog
 */

// security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global unknown $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}

/**
* HTML control class to create dialogues using the jQuery UI.
* 
* @package   htmlelements
* @category  HTML Controls
* @copyright 2009 FSIU
* @license   GNU GPL
* @author    Joke van Niekerk and Charl van Niekerk
*/
class jqdialogue extends object
{
    /**
     * The instance of the skin class in the skin module.
     *
     * @access protected
     * @var object $objSkin
     */
    protected $objSkin;

    /**
     * The title of the dialogue.
     *
     * @access protected
     * @var string $title
     */
    protected $title;

    /**
     * The contents of the dialogue.
     *
     * @access protected
     * @var string $content
     */
    protected $content;

    /**
     * Custom JavaScript code to execute when the dialogue is closed.
     *
     * @access protected
     * @var string $close
     */
    protected $close;

    /**
     * ID for multiple dialogues on one page.
     *
     * @access protected
     * @var string $cssId
     */
    protected $cssId;

    /**
     * jQuery dialogue options.
     *
     * @access protected
     * @var array $options
     */
    protected $options;

    /**
     * Constructor to initialise instance variables.
     */
    public function init()
    {
        $this->cssId = 'jqdialogue';
        $this->options = array(
            'bgiframe:true',
            'buttons:{"Ok":function(){jQuery(this).dialog("close");}}',
            'height:"auto"',
            'modal:true',
            'width:"auto"',
        );

        $this->objSkin = $this->getObject('skin', 'skin');
    }

    /**
     * Sets the id of the dialogue.
     *
     * @access public
     * @param string $title The new title of the dialogue.
     */
    public function setCssId($cssId)
    {
        $this->cssId = $cssId;
    }

    /**
     * Adds an option to the dialogue.
     *
     * @access public
     * @param string $option An option to add to the dialogue.
     */
    public function addOption($option)
    {
        $this->options[] = $option;
    }

    /**
     * Sets the title of the dialogue.
     *
     * @access public
     * @param string $title The new title of the dialogue.
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Sets the content of the dialogue.
     *
     * @access public
     * @param string $content The new content of the dialogue.
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Sets the custom JavaScript code to execute when the dialogue is closed.
     *
     * @access public
     * @param string $close The custom code.
     */
    public function setClose($close)
    {
        $this->close = $close;
    }

    /**
     * Adds applicable scripts to the HTML header and returns the HTML for the body.
     *
     * @access public
     * @return string The HTML for the body.
     */
    public function show()
    {
        if (!$this->title || !$this->content) {
            return '';
        }

        $this->objSkin->setVar('SUPPRESS_PROTOTYPE', true);
        $this->objSkin->setVar('JQUERY_VERSION', '1.2.6');

        $this->appendArrayVar('headerParams', $this->getJavascriptFile('api/ui/ui.core.js', 'jquery'));
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('api/ui/dialog/ui.dialog.js', 'jquery'));
        $this->appendArrayVar('headerParams', '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('api/ui/theme/ui.all.css', 'jquery').'">');

        if ($this->close) {
            $this->options[] = 'close:function(event,ui){'.$this->close.'}';
        }

        $optionsCode = implode(',', $this->options);

        $script = '<script type="text/javascript">jQuery(function(){jQuery("#'.$this->cssId.'").dialog({'.$optionsCode.'});});</script>';
        $this->appendArrayVar('headerParams', $script);

        $html = '<div id="'.$this->cssId.'" title="'.htmlspecialchars($this->title).'">'.$this->content.'</div>';

        return $html;
    }
}
?>