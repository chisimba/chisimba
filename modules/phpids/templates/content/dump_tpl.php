<?php

/**
 *
 *
 * @version $Id$
 * @copyright 2009
 */

if ($isOK) {
    echo 'OK!';
}
else {
    echo 'Failed!';
    echo '<div style="background-color: black; padding: 5px;">';
    echo '<div style="background-color: yellow; padding: 5px; color: red;">';
    echo $result;
    echo '</div>';
    echo '</div>';
}

//&test=%22><script>eval(window.name)</script>
?>