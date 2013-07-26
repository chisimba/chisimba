<?php
// Make it a CSS header
header('Content-type: text/css');

// Add entry point run security here

// Get the name of the cache file
$cacheFile = htmlspecialchars($_GET["cachefile"]);
if ($cacheFile !== "" && $cacheFile !==NULL) {
    $cacheFile = $cacheFile . ".css";
} else {
    // Get a cahce filename based on the directory we are in
    $dirComponent = extractDir();
    $hashComponenet = getHash();
    $cacheFile = extractDir() . ".css";
}

$compileCanvasCss = htmlspecialchars($_GET["compile_c_css"]);
if ($compileCanvasCss !== "" && $compileCanvasCss !==NULL) {
    // Compile the canvas CSS as well
    
}

// Define the lifetime of the cached file in seconds
//define("CACHE_LIFE", 604800);
define("CACHE_LIFE", 0.000001);

if (file_exists($cacheFile)) {
    $cacheTime = @filemtime($cacheFile);
    $expires = "Expires: " . gmdate("D, d M Y H:i:s", $cacheTime + CACHE_LIFE) . " GMT";
    header($expires);
    if (!$cacheTime or (time() - $cacheTime >= CACHE_LIFE)){
        // Generate a cache
        generateCache($cacheFile);
        //require_once CACHED_FILE;
        include_once $cacheFile;
    } else {
        // It has not expired, so load it
        require_once "" . $cacheFile;
    }  
} else {
    $expires = "Expires: " . gmdate("D, d M Y H:i:s", time() + CACHE_LIFE) . " GMT";
    header($expires);
    // It doesn't exist so create it & then include it
    generateCache($cacheFile);
    require_once $cacheFile;
}




/**
* Generate the cache file
*
*/
function generateCache($cacheFile='cache.css')
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
                file_put_contents($cacheFile, $css);
            } else {
                // Append after the first one
                file_put_contents($cacheFile, $css, FILE_APPEND);
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

/**
 * 
 * Extract the current home directory for the site, or return
 * __root if the site is root. This is then used to write the cache
 * file as $dirName.css
 * 
 * @return string The directory
 * 
 */
function extractDir()
{
    //get public directory structure eg "/top/second/third" 
    $publicDirectory = dirname($_SERVER['PHP_SELF']); 
    //place each directory into array 
    $directoryAr = explode('/', $publicDirectory); 
    //get highest or top level in array of directory strings 
    $publicBase = max($directoryAr); 
    if ($publicBase == "") {
        $publicBase=getHash();
    }
    return $publicBase; 
}

function getHash()
{
    return md5($_SERVER['HTTP_HOST']);
}
?>
