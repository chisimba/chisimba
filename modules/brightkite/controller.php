<?php

/**
 * Brightkite controller class.
 * 
 * Class to control the Brightkite module.
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
 * @category  chisimba
 * @package   brightkite
 * @author    Charl van Niekerk <charlvn@charlvn.za.net>
 * @copyright 2008 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 11976 2008-12-29 22:11:41Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       http://brightkite.com
 */

class brightkite extends controller
{
    protected $objBrightkiteOps;

    /**
     * Standard constructor to load the necessary resources
     * and populate the new object's instance variables.
     *
     * @access public
     */
    public function init()
    {
        $this->objBrightkiteOps = $this->getObject('brightkiteops', 'brightkite');
    }

    /**
     * Standard dispatch method to handle the various possible actions.
     *
     * @access public
     */
    public function dispatch()
    {
        $action = $this->getParam('action');
        $method = 'action' . ucfirst($action);

        if (!method_exists($this, $method)) {
            $method = 'actionDefault';
        }

        return $this->$method();
    }

    /**
     * The default action.
     *
     * @access private
     */
    private function actionDefault()
    {
    }

    private function actionCheckins()
    {
        $user = $this->getParam('user');
        $checkins = $this->objBrightkiteOps->getCheckinPlaces($user);
        print_r($checkins);
    }

    private function actionNotes()
    {
        $user = $this->getParam('user');
        $notes = $this->objBrightkiteOps->getNoteBodies($user);
        print_r($notes);
    }

    private function actionPostnote()
    {
        $user = $this->getParam('user');
        $password = $this->getParam('password');
        $note = $this->getParam('note');
        $this->objBrightkiteOps->postNote($user, $password, $note);
    }

    private function actionPostcheckin()
    {
        $user = $this->getParam('user');
        $password = $this->getParam('password');
        $placeQuery = $this->getParam('place');
        $place = $this->objBrightkiteOps->searchPlaces($placeQuery);
        $this->objBrightkiteOps->postCheckin($user, $password, $place['id']);
    }

    /**
     * Overide the login method in the parent class.
     *
     * @access public
     * @param  string $action The name of the action.
     * @return bool
     */
    public function requiresLogin($action)
    {
        $publicActions = array();

        return !in_array($action, $publicActions);
    }
}

?>
