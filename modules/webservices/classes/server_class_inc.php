<?php
include 'SCA/SCA.php';

/**
 * @service
 * @binding.soap
 * @binding.jsonrpc
 * @binding.xmlrpc
 * @binding.restrpc
 */
 
 class server extends object
 {
 	
 	/**
 	 * Init method
 	 * 
 	 * 
 	 */
 	public function init()
 	{}
 	
     /**
      * Method to say hello
      *
      * @param string $name
      * @return string
      */
      public function hello($name)
      {
          return 'hello '.$name;
      }
      
      /**
       * Method to grab all blog posts by userid
       * 
       * @param string $userid the user id
       * @return array $posts the array of posts
       */
      public function getBlogPosts($userid)
      {
      	return 'blogs'. 'posts'. 'whatever';
      }
}
?>