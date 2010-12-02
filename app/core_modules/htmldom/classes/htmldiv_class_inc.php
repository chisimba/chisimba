<?php

/**
 * Div class for Chisimba using the DOM Object
 * 
 * This file contains the div class which is used to generate
 * HTML div element for forms. It was modified after the original
 * HTMLelements div class by Paul Mungai as part of the Chisimba
 * hackathon 2010 11 29. Unlike HTMLelements, this class extends object
 * and must be instantiated using $this->newObject('htmldiv', 'htmldom')
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
 * @package   htmldom
 * @author    Paul Mungai <paul.mungai@wits.ac.za>
 * @copyright 2010, University of the Western Cape & AVOIR Project
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
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

/**
 * This file contains the div class which is used to generate
 * HTML div elements for forms. It was modified after the original
 * HTMLelements div class by Paul Mungai as part of the Chisimba
 * hackathon 2010 12 02. Unlike HTMLelements, this class extends object
 * and must be instantiated using $this->newObject('htmldiv', 'htmldom')
 * div class acts as an base class
 * for some commom objects
 *
 * @author Paul Mungai
 * @copyright 2010
 *
 */
class htmldiv extends object {

    /**
     * Specifies a classname for an element, and is set using
     * $this->setValue($param, $value)
     *
     * @var string $size
     * @access private
     *
     */
    private $class;
    /**
     * Specifies the text direction for the content in an element, and is set using
     * $this->setValue($param, $value)
     *
     * @var string $value
     * @access private
     *
     */
    private $dir;
    /**
     * Specifies an inline style for an element, and is set using
     * $this->setValue($param, $value)
     *
     * @var string $style
     * @access private
     *
     */
    private $style;
    /**
     * Specifies extra information about an element, and is set using
     * $this->setValue($param, $value)
     *
     * @var string $title
     * @access private
     *
     */
    private $title;
    /**
     * Specifies a language code for the content in an element, and is set using
     * $this->setValue($param, $value)
     *
     * @var string $lang
     * @access private
     *
     */
    private $lang;
    /**
     * Specifies a unique id for an element, and is set using
     * $this->setValue($param, $value)
     *
     * @var string $id
     * @access private
     *
     */
    private $id;
    /**
     * Holds content within the div, and is set using
     * $this->setValue($param, $value)
     *
     * @var string $content
     * @access private
     *
     */
    private $content;
    /**
     *
     * Object to hold the dom document
     *
     * @var string Object $objDom
     * @access private
     */
    private $objDom;

    /**
     *
     * Intialiser for the htmldom div object
     *
     * @access public
     * @return void
     *
     */
    public function init() {
        // Instantiate the built in PHP DOM extension and create DOM document.
        $this->objDom = new DOMDocument();
    }

    /**
     *
     * Standard show function to render the div using the DOM document
     * object
     * Example
     * $htmlInput = $this->getObject('htmldiv', 'htmldom');
     * $htmlInput->setValue('class', 'toastem');
     * $htmlInput->setValue('dir', 'toast');
     * $htmlInput->setValue('style', 'rts');
     * $htmlInput->setValue('lang', 'eng');
     * $htmlInput->setValue('title', 'tst');
     * $htmlInput->setValue('id', '123toast');
     * $htmlInput->setValue('content', 'I am within :-(|)');
     * $str = $htmlInput->show();
     * 
     * @param <type> $class
     * @param <type> $dir
     * @param <type> $style
     * @param <type> $lang
     * @param <type> $title
     * @param <type> $id
     * @return <type>
     */
    public function show() {
        $div = $this->objDom->createElement('div');
        // Set the div attributes
        if ($this->class) {
            $div->setAttribute('class', $this->class);
        }
        if ($this->dir) {
            $div->setAttribute('dir', $this->dir);
        }
        if ($this->lang) {
            $div->setAttribute('lang', $this->lang);
        }
        if ($this->style) {
            $div->setAttribute('style', $this->style);
        }
        if ($this->title) {
            $div->setAttribute('title', $this->title);
        }
        if ($this->id) {
            $div->setAttribute('id', $this->id);
        }
        if ($this->content) {
            $ctent = $this->objDom->createTextNode($this->content);
            $div->appendChild($ctent);
        }

        $div = $this->objDom->appendChild($div);
        $ret = $this->objDom->saveHTML();
        return $ret;
    }

    /**
     *
     * A standard setter. The following params may be set here
     * $dir - Set the dir of the div element
     * $class - Set the CSS class to use in the div element
     * $lang - Set the language of the div element
     * $style  - Set the style of the div element
     * $title - Set the title of the div element
     * $id - Set the id of the div element
     * $content - Set the content between the div element
     *
     *
     * @param string $param The name of the parameter to set
     * @param string $value The value to set the parameter to
     * @access public
     */
    public function setValue($param, $value) {
        $this->$param = $value;
    }

    /**
     * A standard fetcher. The following params may be fetched here
     * $dir - Fetch the dir of the div element
     * $class - Fetch the CSS class to use in the div element
     * $lang - Fetch the language of the div element
     * $style  - Fetch the style of the div element
     * $title - Fetch the title of the div element
     * $id - Fetch the id of the div element
     * $content - Fetch the content between the div element
     *
     * @param string $param The name of the parameter to set
     * @access public
     */
    public function getValue($param) {
        return $this->$param;
    }

}

?>