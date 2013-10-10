<?php

/**
 * Class to embed codeto display a flickshow slideshow
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
 * @package   flickrshow
 * @author    Nic Appleby <nappleby@uwc.ac.za>
 * @copyright 2010 AVOIR
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: modules_class_inc.php 12874 2009-03-16 09:21:30Z paulscott $
 * @link      http://avoir.uwc.ac.za
 */

/* ------------------- modules class extends dbTable ------------- */
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

class flickrshowembed extends object {
    
    public function init() {
        $this->objConfig = $this->getObject('altconfig', 'config');
    }
    
    public function embedFlickrshow($flickrshow) {
        $id = uniqid();
        
        $meta   = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                   <meta http-equiv="imagetoolbar" content="false" />
                   <meta name="MSSmartTagsPreventParsing" content="true" />';
        
        $script = $this->getResourceURI('flickrshow.unpacked.js', 'flickrshow');
        
        $css    = '<!--[if IE 6]>
                   <link rel="stylesheet" type="text/css" media="screen" href="'.$flickrshow->skindir.'ie-6-win.css">
                   <![endif]-->
                   <!--[if IE 7]>
                   <link rel="stylesheet" type="text/css" media="screen" href="'.$flickrshow->skindir.'ie-7-win.css">
                   <![endif]-->';
        $file   = "<script type='text/javascript' src='$script'></script>";
        
        $javascript = "var flickrshow_$id = new flickrshow('flickrshow_$id', {url: '".$this->objConfig->getsiteRoot()."', flickr_photoset: '$flickrshow->photoset', skindir: '$flickrshow->skindir', speed: $flickrshow->speed, size: '$flickrshow->size', autostart: $flickrshow->autostart});";
        $object = "<script type='text/javascript'>$javascript</script>";
        	
        $this->appendArrayVar('headerParams', $meta);
        $this->appendArrayVar('headerParams', $css);
        $this->appendArrayVar('headerParams', $file);
        $this->appendArrayVar('headerParams', $object);
        return "<div id='flickrshow_$id' style='width:$flickrshow->width; height:$flickrshow->height'></div>";
        
    }
    
}