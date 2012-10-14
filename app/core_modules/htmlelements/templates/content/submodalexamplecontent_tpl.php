<?php

$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('suppressFooter', TRUE);

?>
    <p>
    Content for the modal window.
    </p>
    <p>
    Notice you can't click anything on the previous page because of the semi-transparent div below..pretty slick huh?
    </p>
    <p>
    Try resizing your window and check out how the modal re-centers itself.
    </p>
    <button onclick="window.parent.hidePopWin()">close</button>