<?php

/**
 * This class contains faculty management helper functions
 *  PHP version 5
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
 * @package   apo (document management system)
 * @author    Nguni Phakela, david wafula
 * @copyright 2010
  =
 */
if (!
    /**
     * Description for $GLOBALS
     * @global string $GLOBALS['kewl_entry_point_run']
     * @name   $kewl_entry_point_run
     */
    $GLOBALS['kewl_entry_point_run']) {
        die("You cannot view this page directly");
}

class faculties extends object {

    // The Label for the Faculty
    private $facultyLabel;

    public function init() {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        $this->loadClass('fieldset', 'htmlelements');
        $this->objUtils = $this->getObject('userutils');
        $this->facultyLabel = "Faculty";
    }

    function showCreateFacultiesForm($name='') {

        $form = new form('registerfaculty', $this->uri(array('action' => 'registerfaculty')));
        //$textinput = new textinput('facultyname');
        //$textinput->value = $name;
        $label = new label('Name of ' . $this->facultyLabel . ': ', 'input_facultyname');
        
        // Opening date
        $table = $this->newObject('htmltable', 'htmlelements');

        $label = new label('Name of ' . $this->facultyLabel . ': ', 'input_facultyname');
        $textinput = new textinput('faculty');
        $textinput->size = 60;
        $table->startRow();
        $table->addCell($label->show());
        $table->addCell($textinput->show());
        $table->endRow();

        $textinput = new textinput('contact');
        $textinput->size = 60;
        $table->startRow();
        $table->addCell("<b>Contact person</b>");
        $table->addCell($textinput->show());
        $table->endRow();



        $textinput = new textinput('telephone');
        $textinput->size = 40;
        $table->startRow();
        $table->addCell("<b>Telephone number</b>");
        $table->addCell($textinput->show());
        $table->endRow();

        $table->startRow();
        $table->addCell("<b>Create in</b>" );
        $table->addCell($this->objUtils->getTree('htmldropdown'));
        $table->endRow();

        $button = new button('create', 'Create ' . $this->facultyLabel);
        $button->setToSubmit();

        $form->addToForm($table->show());
        $form->addToForm('<br/>' . $button->show());

        $fs = new fieldset();
        $fs->setLegend($this->facultyLabel);
        $fs->addContent($form->show());
        
        return $fs->show();
    }

}