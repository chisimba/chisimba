<?php
$extbase = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/adapter/ext/ext-base.js','htmlelements').'" type="text/javascript"></script>';
$extalljs = '<script language="JavaScript" src="'.$this->getResourceUri('ext-3.0-rc2/ext-all.js','htmlelements').'" type="text/javascript"></script>';
$extallcss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('ext-3.0-rc2/resources/css/ext-all.css','htmlelements').'"/>';
$maincss = '<link rel="stylesheet" type="text/css" href="'.$this->getResourceUri('css/simpleregistration.css').'"/>';

$this->appendArrayVar('headerParams', $extbase);
$this->appendArrayVar('headerParams', $extalljs);
$this->appendArrayVar('headerParams', $extallcss);
$this->appendArrayVar('headerParams', $maincss);
$savecommenturl= $this->uri(array('action' => 'savecomment'));
$mainjs=
"
var form = new Ext.form.FormPanel({
        baseCls: 'x-plain',
        renderTo:'surface',
        labelWidth: 55,
        bodyStyle:'padding:5px 5px 0',
        standardSubmit: true,
        url:'".str_replace("amp;", "", $saveCommentUrl->href)."',
        defaultType: 'textfield',
        items:[
        new Ext.form.TextArea({
            fieldLabel: 'Your comments',
            name: 'commentField',
            id: 'commentsFieldId',
            width: 600,
            height: 200
          })
          ],
           buttons: [{
                    text:'Save',
                    handler: function(){
                  if (form.url){
                            form.getForm().getEl().dom.action = form.url;
                          }
                        form.getForm().submit();

                  }
                  }
            ]

      });

";
$commentsButton = new button('comment','Save comment');
$commentsButton->setId('addcomments-btn');
$content= '<div id="surface"><h3>What did you think about FOSSAD?</h3>'.$renderSurface;
$content.= "<script type=\"text/javascript\">".$mainjs."</script></div>";


// Create an instance of the css layout class
$cssLayout = & $this->newObject('csslayout', 'htmlelements');// Set columns to 2
$cssLayout->setNumColumns(2);

$rightSideColumn .= $content;
$postLoginMenu  = $this->newObject('postloginmenu','toolbar');
$cssLayout->setLeftColumnContent( $postLoginMenu->show());

// Add Right Column
$cssLayout->setMiddleColumnContent($rightSideColumn);

//Output the content to the page
echo $cssLayout->show();
?>
