<?php

/**
 * The patch class is used to read and write module version information,
 *  as well as to apply patches to modules
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
 * @category  Chisimba
 * @package   modulecatalogue
 * @author    Monwabisi Sifumba <wsifumba@gmail.com>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
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

class block_updates extends object {

    /**
     *
     * @var string The block title
     * @access public
     */
    var $title;

    /**
     *
     * @var object The patch object
     * @access public
     */
    var $objPatch;

    /**
     *
     * @var object The PHP-DOM document
     * @access public
     */
    var $domDoc;

    /**
     * 
     * @access public
     * @return NULL
     */
    function init() {
        $this->title = 'Updates';
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objPatch = $this->getObject('patch', 'modulecatalogue');
        $this->domDoc = new DOMDocument('UTF-8');
    }

    /**
     * Method to build the block
     * 
     * @access public
     * @return string The block content as string
     */
    public function getUpdates() {
        $doms['updateDiv'] = $this->domDoc->createElement('div');
        $doms['updateDiv']->setAttribute('class', '');
        //the icon object
        $objIcon = $this->getObject('geticon', 'htmlelements');
        foreach ($this->objPatch->checkModules() as $module) {
            $doms['updatePara'] = $this->domDoc->createElement('p');
            $doms['updatePara']->setAttribute('id', 'moduleupdate');
            $doms['moduleIcon'] = $this->domDoc->createElement('image');
            $objIcon->setModuleIcon($module['module_id']);
            $doms['moduleIcon']->setAttribute('id', 'moduleIcon');
            $doms['moduleIcon']->setAttribute('src', $objIcon->getSrc());
            $doms['updateDiv']->setAttribute('id', 'div_updates');
            $doms['updatePara']->setAttribute('id', 'updatePara');
            //the link
            $doms['patchLink'] = $this->domDoc->createElement('a');
            $doms['patchLink']->setAttribute('id', $module['module_id']);
            $doms['patchLink']->setAttribute('class', 'patchLink');
            $doms['patchLink']->setAttribute('href', '#');
            $doms['patchLink']->setAttribute('value', $module['new_version']);
            //Module name
            $doms['updatePara']->appendChild($doms['moduleIcon']);
            $doms['updatePara']->appendChild($this->domDoc->createTextNode(ucwords($module['module_id'])));
            $doms['updatePara']->appendChild($this->domDoc->createElement('br'));
            $doms['label'] = $this->domDoc->createElement('label');
            $doms['label']->appendChild($this->domDoc->createTextNode($this->objLanguage->languageText('word_description', 'system') . ' : ' . $module['desc']));
            $doms['updatePara']->appendChild($doms['label']);
            $doms['updatePara']->appendChild($this->domDoc->createElement('br'));
            $doms['patchLink']->appendChild($this->domDoc->createTextNode($this->objLanguage->languageText('phrase_update', 'system') . ' ' . $module['old_version'] . ' ' . $this->objLanguage->languageText('word_to', 'system') . ' ' . $module['new_version']));
            $doms['updatePara']->appendChild($doms['patchLink']);
            $doms['updateDiv']->appendChild($doms['updatePara']);
            $doms['updateDiv']->appendChild($this->domDoc->createElement('br'));
        }
        //display apply all patches link if the list of packeges is greater than 1
        if (count($this->objPatch->checkModules()) > 1) {
            $doms['updateAll'] = $this->domDoc->createElement('a');
            $doms['updateAll']->setAttribute('id', 'linkUpdateAll');
            $doms['updateAll']->setAttribute('href', '#');
            $doms['updateAll']->appendChild($this->domDoc->createTextNode($this->objLanguage->languageText('phrase_applyupdates', 'system')));
            $doms['updateDiv']->appendChild($doms['updateAll']);
        }
        //display text idicating as such if there are no updates available
        if (count($this->objPatch->checkModules()) == 0) {
            $doms['updateDiv']->appendChild($this->domDoc->createTextNode($this->objLanguage->languageText('mod_updates_noupdates', 'modulecatalogue')));
        }
        return $this->domDoc->saveHTML($doms['updateDiv']);
    }

    /**
     * 
     * @access public
     * @return string The block and javascript file
     */
    public function show() {
        $modules = $this->objPatch->checkModules();
        return $this->getUpdates() . $this->getJavascriptFile('updates.js', 'modulecatalogue');
    }

}

?>
