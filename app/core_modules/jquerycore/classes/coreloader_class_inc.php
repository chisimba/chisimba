<?php
/**
 *
 * Main class for the jquerycore module
 *
 * This class loads the jquery and also performs checks on versions and duplications
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
 * @package   jquerycore
 * @author    Kevin Cyster kcyster@gmail.com
 * @copyright 2007 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   0.001
 * @link      http://www.chisimba.com
 *
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
*
* Main class for the jquerycore module
*
* This module loads the jquery and also performs checks on versions and duplications
*
* @package   jquerycore
* @author    Kevin Cyster kcyster@gmail.com
*
*/
class coreloader extends object
{
    /**
     * 
     * Variable to hold the user interface / core dependency object
     * 
     * @access proteced
     * @var object
     */
    protected $objUiDeps;

    /**
     * 
     * Variable to hold the plugin / core dependency opbject
     * 
     * @access proteced
     * @var object
     */
    protected $objPluginDeps;

    /**
     * 
     * Variable to hold the current jquery core version
     * 
     * @access protected
     * @var string
     */
    protected $core;

    /**
     * 
     * Variable to hold the current jquery user interface version
     * 
     * @access protected
     * @var string
     */
    protected $ui;

    /**
     * 
     * Variable to hold the user interface theme name
     * 
     * @access protected
     * @var string
     */
    protected $theme = 'base';
    
    /**
     *
     * Variable to hold the valid core versions
     * 
     * @access protected 
     * @var array
     */
    protected $validCore;

    /**
     *
     * Variable to hold the valid plugins
     * 
     * @access protected 
     * @var array
     */
    protected $validPlugins;

    /**
     *
     * Variable to hold the plugings
     * 
     * @access protected 
     * @var array
     */
    protected $plugins;

    /**
     *
     * Intialiser for the jquerycore class
     * @access public
     * @return VOID
     *
     */
    public function init()
    {
        // TESTING ONLY
        $this->objConfig = $this->getObject('altconfig', 'config');
        
        $this->getUiDeps();
        $this->getPluginDeps();
        $this->setDefaultCoreVersion();
    }
    
    /**
     *
     * Method to get the user interface / core dependencies from XML
     * 
     * @access private
     * @return VOID
     */
    private function getUiDeps()
    {
        $xmlFile = $this->getResourceUri('ui_dependencies.xml');
        $objDeps = simplexml_load_file($xmlFile);
        if ($objDeps)
        {
            $this->objUiDeps = $objDeps;
        }
    }
    
    /**
     *
     * Method to get the user interface / core dependencies from XML
     * 
     * @access private
     * @return VOID
     */
    private function getPluginDeps()
    {
        $xmlFile = $this->getResourceUri('plugin_dependencies.xml');
        $objDeps = simplexml_load_file($xmlFile);
        if ($objDeps)
        {
            $this->objPluginDeps = $objDeps;
            foreach ($this->objPluginDeps as $plugin)
            {
                $this->validPlugins[] = (string) $plugin['name'];
            }
        }
    }
    
    /**
     *
     * Method to set the default jquery core version.
     * Can be overridden by setCoreVersion or setUserInterfaceVersion
     * 
     * @access public
     * @return VOID
     */
    public function setDefaultCoreVersion()
    {
        // Live path
        // $path = 'core_modules/jquerycore/resources/core/';
        $path = $this->objConfig->getModuleURI() . 'jquerycore/resources/core/';
        if (is_dir($path))
        {
            if ($dh = opendir($path))
            {
                $this->validCore = array();
                while (($file = readdir($dh)) !== false)
                {
                    if (filetype($path.$file) == 'dir')
                    {
                        $key = str_replace('.', '', $file);
                        $this->validCore[$key] = $file;
                    }
                }                
                closedir($dh);
                ksort($this->validCore);
                
                $this->core = end($this->validCore);
                $this->setHighestUserInterfaceVersion();
            }
        }
    }
    
    /**
     *
     * Method to set the highest user interface version for a given core version.
     * 
     * @access private
     * @return VOID
     */
    private function setHighestUserInterfaceVersion()
    {
        $coreVersion = str_replace('.', '', $this->core);
        $uiArray = array();
        foreach ($this->objUiDeps as $version)
        {
            $temp = (string) $version->min_core_version;
            $minCore = str_replace('.', '', $temp);
            if ($minCore <= $coreVersion)
            {
                $ui = (string) $version->ui_version;
                $key = str_replace('.', '', $ui);
                $uiArray[$key] = $ui;
            }
        }
        
        if (!empty($uiArray))
        {
            ksort($uiArray);            
            $this->ui = end($uiArray);
        }
        else
        {
            $this->ui = NULL;
        }
    }

    /**
     *
     * Method to set the jquery core version
     * 
     * @access public
     * @param string $version The jquery core version
     * @return VOID
     */
    public function setCoreVersion($version)
    {
        if (!empty($version))
        {
            if (in_array($version, $this->validCore) && empty($this->plugins))
            {
                $this->core = $version;
                $this->setHighestUserInterfaceVersion();
            }
        }
    }

