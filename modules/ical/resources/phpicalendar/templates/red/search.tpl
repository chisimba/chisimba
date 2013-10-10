{HEADER}
<center>
<table border="0" width="520" cellspacing="0" cellpadding="0" class="calborder">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr valign="top">
					<td align="left" width="400" class="title"><h1>{L_RESULTS}</h1><span class="V9G">{CALENDAR_NAME} {L_CALENDAR}</span></td>
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
		<td class="dayborder"><img src="images/spacer.gif" width="1" height="1" alt=" "></td>
	</tr>
	<tr>
		<td align="left">
			<div style="padding: 10px;">
				<div><p class="V12">{L_QUERY}: {FORMATTED_SEARCH}</p></div>
				<!-- switch results on -->
				<font class="V12"><b><a class="ps3" href="day.php?cal={CAL}&amp;getdate={KEY}">{DAYOFMONTH}</a></b></font><br />
				<div style="margin-left: 10px; margin-bottom: 10px;">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
							<td width="120" class="G10BOLD">{L_TIME}:</td>
							<td align="left" class="G10B">{EVENT_START}</td>
						</tr><tr>
							<td width="120" class="G10BOLD">{L_LOCATION}:</td>
							<td align="left" class="G10B">{LOCATION}</td>
						</tr>
						<tr>
							<td valign="top" width="100" class="G10BOLD">{L_SUMMARY}:</td>
							<td valign="top" align="left" class="G10B">{EVENT_TEXT}</td>
						</tr>
						<!-- switch recur on -->
						<tr>
							<td valign="top" width="100" class="G10BOLD">{L_RECURRING_EVENT}:</td>
							<td valign="top" align="left" class="G10B">{RECUR}</td>
						</tr>
						<!-- switch recur off -->
						<!-- switch description on -->
						<tr>
							<td valign="top" width="100" class="G10BOLD">{L_DESCRIPTION}:</td>
							<td valign="top" align="left" class="G10B">{DESCRIPTION}</td>
						</tr>
						<!-- switch description off -->
					</table>
				</div>
				<!-- switch exceptions on -->		
				<font class="V10"><i>{L_EXCEPTION}</i>: <a class="ps3" href="day.php?cal={CAL}&amp;getdate={KEY}">{DAYOFMONTH}</a></font><br />
				<div style="margin-left: 10px;">
					<table width="100%" border="0" cellspacing="1" cellpadding="1">
						<tr>
							<td width="100" class="V10">{L_TIME}:</td>
							<td align="left" class="V10">{EVENT_START}</td>
						</tr>
						<tr>
							<td valign="top" width="100" class="V10">{L_SUMMARY}:</td>
							<td valign="top" align="left" class="V10">{EVENT_TEXT}</td>
						</tr>
						<!-- switch except_recur on -->
						<tr>
							<td valign="top" width="100" class="V10">{L_RECURRING_EVENT}:</td>
							<td valign="top" align="left" class="V10">{EXCEPT_RECUR}</td>
						</tr>
						<!-- switch except_recur off -->
						<!-- switch except_description on -->
						<tr>
							<td valign="top" width="100" class="V10">{L_DESCRIPTION}:</td>
							<td valign="top" align="left" class="V10">{EXCEPT_DESCRIPTION}</td>
						</tr>
						<!-- switch except_description off -->
					</table>
				</div>
				<!-- switch exceptions off -->		
				<br />
				<!-- switch results off -->
				
				<!-- switch no_results on -->
				<div align="center">
					<p class="V12">{L_NO_RESULTS}</p>
				</div>
				<!-- switch no_results off -->
				
				<div align="center">
					{SEARCH_BOX}
				</div>
				<br />
			</div>
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

