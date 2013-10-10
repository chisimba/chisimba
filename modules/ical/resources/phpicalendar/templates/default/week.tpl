{HEADER}
<center>
	<table border="0" width="770" cellspacing="0" cellpadding="0">
		<tr>
			<td width="610" valign="top">
				<table width="610" border="0" cellspacing="0" cellpadding="0" class="calborder">
					<tr>
						<td align="center" valign="middle">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
							<tr valign="top">
								<td align="left" width="490" class="title"><h1>{DISPLAY_DATE}</h1><span class="V9G">{CALENDAR_NAME} {L_CALENDAR}</span></td>
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
						<td>
							<table width="100%" border="0" cellspacing="0" cellpadding="0" class="G10B">
								<tr>
									<td align="center" valign="top">
										<table width="100%" border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td align="left" valign="top" width="15" class="rowOff2" onmouseover="this.className='rowOn2'" onmouseout="this.className='rowOff2'" onclick="window.location.href='week.php?cal={CAL}&amp;getdate={PREV_WEEK}'">
													<div class="V12">&nbsp;<a class="psf" href="week.php?cal={CAL}&amp;getdate={PREV_WEEK}">&laquo;</a></div>
												</td>
												<td align="left" valign="top" width="15" class="rowOff" onmouseover="this.className='rowOn'" onmouseout="this.className='rowOff'" onclick="window.location.href='week.php?cal={CAL}&amp;getdate={PREV_DAY}'">
													<div class="V12">&nbsp;<a class="psf" href="week.php?cal={CAL}&amp;getdate={PREV_DAY}">&lsaquo;</a></div>
												</td>
												<td align="right" valign="top" width="15" class="rowOff" onmouseover="this.className='rowOn'" onmouseout="this.className='rowOff'" onclick="window.location.href='week.php?cal={CAL}&amp;getdate={NEXT_DAY}'">
													<div class="V12"><a class="psf" href="week.php?cal={CAL}&amp;getdate={NEXT_DAY}">&rsaquo;</a>&nbsp;</div>
												</td>
												<td align="right" valign="top" width="15" class="rowOff" onmouseover="this.className='rowOn'" onmouseout="this.className='rowOff'" onclick="window.location.href='week.php?cal={CAL}&amp;getdate={NEXT_WEEK}'">
													<div class="V12"><a class="psf" href="week.php?cal={CAL}&amp;getdate={NEXT_WEEK}">&raquo;</a>&nbsp;</div>
												</td>
												<td width="1"></td>
												<!-- loop daysofweek on -->
												<td width="80" {COLSPAN} align="center" class="{ROW1}" onmouseover="this.className='{ROW2}'" onmouseout="this.className='{ROW3}'" onclick="window.location.href='week.php?cal={CAL}&amp;getdate={DAYLINK}'">
													<a class="ps3" href="day.php?cal={CAL}&amp;getdate={DAYLINK}"><span class="V9BOLD">{DAY}</span></a> 
												</td>
												<!-- loop daysofweek off -->
											</tr>
											<tr valign="top" id="allday">
												<td width="60" class="rowOff2" colspan="4"><img src="images/spacer.gif" width="60" height="1" alt=" " /></td>
												<td width="1"></td>
												<!-- loop alldaysofweek on -->
												<td width="80" {COLSPAN} class="rowOff">
													<!-- loop allday on -->
													<div class="alldaybg_{CALNO}">
														{ALLDAY}
														<img src="images/spacer.gif" width="80" height="1" alt=" " />
													</div>
													<!-- loop allday off -->
												</td>
												<!-- loop alldaysofweek off -->
											</tr>
											<!-- loop row on -->
											<tr>
												<td rowspan="4" align="center" valign="top" width="60" class="timeborder">9:00 AM</td>
												<td width="1" height="15"></td>
												<td class="dayborder">&nbsp;</td>
											</tr>
											<tr>
												<td width="1" height="15"></td>
												<td class="dayborder2">&nbsp;</td>
											</tr>
											<tr>
												<td width="1" height="15"></td>
												<td class="dayborder">&nbsp;</td>
											</tr>
											<tr>
												<td width="1" height="15"></td>
												<td class="dayborder2">&nbsp;</td>
											</tr>
											<!-- loop row off -->
											<!-- loop event on -->
											<div class="eventfont">
												<div class="eventbg_{EVENT_CALNO}">{CONFIRMED}<b>{EVENT_START}</b></div>
												<div class="padd">{EVENT}</div>
											</div>
											<!-- loop event off -->
										</table>	
									</td>
								</tr>
							</table>
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
			<td width="10">
				<img src="images/spacer.gif" width="10" height="1" alt=" " />
			</td>
			<td width="170" valign="top">
				{SIDEBAR}
			</td>
		</tr>
	</table>
</center>
{FOOTER}
