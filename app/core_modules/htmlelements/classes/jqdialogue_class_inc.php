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
 * @version   $Id$
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
     * Constructor to initialise instance variables.
     */
    public function init()
    {
        $this->objSkin = $this->getObject('skin', 'skin');
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

        $this->appendArrayVar('headerParams', $this->getJavascriptFile('jquery/api/ui/ui.core.js', 'htmlelements'));
        $this->appendArrayVar('headerParams', $this->getJavascriptFile('jquery/api/ui/dialog/ui.dialog.js', 'htmlelements'));
        $this->appendArrayVar('headerParams', '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('jquery/api/ui/theme/ui.all.css', 'htmlelements').'">');

        $options = array();

        $options[] = 'bgiframe:true';
        $options[] = 'height:140';
        $options[] = 'modal:true';

        if ($this->close) {
            $options[] = 'close:function(event,ui){'.$this->close.'}';
        }

        $optionsCode = implode(',', $options);

        $script = '<script type="text/javascript">jQuery(function(){jQuery("#jqdialogue").dialog({'.$optionsCode.'});});</script>';
        $this->appendArrayVar('headerParams', $script);

        $html = '<div id="jqdialogue" title="'.htmlspecialchars($this->title).'">'.$this->content.'</div>';

        return $html;
    }
}
