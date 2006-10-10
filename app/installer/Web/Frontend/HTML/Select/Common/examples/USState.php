<?php
    include('HTML/Select/Common/USState.php');

    $c = new HTML_Select_Common_USState();
?>

<html>
<body>
    <?=$c->toHTML('state', 'colorado')?>
</body>
</html>
