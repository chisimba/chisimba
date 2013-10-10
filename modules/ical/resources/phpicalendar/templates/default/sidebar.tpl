<!-- switch show_user_login on -->
<form style="margin-bottom:0;" action="{CURRENT_VIEW}.php?{LOGIN_QUERYS}" method="post">
<input type="hidden" name="action" value="login" />
<table width="170" border="0" cellpadding="0" cellspacing="0" class="calborder">
	<tr>
		<td colspan="2" align="center" class="sideback"><div style="height: 17px; margin-top: 3px;" class="G10BOLD">{L_LOGIN}</div></td>
	</tr>
	<!-- switch invalid_login on -->
	<tr>
		<td colspan="2" bgcolor="#FFFFFF" align="left">
			<div style="padding-left: 5px; padding-top: 5px; padding-right: 5px;">
				<font color="red">{L_INVALID_LOGIN}</font>
			</div>
		</td>
	</tr>
	<!-- switch invalid_login off -->
	<tr>
		<td bgcolor="#FFFFFF" align="left" valign="middle"><div style="padding-left: 5px; padding-top: 5px;">{L_USERNAME}:</div></td>
		<td bgcolor="#FFFFFF" align="right" valign="middle"><div style="padding-right: 5px; padding-top: 5px;"><input type="text" name="username" size="10" /></div></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF" align="left" valign="middle"><div style="padding-left: 5px; padding-bottom: 5px;">{L_PASSWORD}:</div></td>
		<td bgcolor="#FFFFFF" align="right" valign="middle"><div style="padding-right: 5px; padding-bottom: 5px;"><input type="password" name="password" size="10" /></div></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF" align="center" valign="middle" colspan="2"><div style=padding-left: 5px; padding-bottom: 5px;"><input type="submit" value="{L_LOGIN}" /></div></td>
	</tr>
</table>
</form>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
	</tr>
</table>
<img src="images/spacer.gif" width="1" height="10" alt=" " /><br />
<!-- switch show_user_login off -->
<table width="170" border="0" cellpadding="0" cellspacing="0" class="calborder">
	<tr>
		<td align="left" valign="top" width="24" class="sideback"><a class="psf" href="day.php?cal={CAL}&amp;getdate={PREV_DAY}"><img src="templates/{TEMPLATE}/images/left_arrows.gif" alt="{L_PREV}" width="16" height="20" border="0" align="left" /></a></td>
		<td align="center" width="112" class="sideback"><font class="G10BOLD">{SIDEBAR_DATE}</font></td>
		<td align="right" valign="top" width="24" class="sideback"><a class="psf" href="day.php?cal={CAL}&amp;getdate={NEXT_DAY}"><img src="templates/{TEMPLATE}/images/right_arrows.gif" alt="{L_NEXT}" width="16" height="20" border="0" align="right" /></a></td>
	</tr>
	<tr>
		<td colspan="3" bgcolor="#FFFFFF" align="left">
			<div style="padding: 5px;">
				<b>{L_LEGEND}:</b><br />
				{LEGEND}
				<a class="psf" href="print.php?cal={CAL}&amp;getdate={GETDATE}&amp;printview={CURRENT_VIEW}">{L_GOPRINT}</a><br />
				<!-- switch allow_preferences on -->
				<a class="psf" href="preferences.php?cal={CAL}&amp;getdate={GETDATE}">{L_PREFERENCES}</a><br />
				<!-- switch allow_preferences off -->
				<!-- switch display_download on -->
				<a class="psf" href="{SUBSCRIBE_PATH}">{L_SUBSCRIBE}</a>&nbsp;|&nbsp;<a class="psf" href="{DOWNLOAD_FILENAME}">{L_DOWNLOAD}</a><br />
				<!-- switch display_download off -->
				<!-- switch is_logged_in on -->
				<a class="psf" href="{CURRENT_VIEW}.php?{LOGOUT_QUERYS}">{L_LOGOUT} {USERNAME}</a>
				<!-- switch is_logged_in off -->
			</div>
		</td>
	</tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
	</tr>
</table>
<img src="images/spacer.gif" width="1" height="10" alt=" " /><br />

