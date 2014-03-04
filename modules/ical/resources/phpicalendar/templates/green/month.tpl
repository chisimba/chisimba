{HEADER}
<center>
	<table width="735" border="0" cellspacing="0" cellpadding="0" class="calborder">
		<tr>
			<td align="center" valign="middle">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr valign="top">
						<td align="left" width="615" class="title"><h1>{DISPLAY_DATE}</h1><span class="V9G">{CALENDAR_NAME} {L_CALENDAR}</span></td>
						<td align="right" width="120" class="navback">	
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
	</table>
	{MONTH_LARGE|+0}
	<table width="735" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
			<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
			<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		</tr>
	</table>
	<br />
	{CALENDAR_NAV}
	<!-- switch showbottom on -->
	<br />
	<table width="735" border="0" cellspacing="0" cellpadding="0" class="calborder">
		<tr>
			<td align="center" valign="middle" bgcolor="white">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td align="right" width="40%" class="navback"><a class="psf" href="month.php?cal={CAL}&amp;getdate={PREV_MONTH}"><img src="templates/{TEMPLATE}/images/left_day.gif" alt="{L_PREV}" border="0" align="right" /></a></td>
						<td align="center" width="20%" class="title" nowrap="nowrap" valign="middle"><h1>{L_THIS_MONTHS}</h1></td>
						<td align="left" width="40%" class="navback"><a class="psf" href="month.php?cal={CAL}&amp;getdate={NEXT_MONTH}"><img src="templates/{TEMPLATE}/images/right_day.gif" alt="{L_NEXT}" border="0" align="left" /></a></td>
					</tr>
				</table>
			</td>
		</tr>	
		<tr>
			<td>
				<table width="100%" cellspacing="1" cellpadding="4" border="0">
					<!-- loop showbottomevents_odd on -->
					<tr align="left" valign="top">
						<td width="170" nowrap="nowrap">
							<a class="psf" href="day.php?cal={CAL}&amp;getdate={DAYLINK}">{START_DATE}</a><br />
							<span class="V9G">{START_TIME}</span>
						</td>
						<td>
							{EVENT_TEXT}<br /><span class="V9G">{CALNAME}</span>
						</td>
					</tr>
					<!-- loop showbottomevents_odd off -->
					<!-- loop showbottomevents_even on -->
					<tr align="left" valign="top">
						<td width="170" nowrap="nowrap" bgcolor="#EEEEEE">
							<a class="psf" href="day.php?cal={CAL}&amp;getdate={DAYLINK}">{START_DATE}</a><br />
							<span class="V9G">{START_TIME}</span>
						</td>
						<td bgcolor="#EEEEEE">
							{EVENT_TEXT}<br /><span class="V9G">{CALNAME}</span>
						</td>
					</tr>
					<!-- loop showbottomevents_even off -->
				</table>
			</td>
		</tr>
	</table>
	<table width="737" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
			<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
			<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		</tr>
	</table>
	<!-- switch showbottom off -->
</center>
{FOOTER}
