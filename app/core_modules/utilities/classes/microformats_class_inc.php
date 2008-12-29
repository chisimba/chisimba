<?php
// security check - must be included in all scripts
if (!$GLOBALS['kewl_entry_point_run']) {
    die("You cannot view this page directly");
}
// end security check

/**
 * Class to expose certain microformats in Chisimba pages.
 *
 * @category  Chisimba
 * @package   utilities
 * @author    Paul Scott <pscott@uwc.ac.za>
 * @copyright GNU/GPL AVOIR/UWC 2006
 * @license   http://www.gnu.org/licenses/gpl-2.0.txt The GNU General
Public License
 * @version   $Id$
 * @link      http://avoir.uwc.ac.za
 */

class microformats extends object
{
    /**
    * Constructor
    */
    public function init()
    {
        require_once($this->getResourcePath('microformats/phpMicroformats.class.php'));
        // others?
        
    }
    
    /**
     * Output an HCard
     *
     * @param array $personalData
     * @return hCard
     * @example 
     * 
     * $myPersonalData = array(
    'name'         => 'John Doe',
    'email'     => 'abuse-me@this-host-does-not-exist.info',
    'org'         => array(
        'name'         => 'The virtual company',
        'title'     => 'General chief of all'
    ),

    'location'    => array (
        'street'    => '15x Main street',
        'town'        => 'Jonestown',
        'zip'        => '22912',
        'state'        => 'Some country',
        'country'    => 'Big Country'    
    ),    

    'phone'        => array(
        'home'        => '+911 123 66 71 292',
        'cell'        => '+911 123 88 72 121'    
    ),

    'photo'        => 'http://url/to/some/depictionofme.png',
        
    'im'        => array(
        'skype'        => 'echo',
        'aim'        => 'dudewithnolife'
    )
);
     */
    public function showHcard($personalData)
    {
        return phpMicroformats::createHCard($personalData);
    }
    
    /**
     * Create an HCalendar
     *
     * @param array $event
     * @example 
     * $myEvent = array(
    'name'         => 'Release party of Chisimba',
    'begin'        => time(),
    'end'        => time()+2*60*60, // duration: 2 hours

    'location'    => array (
        'street'    => '15z Main street',
        'town'        => 'Jonestown',
        'zip'        => '22912',
        'state'        => 'Western Cape',
        'country'    => 'South Africa'    
    ),

    'url'        => 'http://chisimba.uwc.ac.za'
);
     */
    public function showHcalendar($event)
    {
        return phpMicroformats::createHCalendar($event);
    }

}
?>