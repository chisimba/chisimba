<?php

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
 * Library class to provide easy access to the Brightkite API.
 *
 * @author    Charl van Niekerk <charlvn@charlvn.za.net>
 * @copyright GNU/GPL, AVOIR
 * @package   brightkite
 * @access    public
 * @version   $Id: brightkiteops_class_inc.php 11976 2008-12-29 22:11:41Z charlvn $
 */
class brightkiteops extends object
{
    protected $objJson;

    /**
     * Standard constructor to load the necessary resources
     * and populate the new object's instance variables.
     */
    public function init()
    {
       $this->objJson = $this->getObject('json', 'utilities');
    }

    public function getCheckins($user)
    {
        $json = file_get_contents("http://brightkite.com/people/$user/objects.json?filters=checkins");
        $checkins = $this->objJson->decode($json);

        return $checkins;
    }

    public function getCheckinPlaces($user)
    {
        $checkins = $this->getCheckins($user);
        $places = array();
        foreach ($checkins as $checkin) {
            $places[] = $checkin['place'];
        }

        return $places;
    }

    public function searchPlaces($query)
    {
        $json = file_get_contents('http://brightkite.com/places/search.json?q=' . urlencode($query));
        $place = $this->objJson->decode($json);

        return $place;
    }

    public function getNotes($user)
    {
        $json = file_get_contents("http://brightkite.com/people/$user/objects.json?filters=notes");
        $notes = $this->objJson->decode($json);

        return $notes;
    }

    public function getNoteBodies($user)
    {
        $notes = $this->getNotes($user);
        $bodies = array();
        foreach ($notes as $note) {
            $bodies[] = $note['body'];
        }

        return $bodies;
    }

    public function postCheckin($user, $password, $place)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, "http://brightkite.com/places/$place/checkins.xml");
        curl_setopt($curl, CURLOPT_USERPWD, "$user:$password");
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_exec($curl);
        curl_close($curl);
    }

    public function postNote($user, $password, $note)
    {
        $checkins = $this->getCheckins($user);
        $lastCheckinPlaceId = $checkins[0]['place']['id'];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_URL, "http://brightkite.com/places/$lastCheckinPlaceId/notes.xml");
        curl_setopt($curl, CURLOPT_USERPWD, "$user:$password");
        curl_setopt($curl, CURLOPT_POSTFIELDS, 'note[body]=' . urlencode($note));
        curl_exec($curl);
        curl_close($curl);
    }
}

?>
