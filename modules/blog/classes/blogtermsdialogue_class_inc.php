<?php

/**
 * blogtermsdialogue class
 * 
 * Class to be used for generating jQuery UI Dialogues for accepting blog terms.
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
 * @package   blog
 * @author    Joke van Niekerk <jokevn@jokevn.za.net> and Charl van Niekerk <charlvn@charlvn.za.net>
 * @copyright 2009 FSIU
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: blogtermsdialogue_class_inc.php 16311 2010-01-16 09:01:14Z dkeats $
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
 * Class to create dialogues using the jQuery UI for accepting blog terms.
 * 
 * @package   blog
 * @category  chisimba
 * @copyright 2009 FSIU
 * @license   GNU GPL
 * @author    Joke van Niekerk and Charl van Niekerk
 */
class blogtermsdialogue extends jqdialogue
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
        $this->title = $this->objLanguage->languageText('mod_blog_terms_title', 'blog');
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

            $labelText = $this->objLanguage->languageText('mod_blog_terms_accept', 'blog');
            $label = new label($labelText, 'acceptedterms');
            $this->content .= ' '.$label->show();

            $this->content .= '</p>';

            $acceptedJs = 'jQuery.getJSON("index.php?module=blog&action=blogadmin&mode=acceptterms")';
            $declinedJs = 'document.location="index.php?module=blog&action=viewblog"';
            $closeJs = 'document.getElementById("acceptedterms").checked?'.$acceptedJs.':'.$declinedJs;

            $this->setClose($closeJs);
        }
        return parent::show();
    }
}
