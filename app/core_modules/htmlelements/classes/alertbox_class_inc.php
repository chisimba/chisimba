<?php

/**
 * alertbox_class_inc.php
 *
 * Facebox
 * This generates Facebook-style lightbox which can display images, divs, or entire remote pages.
 *
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
 * @package   htmlelements
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 */

/**
 * Alertbox
 *
 * This generates Facebook-style lightbox which can display images, divs, or entire remote pages.
 *
 * See: http://famspam.com/facebox
 *
 *
 * @author Tohir Solomons
 * @category  Chisimba
 * @package   htmlelements
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @see       Color Picker by Matt Kruse - resources/ColorPicker2.js
 * @link      http://www.mattkruse.com/javascript/colorpicker/index.html
 * @link      http://avoir.uwc.ac.za
 */
class alertbox extends object
{

    /**
    * Class Constructor
    */
    public function init()
    {
        $this->loadClass('link', 'htmlelements');
    }

    /**
    * Method to render the color picker as HTML code
    *
    * @return string The HTML code of the rendered textinput and colorpicker
    * @access public
    */
    public function putJs()
    {
        $this->appendArrayVar('headerParams', $this->getJavaScriptFile('alertbox/facebox.js'));

        $this->appendArrayVar('headerParams', '<script type="text/javascript">
 jQuery(document).ready(function($) {
  $(\'a[rel*=facebox]\').facebox()
})
</script>');
    }
    
    public function show($text, $link)
    {
        $this->putJs();
        
        $link = new link ($link);
        $link->link = $text;
        $link->extra = 'rel="facebox"';
        
        return $link->show();
    }
}
?>