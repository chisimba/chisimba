<?php
	
	//require_once "archive_class_inc.php";
	
	class unknownarchive extends archive{
	
		public function init( $filename ){
			parent::init( $filename );
		}
		
		public function extractTo( $foldername ){
			return "Class: unknownarchive - Method: extractTo()";
		}
			
	}

?>
