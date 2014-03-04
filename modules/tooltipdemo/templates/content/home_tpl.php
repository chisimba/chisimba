
<?php
$extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
$extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','htmlelements').'" type="text/javascript"></script>';
$extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','htmlelements').'"/>';
$maincss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/tooltip.css').'"/>';
        $this->appendArrayVar('headerParams', $extbase);
        $this->appendArrayVar('headerParams', $extalljs);
        $this->appendArrayVar('headerParams', $extallcss);
        $this->appendArrayVar('headerParams', $maincss);
$mainjs = "
/*!
 * Ext JS Library 3.0+
 * Copyright(c) 2006-2009 Ext JS, LLC
 * licensing@extjs.com
 * http://www.extjs.com/license
 */
Ext.onReady(function(){
    new Ext.ToolTip({
        target: 'tip1',
        html: 'A very simple tooltip'
    });

    new Ext.ToolTip({
        target: 'ajax-tip',
        width: 200,
        autoLoad: {url: 'ajax-tip.html'},
        dismissDelay: 15000 // auto hide after 15 seconds
    });

    new Ext.ToolTip({
        target: 'tip2',
        html: 'Click the X to close me',
        title: 'My Tip Title',
        autoHide: false,
        closable: true,
        draggable:true
    });

    new Ext.ToolTip({
        target: 'track-tip',
        title: 'Mouse Track',
        width:200,
        html: 'This tip will follow the mouse while it is over the element',
        trackMouse:true
    });

    new Ext.ToolTip({
        title: '<a href=\"#\">Rich Content Tooltip</a>',
        id: 'content-anchor-tip',
        target: 'leftCallout',
        anchor: 'left',
        html: null,
        width: 415,
        autoHide: false,
        closable: true,
        contentEl: 'content-tip', // load content from the page
        listeners: {
            'render': function(){
                this.header.on('click', function(e){
                    e.stopEvent();
                    Ext.Msg.alert('Link', 'Link to something interesting.');
                    Ext.getCmp('content-anchor-tip').hide();
                }, this, {delegate:'a'});
            }
        }
    });

    new Ext.ToolTip({
        target: 'bottomCallout',
        anchor: 'top',
        anchorOffset: 85, // center the anchor on the tooltip
        html: 'This tip\'s anchor is centered'
    });

    new Ext.ToolTip({
        target: 'trackCallout',
        anchor: 'right',
        trackMouse: true,
        html: 'Tracking while you move the mouse'
    });


    Ext.QuickTips.init();

});

";

 $style="
<style type=\"text/css\">
        .tip-target {
            width: 100px;
            text-align:center;
            padding: 5px 0;
            border:1px dotted #99bbe8;
            background:#dfe8f6;
            color: #15428b;
            cursor:default;
            margin:10px;
            font:bold 11px tahoma,arial,sans-serif;
            float:left;
        }
    </style>

";
 echo $style;
  $content=' <h3>Easiest Tip</h3>
    <div id="tip1" class="tip-target">Basic ToolTip</div>
    <div id="tip2" class="tip-target">autoHide disabled</div>
    <div id="ajax-tip" class="tip-target">Ajax ToolTip</div>
    <div id="track-tip" class="tip-target">Mouse Track</div>
    <div id="tip4" class="tip-target" ext:qtip="My QuickTip">QuickTip</div>


    <div class="x-clear"></div>
    <h3>Callout Tip</h3>
    <div id="leftCallout" class="tip-target" style="width: 150px;">Anchor right, rich content</div>
    <div id="bottomCallout" class="tip-target" style="width: 200px;">Anchor below</div>
    <div id="trackCallout" class="tip-target" style="width: 150px;">Anchor with tracking</div>

    <div style="display:none;">

        <div id="content-tip">
            <ul>
                <li>5 bedrooms</li>
                <li>2 bathrooms</li>
                <li>Large backyard</li>
                <li>Close to transport</li>
            </ul>

            <div class="x-clear"></div>
            <img src="images/house.jpg" alt="Website Thumbnail" />
        </div>
    </div>';

$content.= "<script type=\"text/javascript\">".$mainjs."</script>";
echo $content;
?>