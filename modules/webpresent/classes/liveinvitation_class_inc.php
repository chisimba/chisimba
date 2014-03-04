<?
/**
* Class to provier reusable view logic to the webpresent module
*
* This class takes functionality for viewing and creates reusable methods
* based on it so that the code can be reused in different templates
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
* @category  Chisimba
* @package   webpresent
* @author    David Wafula
*/

    class liveinvitation extends object{


        public function init()
        {
 
        }
    

        public function showInvitation($agenda)
        {

            $table = $this->newObject('htmltable', 'htmlelements');
            $objInput = &$this->loadClass('textinput', 'htmlelements');
            $objText = &$this->loadClass('textarea', 'htmlelements');
            $objButton = &$this->loadClass('button', 'htmlelements');

            $objInput = new textinput('agenda', $agenda, '100');
            $objText = new textarea('participants', 'enter participants cd cc emails here separated by comma', 12, '70');
            $objButton = new button('invite', 'Start Live Presentation');

            $table->cellpadding = '4';

            $table->startRow();
            $table->addCell('Agenda');
            $table->endRow();

            $table->startRow();
            $table->addCell($objInput->show());
            $table->endRow();

            $table->startRow();
            $table->addCell('Participants', '', '', '');
            $table->endRow();

            $table->startRow();
            $table->addCell($objText->show());
            $table->endRow();


            return $table->show();
        }

    }
?>