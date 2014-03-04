<?php

class block_livechat extends object {

    function init() {
        $this->objLanguage = $this->getObject('language', 'language');
        $this->objAltConfig = $this->getObject('altconfig', 'config');
        $this->objSysConfig = $this->getObject('dbsysconfig', 'sysconfig');

        $this->loggedInUsers = $this->getObject('loggedinusers', 'security');
        $this->title = $this->objLanguage->languageText('mod_livechat_title', 'livechat', 'Live Chat');
        $this->objContext = $this->getObject('dbcontext', 'context');
        $this->contextCode = $this->objContext->getContextCode();
        $this->loadClass('checkbox', 'htmlelements');
        $this->loadClass('label', 'htmlelements');
        $this->loadClass('form', 'htmlelements');
        $this->loadClass('link', 'htmlelements');
    }

    function show() {
        $link = new link($this->uri(array("action" => "sendinvite")));
        $saveUrl = $link->href;
        $sendInviteJS = 'jQuery(document).ready(function() {
                       jQuery("#invitetochatbutton").click(function() {
                          var selectedUsers="";
                          jQuery("input:checked").each(function(){
                           selectedUsers+=","+this.value;
                          });
                            var data=selectedUsers;
                            var url = "' . str_replace("amp;", "", $saveUrl) . '&users="+selectedUsers;
                           jQuery.ajax({
                                type: "POST",
                                url: url,
                                data: data,
                                success: function(msg) {
                                   alert(msg);
                                }
                           });
                    });

               
     });';

        $jq = "<script type='text/javascript'>" . $sendInviteJS . "</script>";
        $objFeatureBox = $this->newObject('featurebox', 'navigation');
        $loggedInUsers = $this->loggedInUsers->getListOnlineUsersInCurrentContext($this->contextCode);
        $content = '<div id="results"></div>' . $jq . '<b>' . count($loggedInUsers) . ' users online</b><br/>';
        $inviteButton = new button('invitetochat', $this->objLanguage->languageText('mod_livechat_invitetochat', 'livechat', 'Invite to Chat'));
        $inviteButton->setId("invitetochatbutton");
        //$inviteButton->setToSubmit();
        $count = 1;
        foreach ($loggedInUsers as $user) {

            $checkbox = new checkbox('users[]', $user['userid']);
            $checkbox->value = $user['userid'];
            $label = new label(' ' . $user['firstname'] . '&nbsp;' . $user['surname'], 'user_' . $user['userid']);
            $content .= ' ' . $checkbox->show() . $label->show() . '<br />';

            $count++;
        }

        $form = new form('whoisonline', $this->uri(array('action' => 'sendinvite')));
        $form->addToForm($content);
       // $form->addToForm($inviteButton->show());
        $block = "shortcuts";
        $hidden = 'default';
        $showToggle = false;
        $showTitle = true;
        $cssClass = "featurebox";

        return $objFeatureBox->show(
                $this->objLanguage->languageText('mod_livechat_whoisonline', 'livechat', 'Who is online'),
                $form->show(),
                $block,
                $hidden,
                $showToggle,
                $showTitle,
                $cssClass, '');
    }

}

?>
