<?php

/**
 * This class provides varoius utilities for essays
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

 * @author
 * @copyright  2009 AVOIR
 */
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
class essayutil extends object {
    function init() {
        $this->objessays = $this->getObject('dbessays');

    }

    function generateJNLP() {
        $objAltConfig = $this->getObject('altconfig','config');
        $modPath=$objAltConfig->getModulePath();
        $replacewith="";
        $docRoot=$_SERVER['DOCUMENT_ROOT'];
        $resourcePath=str_replace($docRoot,$replacewith,$modPath);
        $codebase="http://" . $_SERVER['HTTP_HOST']."/".$resourcePath.'/efl/resources/';
        $essaycontent=$this->objessays->getEssayContent($storyid);
        $str=
            '<jnlp spec="1.0+" codebase="'.$codebase.'">
    <information>
        <title>EFL</title>
        <vendor>WITS eLearn</vendor>
        <description>EFL</description>
        <homepage href="http://www.wits.ac.za"/>
        <description kind="short">EFL</description>
        <icon href="'.$codebase.'/images/logo.png"/>
        <icon kind="splash" href="'.$codebase.'/images/splash_realtime.png"/>
        <offline-allowed/>
    </information>
    <resources>
        <j2se version="1.5+" />
        <jar href="jefla.jar" />
        <jar href="swing-layout-1.0.3.jar" />
        <jar href="looks-2.3.0.jar" />
    </resources>
   <application-desc    main-class="jefla.Main">

       <argument>'.$essaycontent.'</argument>

     </application-desc>

    <security>
        <all-permissions/>
    </security>
</jnlp>
';
        $myFile = $modPath.'efl/resources/jefla.jnlp';
        $fh = fopen($myFile, 'w') or die("can't open file");
        fwrite($fh, $str);
        fclose($fh);
    }
    /**
     * create a JNLP file to launch the marker
     */
    function showJavaMarker() {
        $objAltConfig = $this->getObject('altconfig','config');
        $modPath=$objAltConfig->getModulePath();
        $replacewith="";
        $docRoot=$_SERVER['DOCUMENT_ROOT'];
        $resourcePath=str_replace($docRoot,$replacewith,$modPath);
        $codebase="http://" . $_SERVER['HTTP_HOST']."/".$resourcePath.'/efl/resources/';
        $str='
                        <?xml version="1.0" encoding="utf-8"?>
                        <jnlp spec="1.0+" codebase="'.$codebase.'">
                        <information>
                        <title>EFL</title>
                        <vendor>WITS eLearn</vendor>
                        <description>EFL</description>
                        <homepage href="http://www.wits.ac.za"/>
                        <description kind="short">EFL</description>
                        <icon href="'.$codebase.'/images/logo.png"/>
                        <icon kind="splash" href="'.$codebase.'/images/splash_realtime.png"/>
                        <offline-allowed/>
                        </information>
                        <resources>
                        <j2se version="1.5+" />
                        <jar href="efl.jar" />
                        </resources>
                        <application-desc main-class="efla.EflaApp">
                        </application-desc>
                        <security>
                        <all-permissions/>
                        </security>
                        </jnlp>

                        ';
        return $str;
    }
}
?>
