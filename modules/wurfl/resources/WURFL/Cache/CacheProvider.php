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
 * @package    WURFL_Cache
 * @copyright  WURFL-PRO SRL, Rome, Italy
 * @license
 * @version    $id$
 */


/**
 * Cache is the base interface for any type of caching implementation.
 * It provides an API that allows storing and retrieving resources.
 *
 *
 * @category   WURFL
 * @package    WURFL
 * @copyright  WURFL-PRO SRL, Rome, Italy
 * @license
 * @version    $id$
 */
interface WURFL_Cache_CacheProvider {
	
	const EXPIRATION = "expiration";
	
	const ONE_HOUR = 3600;
	const ONE_DAY = 86400;
	const ONE_WEEK = 604800;
	const ONE_MONTH = 2592000;
	const ONE_YEAR = 31556926;
	const NEVER = 0;
	
	
	/**
	 * Put the the computed data into the cache so that it can be
	 * retrieved later.
	 * @param string key for accesing the data
	 * @param mixed the actual data been stored
	 */
	function put($key, $value);

	/**
	 * Get the previosly saved data.
	 * @param string key for accesing the data
	 * @return mixed the actual data been stored
	 */
	function get($key);
	
	/**
	 * Invalidates the Cache
	 *
	 */
	function clear();
}
?>