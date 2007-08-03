<?php

/**
 * Archive handler
 * 
 * Class to Handle Zip Files for Previews and Extraction
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
 * @see       core
 */


/**
 * Archive handler
 * 
 * Class to Handle Zip Files for Previews and Extraction
 * 
 * @category  Chisimba
 * @package   filemanager
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Tohir Solomons
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       core
 */
class archivehandler extends object

{



    

    /**

    * Constructor

    */

    public function init()

    {

        $this->objConfig =& $this->getObject('altconfig', 'config');

        $this->objZip =& $this->getObject('wzip', 'utilities');

        $this->objFileIcons =& $this->getObject('fileicons', 'files');

        $this->objFileIcons->size = 'small';

        $this->loadClass('formatfilesize', 'files');

    }

    

    /**

	* Enter description here...

	*

	* @param string $path Path to the Zip File

	* @return string Preview of the Zip File

	*/

    public function previewZip($path)

    {

        $files = $this->objZip->listArchiveFiles($path);

        

        if ($files == FALSE) {

            return 'Error: Could not process file';

        } else {

            

            // echo '<pre>';

            // print_r($files);

            // echo '</pre>';

            

            $path_parts = pathinfo($path);

            

            

            

            $table = $this->newObject('htmltable', 'htmlelements');

            $table->startHeaderRow();

            $table->addHeaderCell('&nbsp;');

            $table->addHeaderCell('Name of File');

            $table->addHeaderCell('File Size');

            $table->addHeaderCell('Status');

            $table->endHeaderRow();

            

            $filecount = 0;

            $foldercount = 0;

            

            $objFileSize = new formatfilesize();

            

            foreach ($files as $file)

            {

                if ($file['folder']) {

                    continue;

                }

                

                $table->startRow();

                $table->addCell('&nbsp;');

                $table->addCell($this->objFileIcons->getFileIcon($file['filename']).' '.$file['filename']);

                $table->addCell($objFileSize->formatsize($file['size']));

                $table->addCell($file['status']);

                $table->endRow();

            }

            

            return $table->show();

        }

    }

    



    

    

    



}



?>