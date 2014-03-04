<?php

/**
 * html5table_class_inc.php
 *
 * Generates an HTML5 table to display tabular data on a web page.
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
 * @package   html5elements
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: html5table_class_inc.php 19529 2010-10-28 17:20:34Z charlvn $
 * @link      http://avoir.uwc.ac.za
 */
class html5table extends object
{
    /**
     * Instance of the dbsysconfig class of the sysconfig module.
     *
     * @access private
     * @var    object
     */
    private $objDbConfig;

    /**
     * Instance of the language class of the language module.
     *
     * @access private
     * @var    object
     */
    private $objLanguage;

    /**
     * Initialises the object properties.
     *
     * @access public
     */
    public function init()
    {
        $this->objDbConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->objLanguage = $this->getObject('language', 'language');
    }

    /**
     * Generates an HTML5 table.
     *
     * @access public
     * @param  object $document The DOMDocument to use.
     * @param  string $title    The title of the table. NULL for none.
     * @param  array  $headers  The column headers. Empty array for none.
     * @param  array  $contents The table contents. Empty array for none.
     * @param  array  $edit     The query string parameters for editing.
     * @param  array  $delete   The query string parameters for deleting.
     * @param  string $module   The name of the module.
     * @param  string $checkbox The name of the checkbox array.
     * @param  string $class    The class(es) to assign to the table.
     * @param  string $id       The id of the table.
     * @return object The DOMElement generated.
     */
    public function table(DOMDocument $document, $title, array $headers, array $contents, array $edit=array(), array $delete=array(), $module=NULL, $checkbox=NULL, $class=NULL, $id=NULL)
    {
        $table = $document->createElement('table');

        if (is_string($class)) {
            $table->setAttribute('class', $class);
        }

        if (is_string($id)) {
            $table->setAttribute('id', $id);
        }

        if (is_string($title)) {
            $caption = $document->createElement('caption');
            $table->appendChild($caption);

            $text = $document->createTextNode($title);
            $caption->appendChild($text);
        }

        if (count($headers) > 0) {
            if (is_string($checkbox)) {
                array_unshift($headers, 'Select');
            }

            if (count($edit) > 0 || count($delete)) {
                array_push($headers, 'Actions');
            }

            $thead = $document->createElement('thead');
            $table->appendChild($thead);

            $tr = $document->createElement('tr');
            $thead->appendChild($tr);

            foreach ($headers as $header) {
                $th = $document->createElement('th');
                $tr->appendChild($th);

                $text = $document->createTextNode($header);
                $th->appendChild($text);
            }
        }

        if (count($contents) > 0) {
            $tbody = $document->createElement('tbody');
            $table->appendChild($tbody);

            foreach ($contents as $i => $row) {
                $tr = $document->createElement('tr');
                $tbody->appendChild($tr);

                if (is_string($checkbox)) {
                    $td = $document->createElement('td');
                    $tr->appendChild($td);

                    $input = $document->createElement('input');
                    $input->setAttribute('name', $checkbox.'['.$i.']');
                    $input->setAttribute('type', 'checkbox');
                    $td->appendChild($input);
                }

                foreach ($row as $value) {
                    $td = $document->createElement('td');
                    $tr->appendChild($td);

                    $text = $document->createTextNode($value);
                    $td->appendChild($text);
                }

                if (count($edit) > 0 || count($delete) > 0) {
                    $td = $document->createElement('td');
                    $tr->appendChild($td);

                    if (count($edit) > 0) {
                        $edit['id'] = $i;

                        $a = $document->createElement('a');
                        $a->setAttribute('href', $this->uri($edit, $module, '', FALSE, TRUE, TRUE));
                        $td->appendChild($a);

                        $icon = $this->objDbConfig->getValue('edit_icon', 'html5elements');
                        $text = $this->objLanguage->languageText('mod_html5elements_edit', 'html5elements');

                        $img = $document->createElement('img');
                        $img->setAttribute('src', $icon);
                        $img->setAttribute('alt', $text);
                        $img->setAttribute('title', $text);
                        $a->appendChild($img);
                    }

                    if (count($delete) > 0) {
                        $delete['id'] = $i;

                        $a = $document->createElement('a');
                        $a->setAttribute('href', html_entity_decode($this->uri($delete, $module)));
                        $td->appendChild($a);

                        $icon = $this->objDbConfig->getValue('delete_icon', 'html5elements');
                        $text = $this->objLanguage->languageText('mod_html5elements_delete');

                        $img = $document->createElement('img');
                        $img->setAttribute('src', $icon);
                        $img->setAttribute('alt', $text);
                        $img->setAttribute('title', $text);
                        $a->appendChild($img);
                    }
                }
            }
        }

        return $table;
    }
}

?>
