<?php

/**
 * Class to Show a File Selector Input
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
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   CVS: $Id$
 * @link      http://avoir.uwc.ac.za
 * @see
 */


/**
 * Class to Show a File Selector Input
 *
 * @category  Chisimba
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see
 */
class filesearch extends object
{


    /**
    * Constructor
    */
    public function init()
    {
        
    }
    
    public function searchForm()
    {
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('textinput', 'htmlelements');
        $this->loadClass('button', 'htmlelements');
        
        $form = new form ('searchfile', $this->uri(array('action'=>'search')));
        $textinput = new textinput('filesearch');
        
        $button = new button('submitsearch', 'Search');
        $button->setToSubmit();
        
        $form->addToForm($textinput->show().' '.$button->show());
        
        return $form->show();
    }
    
    public function addFileToSearch($file)
    {
        print_r($file);
        
        // Add to Search
        $objIndexData = $this->getObject('indexdata', 'search');
        
        // Prep Data
        $docId = 'filemanager_file_'.$file['id'];
        $docDate = $file['datecreated'];
        $url = $this->uri(array('action'=>'fileinfo', 'id'=>$file['id']), 'filemanager');
        $title = $file['filename'];
        $contents = $file['filename'].' '.$file['description'];
        $teaser = $file['description'];
        $module = 'filemanager';
        $userId = $file['creatorid'];
        $license = $file['license'];
        
        $extra = array(
                'mimetype'=>$file['mimetype'],
                'filesize'=>$file['filesize'],
                'filepath'=>dirname($file['path']),
            );
        
        
        // Add to Index
        $objIndexData->luceneIndex($docId, $docDate, $url, $title, $contents, $teaser, $module, $userId, NULL, $license, NULL, NULL, 'useronly');
    }




}

?>