<?php
if (class_exists('groupops', false)) {
    $content = $this->getEportfolioUsers();
} else {
    $content = $this->getEportfolioUsersOld();
}
echo $content;
?>
