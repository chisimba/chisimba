<table border="0" width="737" cellspacing="0" cellpadding="0">
	<tr>
		<td width="1%" valign="top" align="right">
			{MONTH_SMALL|-1}
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
					<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
					<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
				</tr>
			</table>
		</td>
		<td width="98%" valign="top" align="center">
			<table border="0" width="330" cellspacing="0" cellpadding="0">
				<tr>
					<td width="160" valign="top">
						<table width="170" border="0" cellpadding="3" cellspacing="0" class="calborder">
							<tr>
								<td align="center" class="sideback"><div style="height:16px;"><b>{L_JUMP}</b></div></td>
							</tr>
							<tr>
								<td>
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
										<form style="margin-bottom:0;" action="day.php" method="get"/>
											<input type="hidden" name="cpath" value="{CPATH}"/>
											<input type="hidden" name="cal" value="{URL_CAL}"/>
											<input type="text" style="width:160px; font-size:10px" name="jumpto_day"/>
											<input type="submit" value="Go"/>
										</form>
										<!-- switch show_goto off -->
										<hr />
										<div class = 'G10BOLD'>{L_PICK_MULTIPLE}:</div>
										<form style="margin-bottom:0;" action="{CURRENT_VIEW}.php" method="get">
											<input type="hidden" name="getdate" value="{GETDATE}"/>
											<input type="hidden" name="cpath" value="{CPATH}"/>
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
					</td>
					<td>
						<img src="images/spacer.gif" width="20" height="1" alt=" " />
					</td>
					<td width="160" valign="top">
						<table width="170" border="0" cellpadding="3" cellspacing="0" class="calborder">
							<tr>
								<td align="center" class="sideback"><div style="height:16px;"><b>{SIDEBAR_DATE}</b></div></td>
							</tr>
							<tr>
								<td>
									<div style="padding-left: 5px;">
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
										<a class="psf" href="{SCRIPT_NAME}?{QUERYS}">Logout {USERNAME}</a>
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
					</td>
				</tr>
			</table>
		</td>
		<td width="1%" valign="top" align="left">
			{MONTH_SMALL|+1}
			<table width="170" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
					<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
					<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
				</tr>
			</table>
		</td>
	</tr>
</table>
