{HEADER}
<center>
<table border="0" width="650" cellspacing="0" cellpadding="0" class="calborder">
	<tr>
		<td>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr valign="top">
					<td align="left" width="400" class="title"><h1>{DISPLAY_DATE}</h1><span class="V9G">{CALENDAR_NAME} {L_CALENDAR}</span></td>
					<td valign="top" align="right" width="120" class="navback">	
						<div style="padding-top: 3px;">
						<table width="90" border="0" cellpadding="0" cellspacing="0">
							<tr valign="top">
								<td><a class="psf" href="print.php?cal={CAL}&amp;getdate={GETDATE}&amp;printview=day"><img src="templates/{TEMPLATE}/images/day_on.gif" alt="{L_DAY}" border="0" /></a></td>
								<td><a class="psf" href="print.php?cal={CAL}&amp;getdate={GETDATE}&amp;printview=week"><img src="templates/{TEMPLATE}/images/week_on.gif" alt="{L_WEEK}" border="0" /></a></td>
								<td><a class="psf" href="print.php?cal={CAL}&amp;getdate={GETDATE}&amp;printview=month"><img src="templates/{TEMPLATE}/images/month_on.gif" alt="{L_MONTH}" border="0" /></a></td>
								<td><a class="psf" href="print.php?cal={CAL}&amp;getdate={GETDATE}&amp;printview=year"><img src="templates/{TEMPLATE}/images/year_on.gif" alt="{L_YEAR}" border="0" /></a></td>
							</tr>
						</table>
						</div>
					</td>
				</tr>  			
			</table>
      	</td>
    </tr>
	<tr>
		<td colspan="3" class="dayborder"><img src="images/spacer.gif" width="1" height="5" alt=" " /></td>
	</tr>
	<tr>
		<td colspan="3">
			<table border="0" cellspacing="0" cellpadding="5" width="100%">
				<tr>
					<td align="left" valign="top">
						<!-- switch some_events on -->
						<div class="V12"><b>{DAYOFMONTH}</b></div>
						<!-- loop events on -->
						<div style="padding: 6px;">
							<table width="100%" border="0" cellspacing="1" cellpadding="1">
								<tr>
									<td width="100" class="G10BOLD">{L_TIME}:</td>
									<td align="left" class="G10B">{EVENT_START}</td>
								</tr>
								<!-- switch location_events on -->
								<tr>
									<td valign="top" width="100" class="G10BOLD">{L_LOCATION}:</td>
									<td valign="top" align="left" class="G10B">{LOCATION}</td>
								</tr>
								<!-- switch location_events off -->
								<tr>
									<td valign="top" width="100" class="G10BOLD">{L_SUMMARY}:</td>
									<td valign="top" align="left" class="G10B">{EVENT_TEXT}</td>
								</tr>
								<!-- switch description_events on -->
								<tr>
									<td valign="top" width="100" class="G10BOLD">{L_DESCRIPTION}:</td>
									<td valign="top" align="left" class="G10B">{DESCRIPTION}</td>
								</tr>
								<!-- switch description_events off -->
							</table>
						</div>
						<!-- loop events off -->
						<!-- switch some_events off -->
															
						<!-- switch no_events on -->
						<div class="V12"><b>{L_NO_RESULTS}</b></div>
						<!-- switch no_events off -->
					</td>
				</tr>
			</table>		
		</td>
	</tr>
</table>
<table width="650" border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="tbll"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblbot"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
		<td class="tblr"><img src="images/spacer.gif" alt="" width="8" height="4" /></td>
	</tr>
</table>
</center>
{FOOTER}