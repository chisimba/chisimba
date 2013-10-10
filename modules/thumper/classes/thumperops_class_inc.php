<?php

/**
 * Facade class to the Thumper library.
 *
 * The Thumper library is an AMQP client to RabbitMQ and similar.
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
 * @package   thumper
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: thumperops_class_inc.php 20376 2011-01-25 02:01:14Z charlvn $
 * @link      http://avoir.uwc.ac.za/
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
// end security check

/**
 * Facade class to the Thumper library.
 *
 * The Thumper library is an AMQP client to RabbitMQ and similar.
 *
 * @category  Chisimba
 * @package   thumper
 * @author    Charl van Niekerk <charlvn@charlvn.com>
 * @copyright 2010 Charl van Niekerk
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General Public License
 * @version   $Id: thumperops_class_inc.php 20376 2011-01-25 02:01:14Z charlvn $
 * @link      http://avoir.uwc.ac.za/
 */
class thumperops extends object
{
    /**
     * Instance of the dbsysconfig class of the sysconfig module.
     *
     * @access private
     * @var    object
     */
    private $objSysConfig;

    /**
     * The hostname of the AMQP server.
     *
     * @access private
     * @var    string
     */
    private $host;

    /**
     * The port of the AMQP server.
     *
     * @access private
     * @var    integer
     */
    private $port;

    /**
     * The username to use.
     *
     * @access private
     * @var    string
     */
    private $user;

    /**
     * The password to use.
     *
     * @access private
     * @var    string
     */
    private $pass;

    /**
     * The vhost to use.
     *
     * @access private
     * @var    string
     */
    private $vhost;

    /**
     * Instance of the Thumper Producer class.
     *
     * @access private
     * @var    object
     */
    private $objProducer;

    /**
     * Instance of the Thumper Consumer class.
     *
     * @access private
     * @var    object
     */
    private $objConsumer;

    /**
     * Initialises the object properties.
     *
     * @access public
     */
    public function init()
    {
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');
        $this->host = $this->objSysConfig->getValue('host', 'thumper');
        $this->port = $this->objSysConfig->getValue('port', 'thumper');
        $this->user = $this->objSysConfig->getValue('user', 'thumper');
        $this->pass = $this->objSysConfig->getValue('pass', 'thumper');
        $this->vhost = $this->objSysConfig->getValue('vhost', 'thumper');
    }

    /**
     * Returns an instance of the Thumper Producer class.
     *
     * @access public
     * @return object
     */
    public function getProducer()
    {
        if (!is_object($this->objProducer)) {
            include $this->getResourcePath('Thumper/Producer.php');
            $this->objProducer = new Producer($this->host, $this->port, $this->user, $this->pass, $this->vhost);
        }

        return $this->objProducer;
    }

    /**
     * Returns an instance of the Thumper Consumer class.
     *
     * @access public
     * @return object
     */
    public function getConsumer()
    {
        if (!is_object($this->objConsumer)) {
            include $this->getResourcePath('Thumper/Consumer.php');
            $this->objConsumer = new Consumer($this->host, $this->port, $this->user, $this->pass, $this->vhost);
        }

        return $this->objConsumer;
    }
}
