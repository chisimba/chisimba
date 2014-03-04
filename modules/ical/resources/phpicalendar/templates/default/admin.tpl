{HEADER}
<center>
<table border="0" width="600" cellspacing="0" cellpadding="0" class="calborder">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr valign="top">
					<td align="left" width="400" class="title"><h1>{L_ADMIN_HEADER}</h1><span class="V9G">{L_ADMIN_SUBHEAD}</span></td>
					<td align="right" width="120" class="navback">	
						<div style="padding-top: 3px;">
						<table width="120" border="0" cellpadding="0" cellspacing="0">
							<tr valign="top">
								<td><a class="psf" href="../day.php?cal={CAL}&amp;getdate={GETDATE}"><img src="../templates/{TEMPLATE}/images/day_on.gif" alt="{L_DAY}" border="0" /></a></td>
								<td><a class="psf" href="../week.php?cal={CAL}&amp;getdate={GETDATE}"><img src="../templates/{TEMPLATE}/images/week_on.gif" alt="{L_WEEK}" border="0" /></a></td>
								<td><a class="psf" href="../month.php?cal={CAL}&amp;getdate={GETDATE}"><img src="../templates/{TEMPLATE}/images/month_on.gif" alt="{L_MONTH}" border="0" /></a></td>
								<td><a class="psf" href="../year.php?cal={CAL}&amp;getdate={GETDATE}"><img src="../templates/{TEMPLATE}/images/year_on.gif" alt="{L_YEAR}" border="0" /></a></td>
							</tr>
						</table>
						</div>
					</td>
				</tr>  			
			</table>
		</td>
	</tr>
	<tr>
		<td class="dayborder"><img src="images/spacer.gif" width="1" height="5" alt=" " /></td>
	</tr>
	<tr>
		<td align="right">
			<!-- switch logged_in2 on -->
			<a href="index.php?action=logout">{L_LOGOUT}</a>&nbsp;
			<!-- switch logged_in2 off -->
		</td>
	</tr>
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="G10B">
				<tr>
					<td width="2%"></td>
					<td width="98%" valign="top" align="left">
						<!-- switch login_error on -->
						<font color="red">{L_INVALID_LOGIN}</font><br /><br />
						<!-- switch login_error off -->
	
						<!-- switch display_login on -->
						<form action="index.php?action=login" method="post">
							<table cellspacing="0" cellpadding="0">
								<tr>
									<td nowrap>{L_USERNAME}: </td>
									<td align="left"><input type="text" name="username"></td>
								</tr>
								<tr>
									<td>{L_PASSWORD}: </td>
									<td align="left"><input type="password" name="password"></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td align="left"><input type="submit" value="{L_LOGIN}"></td>
								</tr>
								<!--
								<tr>
									<td align="center" colspan="3">{LOGIN_ERROR}&nbsp;</td>
								</tr>
								-->
							</table>
						</form>
						<!-- switch display_login off -->
						
						
						<!-- switch logged_in on -->
						<b>{L_ADDUPDATE_CAL}</b><br />
						{L_ADDUPDATE_DESC}<br /><br />
						<form action="index.php" method="post" enctype="multipart/form-data">
							<input type="hidden" name="action" value="addupdate">
							<table width="100%" border="0" cellspacing="0" cellpadding="2" class="G10B">
								<tr>
									<td nowrap>{L_CAL_FILE} 1: </td>
									<td><input type="file" name="calfile[1]"></td>
								</tr>
								<tr>
									<td nowrap>{L_CAL_FILE} 2: </td>
									<td><input type="file" name="calfile[2]"></td>
								</tr>
								<tr>
									<td nowrap>{L_CAL_FILE} 3: </td>
									<td><input type="file" name="calfile[3]"></td>
								</tr>
								<tr>
									<td nowrap>{L_CAL_FILE} 4: </td>
									<td><input type="file" name="calfile[4]"></td>
								</tr>
								<tr>
									<td nowrap>{L_CAL_FILE} 5: </td>
									<td><input type="file" name="calfile[5]"></td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><input type="submit" value="{L_SUBMIT}"></td>
								</tr>
								<tr>
									<td align="center" colspan="2">{ADDUPDATE_MSG} &nbsp;</td>
								</tr>
							</table>
						</form>
						
						<b>{L_DELETE_CAL}</b>
						<form action="index.php" method="post">
							<table width="100%" border="0" cellspacing="0" cellpadding="2" class="G10B">
								{DELETE_TABLE}
							</table>
							<input type="hidden" name="action" value="delete">
							<p><input type="submit" value="{L_DELETE}"></p>
							<p>{DELETE_MSG} &nbsp;</p>
						</form>
						
						<!-- switch logged_in off -->
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<table width="600" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
	</tr>
</table>
{FOOTER}


