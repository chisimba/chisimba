<?php
    include('HTML/Select/Common/Country.php');

    $c = new HTML_Select_Common_Country();
?>

<html>
<body>
    <?=$c->toHTML('country', 'gb')?>
</body>
</html>
