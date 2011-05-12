<p>
Thank you for installing Chisimba. <a onclick="sq_redirect(this.href); return false;" href="<?php echo $login_url ?>"> Please log in to the system now to complete your installation and begin using Chisimba</a><br />(remember, the initial username is "<strong>admin</strong>", with the password "<strong>a</strong>").<br />At this time you may want to join a users mailing list in order to gain the full benefit of the Chisimba community. You are able to subscribe to the mailing lists at <a href="http://groups.google.com/group/chisimba-dev">http://groups.google.com/group/chisimba-dev</a>
</p>
<p>
<!--<a href="#template_bottom">here</a>--><!--javascript: -->Perform alternate AJAX final setup and log in to system <input name="finish" type="image" onmouseover="over('finish', this)" onmouseout="off('finish', this)" src="<?php echo $extra ?>/finish_off.png" onclick="ajax_install('<?php echo $ajaxregister_url ?>', '<?php echo $ajaxregister_url_params ?>', '<?php echo $ajaxlogin_url ?>'); return false;">
</p>
<!--<a id="template_bottom">-->
<div
    id="status"
    style="
        height: 200px;
        overflow: auto;
    "
>
</div>
<div id="progress_bar_border" style="
	border: 1px solid fuchsia;
	background-color: white;
	height: 10px;
">
    <div id="progress_bar" style="
	background-color: gray;
	width: 0px;
	height: 10px;
    ">
    </div>
</div>
