<?php
/*
* Template for the chat display.
* @package pbl
*/

/*
* Template for the chat display.
*/

// Suppress Page Variables
$this->setVar('pageSuppressContainer', TRUE);
$this->setVar('pageSuppressBanner', TRUE);
$this->setVar('pageSuppressToolbar', TRUE);
$this->setVar('suppressFooter', TRUE);
$this->setVar('pageSuppressIM', TRUE);

$bodyParams='class="container" ';
$this->setVarByRef('bodyParams',$bodyParams);

    ob_start();
    $path = $this->uri(array('action'=>'showchat'));
    // Replace the &amp; to & in the url
    $path = preg_replace('/&amp;/', '&', $path);

    // set chat to refresh every 30 seconds
?>
    <script language="JavaScript">
        <!--
        function RefreshChat()
        {
                window.location.href='<? echo $path; ?>';
        <? echo $script; ?>

        }
        // -->
    </script>
<body onLoad="window.setTimeout('RefreshChat();',30000);">
<?php
    // get chat and output to screen
    $chatText = $this->dbchat->broadCast();
    
    $objLayer = $this->newObject('layer', 'htmlelements');
    $objLayer->str = $chatText;
    $objLayer->align = 'left';
    echo $objLayer->show();

    // set up chat area
?>
    <script language="javascript">
    <!--
        nscroll = parent.window.frames.chatarea.document.body.offsetHeight;
        //parent.window.frames.chatarea.scrollIntoView(0);
        parent.window.frames.chatarea.window.scrollBy(0,nscroll);
    // -->'
    </script>
</body>
<?php
    ob_end_flush();
?>