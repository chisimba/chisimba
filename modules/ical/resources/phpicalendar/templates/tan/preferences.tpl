{HEADER}
<center>
<table border="0" width="520" cellspacing="0" cellpadding="0" class="calborder">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr valign="top">
					<td align="left" width="400" class="title"><h1>{L_PREFERENCES}</h1><span class="V9G">{L_PREFS_SUBHEAD}</span></td>
					<td valign="top" align="right" width="120" class="navback">	
						<div style="padding-top: 3px;">
						<table width="120" border="0" cellpadding="0" cellspacing="0">
							<tr valign="top">
								<td><a class="psf" href="day.php?cal={CAL}&amp;getdate={GETDATE}"><img src="templates/{TEMPLATE}/images/day_on.gif" alt="{L_DAY}" border="0" /></a></td>
								<td><a class="psf" href="week.php?cal={CAL}&amp;getdate={GETDATE}"><img src="templates/{TEMPLATE}/images/week_on.gif" alt="{L_WEEK}" border="0" /></a></td>
								<td><a class="psf" href="month.php?cal={CAL}&amp;getdate={GETDATE}"><img src="templates/{TEMPLATE}/images/month_on.gif" alt="{L_MONTH}" border="0" /></a></td>
								<td><a class="psf" href="year.php?cal={CAL}&amp;getdate={GETDATE}"><img src="templates/{TEMPLATE}/images/year_on.gif" alt="{L_YEAR}" border="0" /></a></td>
							</tr>
						</table>
						</div>
					</td>
				</tr>  			
			</table>
		</td>
	</tr>
	<tr>
		<td class="dayborder"><img src="images/spacer.gif" width="1" height="1" alt=" " /></td>
	</tr>
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="5">	
				<!-- switch message on -->
				<tr>
					<td colspan="2" align="center"><font class="G10BOLD">{MESSAGE}</font></td>
				</tr>
				<!-- switch message off -->
				<tr>
					<td valign="top" align="left">
					<form action="preferences.php?action=setcookie" method="post">
					<table border="0" width="100%" cellspacing="2" cellpadding="2" align="center">
						<tr align="left" valign="top">
							<td width="80%" nowrap="nowrap">{L_SELECT_LANG}:</td>
							<td width="10%"><img src="images/spacer.gif" alt=" " width="20" height="1" border="0" /></td>
							<td width="10%"><select name="cookie_language" class="query_style">{LANGUAGE_SELECT}</select></td>
						</tr>
						<tr align="left" valign="top">
							<td nowrap="nowrap">{L_SELECT_CAL}:</td>
							<td><img src="images/spacer.gif" alt=" " width="20" height="1" border="0" /></td>
							<td><select name="cookie_calendar" class="query_style">{CALENDAR_SELECT}</select>
							<input type="hidden" name="cpath" value="{CPATH}"/></td>
						</tr>
						<tr align="left" valign="top">
							<td nowrap="nowrap">{L_SELECT_VIEW}:</td>
							<td><img src="images/spacer.gif" alt=" " width="20" height="1" border="0" /></td>
							<td><select name="cookie_view" class="query_style">{VIEW_SELECT}</select></td>
						</tr>
						<tr align="left" valign="top">
							<td nowrap="nowrap">{L_SELECT_TIME}:</td>
							<td><img src="images/spacer.gif" alt=" " width="20" height="1" border="0" /></td>
							<td><select name="cookie_time" class="query_style">{TIME_SELECT}</select></td>
						</tr>
						<tr align="left" valign="top">
							<td nowrap="nowrap">{L_SELECT_DAY}:</td>
							<td><img src="images/spacer.gif" alt=" " width="20" height="1" border="0" /></td>
							<td><select name="cookie_startday" class="query_style">{STARTDAY_SELECT}</select></td>
						</tr>
						<tr align="left" valign="top">
							<td nowrap="nowrap">{L_SELECT_STYLE}:</td>
							<td><img src="images/spacer.gif" alt=" " width="20" height="1" border="0" /></td>
							<td><select name="cookie_style" class="query_style">{STYLE_SELECT}</select></td>
						</tr>
						<!-- switch cookie_already_set on -->
						<tr align="left" valign="top">
							<td nowrap="nowrap">{L_UNSET_PREFS}:</td>
							<td><img src="images/spacer.gif" alt=" " width="20" height="1" border="0" /></td>
							<td><input type="checkbox" name="unset" value="true" /></td>
						</tr>
						<!-- switch cookie_already_set off -->
						<!-- switch cookie_not_set on -->
						<tr align="left" valign="top">
							<td nowrap="nowrap">&nbsp;</td>
							<td><img src="images/spacer.gif" alt=" " width="20" height="1" border="0" /></td>
							<td><input type="submit" name="set" value="{L_SET_PREFS}" /></td>
						</tr>
						<!-- switch cookie_not_set off -->
					</table>
					</form>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table width="520" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
	</tr>
</table>

</center>
{FOOTER}
