<?php

/**
 * Location controller class.
 * 
 * Class to control the Location module.
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
 * @package   location
 * @author    Charl van Niekerk <charlvn@charlvn.za.net>
 * @copyright 2008 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: controller.php 11749 2008-12-07 19:42:15Z charlvn $
 * @link      http://avoir.uwc.ac.za
 * @see       http://fireeagle.yahoo.net
 */

class location extends controller
{
    /**
     * Instance of the location library object.
     *
     * @access protected
     * @var object
     */
    protected $objLocationOps;

    /**
     * Standard constructor to load the necessary resources
     * and populate the new object's instance variables.
     *
     * @access public
     */
    public function init()
    {
        // Load the location library
        $this->objLocationOps = $this->getObject('locationops', 'location');
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
     * The callback action for the callback from Fire Eagle.
     *
     * @access private
     */
    private function actionCallback()
    {
        $this->objLocationOps->handleFireEagleCallback();
        $this->objLocationOps->update();
        $this->nextAction(NULL);
    }

    /**
     * The synchronise action for refreshing the location info cache.
     *
     * @access private
     */
    private function actionSynchronise()
    {
        $this->objLocationOps->synchroniseFireEagle();
    }

    /**
     * The map action for displaying a map of the location module users.
     *
     * @access private
     * @return string The template name.
     */
    private function actionMap()
    {
        $this->objLocationOps->setupMap(TRUE);

        return 'map_tpl.php';
    }

    /**
     * The default action to be performed when no valid action is specified.
     *
     * @access private
     */
    private function actionDefault()
    {
        $this->checkFireEagle();
        $this->objLocationOps->setupMap();
        $this->objLocationOps->setTemplateVars();

        return 'default_tpl.php';
    }

    /**
     * Checks if the user is authenticated with Fire Eagle, otherwise redirects to get authenticated.
     *
     * @access private
     */
    private function checkFireEagle()
    {
        if (!$this->objLocationOps->isFireEagleAuthenticated()) {
            $url = $this->objLocationOps->getFireEagleAuthoriseUrl();
            header("Location: $url");
            exit;
        }
    }

    /**
     * Checks Fire Eagle for updates to the user's location.
     *
     * @access private
     */
    private function actionUpdate()
    {
        $this->objLocationOps->update();
        $this->nextAction(NULL);
    }

    /**
     * Enables Twitter integration for the current user.
     *
     * @access private
     */
    private function actionEnabletwitter()
    {
        $this->objLocationOps->enableTwitter();
        $this->nextAction(NULL);
    }

    /**
     * Disables Twitter integration for the current user.
     *
     * @access private
     */
    private function actionDisabletwitter()
    {
        $this->objLocationOps->disableTwitter();
        $this->nextAction(NULL);
    }

    /**
     * Overide the login object in the parent class.
     *
     * @access public
     * @param  string $action The name of the action
     * @return bool
     */
    public function requiresLogin($action)
    {
        $publicActions = array('synchronise', 'map');

        return !in_array($action, $publicActions);
    }
}

?>