    /**
     *
     * Method to set the jquery user interface theme
     * 
     * @access public
     * @param string $theme The name of the CSS theme to use
     * @return VOID
     */
    public function setTheme($theme)
    {
        // Live path
        // $path = 'core_modules/jquerycore/resources/core/';
        $path = $this->objConfig->getModuleURI() . 'jquerycore/resources/ui/themes/';

        if (!empty($theme) && is_string($theme))
        {
            $themes = array();
            if (is_dir($path))
            {
                if ($dh = opendir($path))
                {
                    $themes = array();
                    while (($file = readdir($dh)) !== false)
                    {
                        if (filetype($path.$file) == 'dir')
                        {
                            $themes[] = $file;
                        }
                    }                
                    closedir($dh);
                }
            }
            if (in_array($theme, $themes))
            {
                $this->theme = $theme;
            }
        }
    }
    
    /**
     *
     * Method to set the plugings to load
     * 
     * @access public
     * @param array $plugins An array of plugin names
     * @return VOID
     */
    public function setPlugins(array $plugins)
    {
        if (!empty($plugins))
        {
            foreach ($plugins as $plugin)
            {
                if (in_array($plugin, $this->validPlugins))
                {
                    $this->plugins[] = $plugin;
                }                
            }
            if (!empty($this->plugins))
            {
                $this->plugins = array_unique($this->plugins);
            }
        }
    }

    /**
     *
     * Method to set a pluging to load
     * 
     * @access public
     * @param string $plugin The name of a plugin to load
     * @return VOID
     */
    public function setPlugin($plugin)
    {
        if (!empty($plugin) && is_string($plugin))
        {
            if (in_array($plugin, $this->validPlugins))
            {
                $this->plugins[] = $plugin;
            }
            if (!empty($this->plugins))
            {
                $this->plugins = array_unique($this->plugins);
            }
        }
    }
    
    /**
     *
     * Method to load the core jquery javascript.
     * 
     * @return string $string The jquery core file to be loaded
     */
    private function loadCore()
    {
        $coreFile = 'core/' . $this->core . '/jquery.min.js';
        $string = $this->getJavascriptFile($coreFile) . "\n";
        return $string;
    }
    
    /**
     *
     * Method to load the jquery user interface javascript.
     * 
     * @return string $string The jquery user interface file to be loaded
     */
    private function loadUi()
    {
        $uiFile = 'ui/javascript/' . $this->ui . '/jquery-ui.min.js';
        $string = $this->getJavascriptFile($uiFile) . "\n";
        return $string;
    }
    
    /**
     *
     * Method to load the jquery user interface theme.
     * 
     * @return string $string The jquery user interface theme to be loaded
     */
    private function loadTheme()
    {
        $themeFile = 'ui/themes/' . $this->theme . '/jquery.ui.all.css';
        $string = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri($themeFile).'" />' . "\n";
        return $string;
    }
    
    /**
     *
     * Method to add dependency plugins to the plugin list.
     * 
     * @return VOID 
     */
    private function addDependencies()
    {
        foreach ($this->plugins as $plugin)
        {
            foreach ($this->objPluginDeps as $xmlPlugin)
            {
                if ((string) $xmlPlugin['name'] == $plugin)
                {
                    if (array_key_exists('dependencies', (array) $xmlPlugin->version))
                    {
                        foreach ($xmlPlugin->version->dependencies->depends as $dependency)
                        {
                            $this->plugins[] = (string) $dependency;
                        }
                    }
                }
            }
        }
    }
    
    private function loadPlugins()
    {
        $string = '';
        $currentCore = str_replace('.', '', $this->core);
        foreach ($this->plugins as $plugin)
        {
            foreach ($this->objPluginDeps as $xmlPlugin)
            {
                if ((string) $xmlPlugin['name'] == $plugin)
                {
                    $core = str_replace('.', '', (string) $xmlPlugin->version->min_core_version);
                    if ($currentCore < $core)
                    {
                        $this->core = (string) $xmlPlugin->version->min_core_version;
                        $currentCore = str_replace('.', '', $this->core);
                    }
                    if (array_key_exists('deprecated_from', (array) $xmlPlugin->version))
                    {
                        $deprecatedFrom = str_replace('.', '', (string) $xmlPlugin->version->deprecated_from);
                        if ($currentCore > $deprecatedFrom)
                        {
                            break;
                        }
                    }
                    $version = (string) $xmlPlugin->version['ver'];
                    $fileName = (string) $xmlPlugin->version->file;                    
                    $file = 'plugins/' . $plugin . '/' . $version .'/' . $fileName;
                    $string .= $this->getJavascriptFile($file) . "\n";
                    
                    if (array_key_exists('css', (array) $xmlPlugin->version))
                    {
                        $fileName = (string) $xmlPlugin->version->css;                    
                        $file = 'plugins/' . $plugin . '/css/' . $fileName;
                        $string .= '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri($file).'">' . "\n";
                    }
                }
            }
        }
        return $string;
    }

    /**
     *
     * Method to load the jquery into the headers
     * 
     * @access public
     * @return VOID
     */
    public function load()
    {        
        $plugins = '';
        if (!empty($this->plugins))
        {
            $this->addDependencies();
            $plugins = $this->loadPlugins();
        }
        $string = $this->loadCore();
        $string .= '<script type="text/javascript">jQuery.noConflict();</script>' . "\n";
        $string .= $this->loadUi();
        $string .= $this->loadTheme();
        $string .= $plugins;
        $this->appendArrayVar('headerParams', $string);
    }
}
?>