<table width="170" border="0" cellpadding="0" cellspacing="0" class="calborder">
	<tr>
		<td align="center" class="sideback"><div style="height: 17px; margin-top: 3px;" class="G10BOLD">{L_JUMP}</div></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF" align="left">
			<div style="padding: 5px;">
				<form style="margin-bottom:0;" action="{CURRENT_VIEW}.php" method="get">
					<select name="action" class="query_style" onchange="window.location=(this.options[this.selectedIndex].value);">{LIST_JUMPS}</select><br />
					<select name="action" class="query_style" onchange="window.location=(this.options[this.selectedIndex].value);">{LIST_ICALS}</select><br />
					<select name="action" class="query_style" onchange="window.location=(this.options[this.selectedIndex].value);">{LIST_YEARS}</select><br />
					<select name="action" class="query_style" onchange="window.location=(this.options[this.selectedIndex].value);">{LIST_MONTHS}</select><br />
					<select name="action" class="query_style" onchange="window.location=(this.options[this.selectedIndex].value);">{LIST_WEEKS}</select><br />
					<input type="hidden" name="cpath" value="{CPATH}"/>

				</form>
				<!-- switch show_search on -->
				{SEARCH_BOX}
				<!-- switch show_search off -->
				<!-- switch show_goto on -->
				<form style="margin-bottom:0;" action="day.php" method="get">
					<input type="hidden" name="cal" value="{URL_CAL}">
					<input type="text" style="width:160px; font-size:10px" name="jumpto_day">
					<input type="submit" value="Go"/>
				</form>
				<!-- switch show_goto off -->
				<hr />
				<div class = 'G10BOLD'>{L_PICK_MULTIPLE}:</div>
				<form style="margin-bottom:0;" action="{CURRENT_VIEW}.php" method="get">
					<input type="hidden" name="cpath" value="{CPATH}"/>
					<input type="hidden" name="getdate" value="{GETDATE}"/>
					<select name="cal[]" class="query_style" size="5" multiple="multiple">{LIST_ICALS_PICK}</select><br />
					<input type="submit" value="Go"/>
				</form>
			</div>
		</td>
	</tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
	</tr>
</table>
<img src="images/spacer.gif" width="1" height="10" alt=" " /><br />

<!-- switch tomorrows_events on -->

<table width="170" border="0" cellpadding="0" cellspacing="0" class="calborder">
	<tr>
		<td align="center" class="sideback"><div style="height: 17px; margin-top: 3px;" class="G10BOLD">{L_TOMORROWS}</div></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF" align="left">
			<div style="padding: 5px;">
				<!-- switch t_allday on -->
				{T_ALLDAY}<br />
				<!-- switch t_allday off -->
				<!-- switch t_event on -->
				&bull; {T_EVENT}<br />
				<!-- switch t_event off -->
			</div>
		</td>
	</tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
	</tr>
</table>
<img src="images/spacer.gif" width="1" height="10" alt=" " /><br />

<!-- switch tomorrows_events off -->

<!-- switch vtodo on -->

<table width="170" border="0" cellpadding="0" cellspacing="0" class="calborder">
	<tr>
		<td align="center" width="98%" class="sideback"><div style="height: 17px; margin-top: 3px;" class="G10BOLD">{L_TODO}</div></td>
	</tr>
	<tr>
		<td bgcolor="#FFFFFF" align="left">
			<div style="padding: 5px;">
				<table cellpadding="0" cellspacing="0" border="0">
					<!-- switch show_completed on -->
					<tr>
						<td><img src="images/completed.gif" alt=" " width="13" height="11" border="0" align="middle" /></td>
						<td><img src="images/spacer.gif" width="2" height="1" border="0" alt="" /></td>
						<td><s><a class="psf" href="javascript:openTodoInfo('{VTODO_ARRAY}')"><font class="G10B"> {VTODO_TEXT}</font></a></s></td>
					</tr>
					<!-- switch show_completed off -->
					<!-- switch show_important on -->
					<tr>
						<td><img src="images/important.gif" alt=" " width="13" height="11" border="0" align="middle" /></td>
						<td><img src="images/spacer.gif" width="2" height="1" border="0" alt="" /></td>
						<td><a class="psf" href="javascript:openTodoInfo('{VTODO_ARRAY}')"><font class="G10B"> {VTODO_TEXT}</font></a></td>
					</tr>
					<!-- switch show_important off -->
					<!-- switch show_normal on -->
					<tr>
						<td><img src="images/not_completed.gif" alt=" " width="13" height="11" border="0" align="middle" /></td>
						<td><img src="images/spacer.gif" width="2" height="1" border="0" alt="" /></td>
						<td><a class="psf" href="javascript:openTodoInfo('{VTODO_ARRAY}')"><font class="G10B"> {VTODO_TEXT}</font></a></td>
					</tr>
					<!-- switch show_normal off -->
				</table>
			</div>
		</td>
	</tr>			
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
	</tr>
</table>
<img src="images/spacer.gif" width="1" height="10" alt=" " /><br />


<!-- switch vtodo off -->

{MONTH_SMALL|-1}
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
	</tr>
</table>
<img src="images/spacer.gif" width="1" height="10" alt=" " /><br />

{MONTH_SMALL|+0}
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
	</tr>
</table>
<img src="images/spacer.gif" width="1" height="10" alt=" " /><br />

{MONTH_SMALL|+1}
<table width="100%" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
	</tr>
</table>
