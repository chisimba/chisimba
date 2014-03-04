<?php
/**
 * WURFL API
 *
 * LICENSE
 *
 * This file is released under the GNU General Public License. Refer to the
 * COPYING file distributed with this package.
 *
 * Copyright (c) 2008-2009, WURFL-Pro S.r.l., Rome, Italy
 *
 *
 *
 * @category   WURFL
 * @package    WURFL
 * @copyright  WURFL-PRO SRL, Rome, Italy
 * @license
 * @version    $id$
 * 
 * 
 */
final class WURFL_FileManager {
	
	// prevent instantiation
	private function __construct() {
	}
	private function __clone() {
	}
	
	/**
	 * Returns a previously serialized data
	 *
	 * @param  string $fileName name of the file to unserialize
	 * @param  string $dir sub-directory where to find the file
	 * @return mixed
	 */
	public static function fetch($fileName, $dir) {
		if (! isset ( $fileName ) || is_null ( $fileName )) {
			return NULL;
		}
		
		$fullName = self::getFileName ( $fileName, self::getDeepDirPath ( $dir, $fileName ) );
		if (! file_exists ( $fullName ))
			return NULL;
		return @unserialize(file_get_contents($fullName));
	}
	
	/**
	 * Saves a serialized version of the given data
	 *
	 * @param string $key name of the file
	 * @param mixed $data
	 * @param string $dir directory where to put the data
	 */
	public static function save($key, $data, $dir, $mtime = 0) {
		
		$fileName = self::getFileName ( $key, self::createDeepDirPath ( $dir, $key ) );
		if (@file_put_contents ( $fileName, serialize ( $data ), LOCK_EX )) {			
			$mtime = $mtime > 0 ? $mtime : time();
			@chmod ( $fileName, 0777 );
			return @touch ( $fileName, $mtime );
		}		
	}
	
	
	public static function fileModificationTime($key, $dir) {
		$fileName = self::getFileName ( $key, self::createDeepDirPath ( $dir, $key ) );
		return filemtime($fileName); 	
	}
	
	public static function delete($key, $dir) {
		$fileName = self::getFileName ( $key, self::createDeepDirPath ( $dir, $key ) );
		@unlink($fileName);
	}
	
	/**
	 * Utility method for creating a file
	 *
	 * @param String $fileName
	 */
	public static function createFile($fileName) {
		$handle = fopen ( $fileName, "w" );
		if ($handle == NULL) {
			throw new WURFL_WURFLException ( "Can't create file." );
		}
		fclose ( $handle );
	}
	
	/**
	 * Utility method for creating a directory
	 *
	 * @param string $dirName
	 */
	public static function createDir($dirName) {
		if (isset ( $dirName )) {
			if (! is_dir ( $dirName )) {
				mkdir ( $dirName, 0777, true );
			}
		}
	}
	
	/**
	 * Utility method for deleting the content of a directory
	 *
	 * @param String $dirName
	 */
	public static function deleteContent($dirName) {
		self::deleteRecursive ( $dirName );
	}
	
	public static function removeDir($dirName) {
		self::deleteRecursive ( $dirName );
	}
	
	/**
	 * FileManager::createDeepDirPath()
	 *
	 * Creates deeper levels of cache directory structure to improve
	 * filesystem performance and returns directory path
	 *
	 * When filename is like 0435ssd45123323
	 * do dirs like
	 * cachedir/0/4
	 * @param string $dir
	 * @return string directory path
	 */
	private static function createDeepDirPath($dir, $key) {
		try {
			$dir = self::getDeepDirPath ( $dir, $key );
			if (! file_exists ( $dir )) {
				mkdir ( $dir, 0777, true );
			}
		} catch ( Exception $e ) {
			throw new WURFL_WURFLException ( 'Could not create deep cache directory' );
		}
		return $dir;
	}
	
	/**
	 * Composes deeper directory path and returns it
	 * @param Cache directory $dir
	 * @param filename $key
	 */
	private static function getDeepDirPath($dir, $key) {
		$md5key = md5 ( $key );
		$dir = $dir . DIRECTORY_SEPARATOR . substr ( $md5key, 0, 1 ) . DIRECTORY_SEPARATOR . substr ( $md5key, 1, 1 );
		return $dir;
	}
	
	private static function deleteRecursive($dirName) {
		if (! file_exists ( $dirName )) {
			return;
		}
		
		// Simple delete for a file
		if (is_file ( $dirName ) || is_link ( $dirName )) {
			return @unlink ( $dirName );
		}
		$dir = dir ( $dirName );
		while ( false !== $entry = $dir->read () ) {
			if ($entry != '.' && $entry != '..') {
				self::deleteRecursive ( $dirName . DIRECTORY_SEPARATOR . $entry );
			}
		}
		// Clean up
		$dir->close ();
		rmdir ( $dirName );
	}
	
	/**
	 * Checks if the specified file exists
	 *
	 * @param string $fileName full path to the file
	 * @return boolean
	 */
	public static function fileExists($fileName) {
		return file_exists ( $fileName );
	}
	
	private static function getFileName($name, $dir) {
		return $dir . '/' . md5 ( $name );
	}

}

//EOF FileManager.php