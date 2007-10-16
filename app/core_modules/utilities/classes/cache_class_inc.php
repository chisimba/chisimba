<?php

/**
 * Class to cache data to the file system
 *
 * This class allows developers to cache data to file system and retrieve it later.
 * Based on original by Edward Eliot
 *
 * @category  Chisimba
 * @package   utilities
 * @author    Tohir Solomons <tsolomons@uwc.ac.za>
 * @copyright 2007 Paul Scott
 * @version   Release: @package_version@
 * @link      http://avoir.uwc.ac.za
 * @see       http://www.ejeliot.com/pages/downloads-scripts
 */

/***************************************************************/
/* Cache - part of the PhpDelicious library

  Software License Agreement (BSD License)

  Copyright (C) 2005-2006, Edward Eliot.
  All rights reserved.

  Redistribution and use in source and binary forms, with or without
  modification, are permitted provided that the following conditions are met:

     * Redistributions of source code must retain the above copyright
       notice, this list of conditions and the following disclaimer.
     * Redistributions in binary form must reproduce the above copyright
       notice, this list of conditions and the following disclaimer in the
       documentation and/or other materials provided with the distribution.
     * Neither the name of Edward Eliot nor the names of its contributors
       may be used to endorse or promote products derived from this software
       without specific prior written permission of Edward Eliot.

  THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDER AND CONTRIBUTORS "AS IS" AND ANY
  EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
  DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE FOR ANY
  DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
  (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
  LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
  ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
  (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

  Last Updated:  14th March 2006                              */
/***************************************************************/



class cache extends Object
{
    /**
     * @var string $sFile Name of the file containing the contents contents
     */
    private $sFile;

    /**
     * @var string $sFileLock Name of the file that is a lock for the contents
     */
    private $sFileLock;

    /**
     * @var int $iCacheTime Period of Caching
     */
    private $iCacheTime;

    /**
     * @var string $cachePath path to the Caching Files
     */
    private $cachePath;

    /**
     * Constructor
     */
    public function init()
    {
        // Sets Path to Caching Directory

        // Loading Config
        $objConfig = $this->getObject('altconfig', 'config');
        // Load Directory Creator
        $objMkdir = $this->getObject('mkdir', 'files');

        // Create Path
        $path = $objConfig->getcontentBasePath().'/cache';

        // Load class to clean url
        $objCleanUrl = $this->getObject('cleanurl', 'filemanager');
        // Clean Url
        $path = $objCleanUrl->cleanUpUrl($path);

        // Check that path exists
        $objMkdir->mkdirs($path);

        // Set Path
        $this->cachePath = $path ;
    }

    /**
     * Method to setup a caching key
     * @param string $sKey Name of the Key
     * @param string $module Name of the Module
     * @param int $iCacheTime Period of Caching
     */
    function setup($sKey, $module, $iCacheTime)
    {
        $this->sFile = $this->cachePath.'/'.md5($module.'~'.$sKey).".txt";
        $this->sFileLock = $this->sFile.'.lock';
        $iCacheTime >= 10 ? $this->iCacheTime = $iCacheTime : $this->iCacheTime = 10;
    }

    /**
     * Method to check whether cache content is still Valid
     */
    function check()
    {
        // Check if File Lock E
        if (file_exists($this->sFileLock)) {
            return true;
        }
        return (file_exists($this->sFile) && ($this->iCacheTime == -1 || time() - filemtime($this->sFile) <= $this->iCacheTime));
    }

    /**
     * Method to check if the cached content exists
     */
    function exists()
    {
        return (file_exists($this->sFile) || file_exists($this->sFileLock));
    }

    /**
     * Method to set the contents for the cache
     * @param string $vContents
     * @return boolean Whether cache content was successfully set or not
     */
    function set($vContents)
    {
        if (!file_exists($this->sFileLock)) {
            if (file_exists($this->sFile)) {
               copy($this->sFile, $this->sFileLock);
            }
            $oFile = fopen($this->sFile, 'w');
            fwrite($oFile, serialize($vContents));
            fclose($oFile);
            if (file_exists($this->sFileLock)) {
               unlink($this->sFileLock);
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Method to retrieve the cached contents
     * @return string
     */
    function get()
    {
        if (!$this->exists()) {
            return FALSE;
        } else if (file_exists($this->sFileLock)) {
            return unserialize(file_get_contents($this->sFileLock));
        } else {
            return unserialize(file_get_contents($this->sFile));
        }
    }
}
?>