<?php

/**
 * Class to parse a string (e.g. page content) that contains a link
 * to a mol or sdf chemical structure file and render it in the page in
 * 2d rotator, 3d rotator, image or tranformer display type
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
 * @package   filters
 * @author    Warren Windvogel
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 */
     // security check - must be included in all scripts
if (!
/**
 * Description for $GLOBALS
 * @global string $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 */
$GLOBALS['kewl_entry_point_run'])
{
    die("You cannot view this page directly");
}
// end security check
/**
 *
 * Class to parse a string (e.g. page content) that contains a link
 * to a mol or sdf chemical structure file and render it in the page in
 * 2d rotator, 3d rotator, image or tranformer display type
 *
 * @uses [CHEM]/file/path/to/molecule.mol[/CHEM]
 *       or
 *       [CHEM: file=/file/path/to/molecule.mol, type=2d, image or transformer, height=***, width=***, background=backgroundcolor]
 * @author    Warren Windvogel
 * @package   filters
 * @access    public
 *
 */

class parse4chem extends object
{

    /**
     * init
     *
     * Standard Chisimba init function
     *
     * @return void
     * @access public
     */
    function init()
    {
        $this->objConfig = $this->getObject('altconfig', 'config');
        // Get an instance of the params extractor
        $this->objDisplay = $this->getObject("chemdisplay", "chemdoodle");
        // Get an instance of the params extractor
        $this->objExpar = $this->getObject("extractparams", "utilities");
    }

    /**
    *
    * Method to parse the string
    * @param  String $str The string to parse
    * @return The    parsed string
    *
    */
    public function parse($txt)
    {
        preg_match_all('/\[CHEM\](.*)\[\/CHEM\]/U', $txt, $results, PREG_PATTERN_ORDER);
        preg_match_all('/\\[CHEM:(.*?)\\]/', $txt, $results2, PREG_PATTERN_ORDER);
        $counter = 0;
        foreach ($results[1] as $item)
        {
            $file = $item;

            $replacement = $this->objDisplay->showTransformerMolecule($file);
            $txt = str_replace($results[0][$counter], $replacement, $txt);
            $counter++;
        }

        //Get all the ones [FLV: xx=yy] tags (added by Derek 2007 09 23)
        $counter = 0;
        foreach ($results2[0] as $item)
        {
            $this->item=$item;
            $str = $results2[1][$counter];
            $ar= $this->objExpar->getArrayParams($str, ",");
            if (isset($this->objExpar->width)) {
                $width = $this->objExpar->width;
            } else {
                $width=320;
            }
            if (isset($this->objExpar->height)) {
                $height = $this->objExpar->height;
            } else {
                $height=240;
            }
            if (isset($this->objExpar->background)) {
                $height = $this->objExpar->background;
            } else {
                $background='white';
            }

            if (isset($this->objExpar->type)) {
                $type = $this->objExpar->type;
            } else {
                $type='transformer';
            }
            if (isset($this->objExpar->file)) {
                $file = $this->objExpar->file;
            }

            switch($type){
                default:
                case 'transformer':
                    $replacement = $this->objDisplay->showTransformerMolecule($file, $width, $height, 'true', 3, 'true', $background);
                    break;
                case '2d':
                    $replacement = $this->objDisplay->show2dMolecule($file, $width, $height);
                    break;
                case 'image':
                    $replacement = $this->objDisplay->showMolecule($file, $width, $height);
                    break;

            }

            $txt = str_replace($item, $replacement, $txt);
            $counter++;
        }

        return $txt;
    }
}
?>