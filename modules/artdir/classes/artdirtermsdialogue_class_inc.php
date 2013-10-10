<?php

/**
 * artdirtermsdialogue class
 * 
 * Class to be used for generating jQuery UI Dialogues for accepting artdir terms.
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
 * @package   artdir
 * @author    Joke van Niekerk <jokevn@jokevn.za.net> and Charl van Niekerk <charlvn@charlvn.za.net>
 * @author    Paul Scott <pscott209@gmail.com>
 * @copyright 2011 FSIU
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: $
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

$GLOBALS['_globalObjEngine']->loadClass('jqdialogue', 'jquery');

/**
 * Class to create dialogues using the jQuery UI for accepting artdir terms.
 * 
 * @package   artdir
 * @category  chisimba
 * @copyright 2011 FSIU
 * @license   GNU GPL
 * @author    Joke van Niekerk and Charl van Niekerk
 * @author    Paul Scott <pscott209@gmail.com>
 */
class artdirtermsdialogue extends jqdialogue
{
    /**
     * Object of the language class in the language module.
     *
     * @access protected
     * @var object $objLanguage
     */
    protected $objLanguage;

    /**
     * The constructor.
     *
     * @access public
     */
    public function init()
    {
        parent::init();
        $this->objLanguage = $this->getObject('language', 'language');
        $this->title = $this->objLanguage->languageText('mod_artdir_terms_title', 'artdir');
    }

    /**
     * Generates the dialogue box.
     *
     * @access public
     * @return string The generated (X)HTML.
     */
    public function show()
    {
        if ($this->content) {
            $this->loadClass('checkbox', 'htmlelements');
            $this->loadClass('label', 'htmlelements');

            $this->content .= '<p>';

            $checkbox = new checkbox('acceptedterms');
            $checkbox->setId('acceptedterms');
            $this->content .= $checkbox->show();

            $labelText = $this->objLanguage->languageText('mod_artdir_terms_accept', 'artdir');
            $label = new label($labelText, 'acceptedterms');
            $this->content .= ' '.$label->show();

            $this->content .= '</p>';

            $acceptedJs = 'jQuery.getJSON("index.php?module=artdir&action=artdiradmin&mode=acceptterms")';
            $declinedJs = 'document.location="index.php?module=artdir&action=view"';
            $closeJs = 'document.getElementById("acceptedterms").checked?'.$acceptedJs.':'.$declinedJs;

            $this->setClose($closeJs);
        }
        return parent::show();
    }
}
