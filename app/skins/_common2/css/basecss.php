<?php
// Make it a CSS header
header('Content-type: text/css');



// Define the filename of the cached file
define("CACHED_FILE", "cached.css");
// Define the lifetime of the cached file in seconds
//define("CACHE_LIFE", 604800);
define("CACHE_LIFE", 0.000001);

if (file_exists(CACHED_FILE)) {
    $cacheTime = @filemtime(CACHED_FILE);
    $expires = "Expires: " . gmdate("D, d M Y H:i:s", $cacheTime + CACHE_LIFE) . " GMT";
    header($expires);
    if (!$cacheTime or (time() - $cacheTime >= CACHE_LIFE)){
        // Generate a cache
        generateCache();
        //require_once CACHED_FILE;
        include_once CACHED_FILE;
    } else {
        // It has not expired, so load it
        require_once "" . CACHED_FILE;
    }  
} else {
    $expires = "Expires: " . gmdate("D, d M Y H:i:s", time() + CACHE_LIFE) . " GMT";
    header($expires);
    // It doesn't exist so create it & then include it
    generateCache();
    require_once CACHED_FILE;
}




/**
* Generate the cache file
*
*/
function generateCache()
{
    $cssArray = array(
        "layout.css",
        "common2.css",
        "htmlelements.css",
        "creativecommons.css",
        "forum.css",
        "calendar.css",
        "cms.css",
        "stepmenu.css",
        "switchmenu.css",
        "colorboxes.css",
        "manageblocks.css",
        "facebox.css",
        "modernbrickmenu.css",
        "jquerytags.css",
        "overlappingtabs.css",
        "login.css",
        "navigationmenu.css",
        "modulespecific.css",
        "cssdropdownmenu.css",
        "sexybuttons.css",
        "chisimbacanvas.css",
	"filemanager.css",
    );
    //load up all of the CSS files into an array
    $cssFiles = glob("*.css");
    $counter=1;
    foreach ($cssArray as $cssFile) {
        if (file_exists($cssFile)) {
            $css = file_get_contents($cssFile);
            $css = optimize($css);
            if ($counter == 1) {
                // Create it or overwrite it the first time around
                file_put_contents(CACHED_FILE, $css);
            } else {
                // Append after the first one
                file_put_contents(CACHED_FILE, $css, FILE_APPEND);
            }
        }
        $counter++;
    }
}

/**
 *
 * Get rid os spaces, newlines, tabs, comments, etc
 *
 */
function optimize($css)
{
  // remove comments
  $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
  // remove tabs, spaces, newlines, etc.
  $css = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $css);
  return $css;
}
?>
