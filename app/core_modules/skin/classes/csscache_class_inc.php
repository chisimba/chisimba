<?php
/**
 *
 * Cache CSS files in Chisimba
 *
 * Cache CSS file from both common skins and canvases.
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
 * @package   skin
 * @author    Derek Keats <derek.keats@wits.ac.za>
 * @author Tohir Solomons
 * @author Charl Mert
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */
// security check - must be included in all scripts
if (!
/**
 * The $GLOBALS is an array used to control access to certain constants.
 * Here it is used to check if the file is opening in engine, if not it
 * stops the file from running.
 *
 * @global entry point $GLOBALS['kewl_entry_point_run']
 * @name   $kewl_entry_point_run
 *
 */
$GLOBALS['kewl_entry_point_run'])
{
        die("You cannot view this page directly");
}
// end security check

/**
 * Cache CSS files in Chisimba
 *
 * Cache CSS file from both common skins and canvases.
 *
 * @author Derek Keats
 * 
*/
class csscache extends object
{

    /**
    *
    * @var string object $objConfig Hold configuration reading object
    * @access public
    *
    */
    public $objConfig;
    /**
    *
    * @var string object $objConfig Hold database configuration reading object for modules
    * @access public
    *
    */
    public $objDbConfig;

    /**
    *
    * The filename for the CSS cached file
    *
    * @var string $cacheFile
    * @Access public
    * 
    */
    public $cacheFile;
    
    /**
     *
     * Hold the root of the current skin 
     * 
     * @var string $skinRoot the root of the current skin
     */
    public $skinRoot;

    /**
    *
    * Constructor for the class
    * @access public
    * @return VOID
    *
    */
    public function init()
    {
        $this->objConfig = $this->getObject('altconfig','config');
        $this->objDbConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->skinRoot = $this->objConfig->getskinRoot();      
    }

    /**
    *
    * Cache the _Common2 CSS stylesheets
    *
    * @return string current skin
    * @access public
    *
    */
    public function cacheCommon()
    {
        $sitePath = $this->objConfig->getSiteRootPath();
        $cacheFile = $sitePath . 'cache.css';
        // Check if file exists and check its age if it needs a rewrite
        if ($this->checkCache($cacheFile)) {
            //We need to build the cache.
            $skinRoot = $this->objConfig->getskinRoot();
            $commonDir = '_common2/css/';
            $cssPath = $sitePath . $skinRoot . $commonDir;
            //$cssFiles = glob($cssPath . "*.css"); can't glob because order is wrong
            $cssFiles = $this->getCssFileList();
            $this->buildCache($cssFiles, $cacheFile, $cssPath);
        }
    }
    
    /**
     * 
     * Check if we need to rewrite the CSS cache based on if it exist and if its
     * TTL has expired.
     * 
     * @param type $cacheFile string The path to the file
     * @return boolean TRUE|FALSE
     * 
     */
    public function checkCache($cacheFile)
    {
        if (file_exists($cacheFile)) {
            // Check if it has expired.
            $cacheTime = @filemtime($cacheFile);
            // Get the cache TTL
            $ttl = $this->objDbConfig->getValue('csscache_ttl', 'skin');
            if ((!$cacheTime) || (time() - $cacheTime >= $ttl)) {
                return TRUE;
            } else {
                // The cache is still good.
                return FALSE;
            }
        } else {
            return TRUE;
        }
    }
    
    
    /**
     * 
     * Write the cache file
     * 
     * @param type $cssFiles An array of CSS files to cache.  
     * @param type $cacheFile The file to add to the cache.
     * @param type $cssPath The path to the CSS files that we are caching.
     * @return int $counter The counter in case we want to use it.
     * 
     */
    public function buildCache($cssFiles, $cacheFile, $cssPath)
    {
        $counter=1;
        $tmp ="";
        foreach ($cssFiles as $cssFile) {
            $cssFile = $cssPath . $cssFile;
            if (file_exists($cssFile)) {
                $css = file_get_contents($cssFile);
                $css = $this->optimize($css);
                if ($counter == 1) {
                    // Create it or overwrite it the first time around
                    $timeWritten = "/* Written: " . date("F j, Y, g:i a") . " */\n";
                    file_put_contents($cacheFile, $timeWritten);
                }
                // Append after the first one
                file_put_contents($cacheFile, $css, FILE_APPEND);
            }
            $counter++;
        }
        // Send back the number of files writen.
        return $counter;
    }

    /**
     *
     * Get rid os spaces, newlines, tabs, comments, etc to optimize
     * the CSS (minimize)
     * 
     * @param string $css The CSS to minimize
     * @return string $css the Minimized CSS
     * @access public
     *
     */
    public function optimize($css)
    {
      // remove comments
      $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
      // remove tabs, spaces, newlines, etc.
      $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
      return $css;
    }
    
    
    /**
     * Build an array of files to optimize. Note that we cannot use GLOB because
     * the files will be in the wrong order. 
     * 
     * @return string An array of files to optimize
     * @access Public
     * 
     */
    public function getCssFileList()
    {
        $cssArray = array(
            "layout.css",
            "common2.css",
            "htmlelements.css",
            "creativecommons.css",
            "forum.css",
            "calendar.css",
            "cms.css",
            "stepmenu.css",
            "switchmenu.css",
            "colorboxes.css",
            "manageblocks.css",
            "facebox.css",
            "modernbrickmenu.css",
            "jquerytags.css",
            "overlappingtabs.css",
            "login.css",
            "navigationmenu.css",
            "modulespecific.css",
            "cssdropdownmenu.css",
            "sexybuttons.css",
            "chisimbacanvas.css",
            "filemanager.css",
        );
        return $cssArray;
    }
}
?>