<?php

/*

 * This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the
 *  Free Software Foundation, Inc.,
 *  59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 * 

 */

require_once 'Sabre/autoload.php';

class dav extends object {

    function init() {
        
    }

    public function runDav() {
        $rootDirectory = new Sabre_DAV_FS_Directory('usrfiles/users/1');
        $server = new Sabre_DAV_Server($rootDirectory);
        $server->setBaseUri('/chisimba/index.php?module=webdav');
        $lockBackend = new Sabre_DAV_Locks_Backend_File('/usrfiles/users/1/locks');
        $lockPlugin = new Sabre_DAV_Locks_Plugin($lockBackend);
        $server->addPlugin($lockPlugin);

        $plugin = new Sabre_DAV_Browser_Plugin();
        $server->addPlugin($plugin);
        
        $server->addPlugin(new Sabre_DAV_Mount_Plugin());

        $server->exec();
    }

}

?>
