<?php
    $objExtJS = $this->getObject('extjs','ext');
    $objExtJS->show();

    $fullUri = $this->uri(NULL);
    $fullUri = explode("?",$fullUri);
    $siteUri = $fullUri[0];

    $this->appendArrayVar('headerParams', '
        <script type="text/javascript">
        var baseuri = "'.$siteUri.'";
        </script>');

    $ext =$this->getJavaScriptFile('Ext.data.country.js', 'useradmin');
    $ext .=$this->getJavaScriptFile('CheckColumn.js', 'useradmin');
    $ext .=$this->getJavaScriptFile('Ext.ux.grid.Search.js', 'useradmin');
    $ext .=$this->getJavaScriptFile('edituser.js', 'useradmin');
    $ext .=$this->getJavaScriptFile('adduser.js', 'useradmin');
    $ext .=$this->getJavaScriptFile('useradmin.js', 'useradmin');	

    $this->appendArrayVar('headerParams', $ext);

    echo '<div id="user-grid"></div>	</p>';
?>
