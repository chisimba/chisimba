<?php
/**
* Class used to invoke realtime applet depending on the user's moded
*
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
* @package   webpresent
* @author    David Wafula <dwafula[AT]gmail[DOT]com>
* @copyright 2008 UWC and AVOIR
* @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License 
* @version   $Id: messagestpl_class_inc.php 7192 2007-09-21 19:12:48Z dkeats $
* @link      http://avoir.uwc.ac.za
*/

// security check - must be included in all scripts
if(!$GLOBALS['kewl_entry_point_run']){
    die("You cannot view this page directly");
}
// end of security


class realtimemanager extends Object
{
 /**
     * This function generates a random string. This is used as id for the java slides server as well as
     * the client (applet)
     * @param <type> $length
     * @return <type>
     */
    public function randomString($length)
    {
        // Generate random 32 charecter string
        $string = md5(time());

        // Position Limiting
        $highest_startpoint = 32-$length;

        // Take a random starting point in the randomly
        // Generated String, not going any higher then $highest_startpoint
        $randomString = substr($string,rand(0,$highest_startpoint),$length);

        return $randomString;

    }


        /**
         *This sends an email to all invited guests
         * @param <type> $emails
         * @param <type> $agenda
         * @param <type> $url
         */
    public function sendInvitation($emails,$agenda,$url)
    {
        $msg=$this->objUser->fullname(). ' has invited you for a realtime presentation. The agenda of the session is "'.$agenda.'". To join, simply click on '.$url;
        $emails.=',';

        //should be separated by commas
        $objMailer = $this->getObject('mailer', 'mail');
        $token = strtok($emails,",");
        while ($token){

            $objMailer->setValue('to', $token);
            //$objMailer->setValue('cc', $emails);
            $objMailer->setValue('from', $this->objUser->email());
            $objMailer->setValue('fromName', $this->objUser->fullname());
            $objMailer->setValue('subject', 'You have been invited for realtime presentation at '.$this->objConfig->getSiteName());
            $objMailer->setValue('body', $msg);
            $objMailer->send();

            $token = strtok(",");
        }
    }


    /**
     *
     * Function to invoke the presenter applet
     *
     */
    public function testapplet()
    {
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $supernodeHost=$objSysConfig->getValue('SUPERNODE_HOST', 'realtime');
        $supernodePort=$objSysConfig->getValue('SUPERNODE_PORT', 'realtime');
        $this->setVarByRef('supernodeHost', $supernodeHost);
        $this->setVarByRef('supernodePort', $supernodePort);

        //$slideServerId=$this->randomString(32);
        $slideServerId=$this->objConfig->serverName();

        //if(!$this->slideServerRunning()){
        $this->startSlidesServer($slideServerId);
        //}
        $id= $this->getParam('id');
        $title=$this->getParam('agendaField');
        $participants=$this->getParam('participants');
        $url=$this->objConfig->getsiteRoot().'/index.php?module=webpresent&action=testaudienceapplet&id='.$id.'&agenda='.$title;
        $this->sendInvitation($participants,$title,$url);
        $filePath=$this->objConfig->getContentBasePath().'/webpresent/'.$id;
        $this->setVarByRef('filePath', $filePath);
        $this->setVarByRef('sessionTitle',$title);
        $this->setVarByRef('sessionid', $id);
        $this->setVarByRef('slideServerId', $slideServerId);
        $this->setVarByRef('isPresenter', 'true');

        return "presenter-applet_php.php";
    }

        /**
         * Function to test if slides server is running or not
         * @return <type>
         */
    public function slideServerRunning()
    {

        $result = array();
        $cmd='ps aux | grep java';
        $needle=' avoir.realtime.tcp.base.SlidesServer';
        exec( $cmd, $result);
        foreach ($result as $v ){

            if($this->in_str($needle,$v)){

                return true;
            }
        }
        return false;
    }
        /**
         * Given a hay, look for the needle, return true if found
         * @param <type> $needle
         * @param <type> $haystack
         * @return <type>
         */
    public function in_str($needle, $haystack)
    {
        return (false !== strpos($haystack, $needle))  ? true : false;

    }

        /**
         * This start a slide server. This function is intended to be invoked
         * from an outside embedded appllication
         */
    public function __runslideserver()
    {
        $slideServerId=$this->getParam("slideServerId");
        $this->startSlidesServer($slideServerId);
        echo 'started slide server';
    }
       /**
        * Function to invoke the audience applet
        * @return <type>
        */
    public function showapplet()
    {
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $supernodeHost=$objSysConfig->getValue('SUPERNODE_HOST', 'realtime');
        $supernodePort=$objSysConfig->getValue('SUPERNODE_PORT', 'realtime');
        $this->setVarByRef('supernodeHost', $supernodeHost);
        $this->setVarByRef('supernodePort', $supernodePort);

        $slideServerId=$this->randomString(32);
        //$slideServerId=$this->objConfig->serverName();

        //   if(!$this->slideServerRunning()){

        $this->startSlidesServer($slideServerId);
        //   }
        $id= $this->getParam('id');
        $title=$this->getParam('agenda');

        $filePath=$this->objConfig->getContentBasePath().'/webpresent/'.$id;
        $this->setVarByRef('filePath', $filePath);
        $this->setVarByRef('sessionTitle',$title);

        $this->setVarByRef('sessionid', $id);
        $this->setVarByRef('slideServerId',$slideServerId);
        $this->setVarByRef('isPresenter', $ispresenter);

        return "showapplet_php.php";
    }



        /**
         * Start an instance of the slide server, each with a unique id
         * @param <type> $slideServerId
         */
    function startSlidesServer($slideServerId)
    {
        $objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $port=$objSysConfig->getValue('WHITEBOARDPORT', 'realtime');
        $minMemory=$objSysConfig->getValue('MIN_MEMORY', 'realtime');
        $supernodeHost=$objSysConfig->getValue('SUPERNODE_HOST', 'realtime');
        $supernodePort=$objSysConfig->getValue('SUPERNODE_PORT', 'realtime');

        $maxMemory=$objSysConfig->getValue('MAX_MEMORY', 'realtime');
        //  $cmd = "java -Xms".$minMemory."m -Xmx".$maxMemory."m -cp .:".
        $this->objConfig = $this->getObject('altconfig', 'config');
        $cmd = "java -Xms64m -Xmx128m -cp ";
        $cmd.=$this->objConfig->getModulePath()."/realtime/resources/realtime-common-1.0.2.jar:";
        $cmd.=$this->objConfig->getModulePath()."/realtime/resources/realtime-classroom-base-1.0.2.jar:";
        $cmd.=$this->objConfig->getModulePath()."/realtime/resources/realtime-instructor-1.0.2.jar:";
        $cmd.=$this->objConfig->getModulePath()."/realtime/resources/realtime-user-1.0.2.jar:";
        $cmd.=$this->objConfig->getModulePath()."/realtime/resources/realtime-launcher-1.0.2.jar  avoir.realtime.classroom.SlidesServer ".$slideServerId." ".$supernodeHost." ".$supernodePort." >/dev/null &";
      //  echo $cmd;
        system($cmd,$return_value);
    }

}
?>
