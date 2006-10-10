<?php
    include('HTML/Select/Common/FRDepartements.php');

    $c = new HTML_Select_Common_FRDepartements();
?>

<html>
<body>
    <?=$c->toHTML('state', 'utah')?>
</body>
</html>
