<?php
    //$objPrototype = $this->newObject('prototype','prototype');
    //$this->appendArrayVar('headerParams', $objPrototype->show());

    $objExtJS = $this->getObject('extjs','ext');
    $objExtJS->show(EXT_JS_WITH_PROTOTYPE);

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
    /*
<script type="text/javascript">
function DoIt()
{
    var val;
    val = window.prompt('Val','temp');
    userNameAvailable(val);
    alert(Object.inspect(val2));
}
</script>
<form name="DoItForm" action="">
<input type="button" value="DoIt" onclick="DoIt()">
</form>
    */
?>