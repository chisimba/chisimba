<?php
require_once("debug.php");
require_once("db.php");
require_once("orm.php");

// Define the ultimate LTI object to provide the tool its data
class LTIObject {
   private $launchdata = false;
   private $usr = false;
   private $crs = false;
   private $org = false;
   private $memb = false;

   function __construct($launchdata) {
      $this->launchdata = $launchdata;
   }

   function launch($key = false) {
      if ( $key ) {
         return $this->launchdata[$key];
      }
      return $this->launchdata;
   }

   function user($key = false) {
      if ( ! $this->launchdata || ! $this->launchdata[user_id] ) return false;
      $userid = $this->launchdata[user_id];
      if ( ! $this->usr ) {
          $this->usr = new ORM("user", "user_id", "lti_user");
          $this->usr->get($userid);
      }
      if ( ! $this->usr->id() ) return false;
      $data = $this->usr->data();
      if ( $key ) return $data[$key];
      return $data;
   }

   function course($key = false) {
      if ( ! $this->launchdata || ! $this->launchdata[course_id] ) return false;
      $courseid = $this->launchdata[course_id];
      if ( ! $this->crs ) {
          $this->crs = new ORM("course", "course_id", "lti_course");
          $this->crs->get($courseid);
      }
      if ( ! $this->crs->id() ) return false;
      $data = $this->crs->data();
      if ( $key ) return $data[$key];
      return $data;
   }

   function org($key = false) {
      if ( ! $this->launchdata || ! $this->launchdata[org_id] ) return false;
      $orgid = $this->launchdata[org_id];
      if ( ! $this->org ) {
          $this->org = new ORM("org", "org_id", "lti_org");
          $this->org->get($orgid);
      }
      if ( ! $this->org->id() ) return false;
      $data = $this->org->data();
      if ( $key ) return $data[$key];
      return $data;
   }

   function memb($key = false) {
      if ( ! $this->launchdata || ! $this->launchdata[user_id] 
           || ! $this->launchdata[course_id] ) return false;
      if ( ! $this->memb ) {

         $this->memb = new ORM("membership", false,"lti_membership");
         if ( ! $this->memb ) return false;
         $this->memb->read( array( "course_id" => $this->launchdata[course_id],
                             "user_id" => $this->launchdata[user_id]) ) ;
      }
      if ( ! $this->memb->id() ) return false;
      $data = $this->memb->data();
      if ( $key ) return $data[$key];
      return $data;
   }

   function isInstructor() {
     $memb = $this->memb(role_id);
     return ($memb == 2);
   }

}
?>
