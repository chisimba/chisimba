{HEADER}
<center>
	<table width="676" border="0" cellspacing="0" cellpadding="0" class="calborder">
		<tr>
			<td align="center" valign="middle" bgcolor="white">
				<table width="100%" border="0" cellspacing="0" cellpadding="0">
					<tr>
						<td align="left" width="120" class="navback">
							&nbsp;
						</td>
						<td class="navback">
							<table width="100%" border="0" cellspacing="0" cellpadding="0">
								<tr>
									<td align="right" width="45%" class="navback">
										<a class="psf" href="year.php?cal={CAL}&amp;getdate={PREV_YEAR}"><img src="templates/{TEMPLATE}/images/left_day.gif" alt="[Previous Year]" border="0" align="right" /></a>
									</td>
									<td align="center" width="10%" class="title" nowrap="nowrap" valign="middle">
										<h1>{THIS_YEAR}</h1>
									</td>
									<td align="left" width="45%" class="navback">
										<a class="psf" href="year.php?cal={CAL}&amp;getdate={NEXT_YEAR}"><img src="templates/{TEMPLATE}/images/right_day.gif" alt="[Next Year]" border="0" align="left" /></a>
									</td>
								</tr>
							</table>
						</td>
						<td align="right" width="120" class="navback">
							<table width="120" border="0" cellpadding="0" cellspacing="0">
								<tr>
								<td><a class="psf" href="day.php?cal={CAL}&amp;getdate={GETDATE}"><img src="templates/{TEMPLATE}/images/day_on.gif" alt="{L_DAY}" border="0" /></a></td>
								<td><a class="psf" href="week.php?cal={CAL}&amp;getdate={GETDATE}"><img src="templates/{TEMPLATE}/images/week_on.gif" alt="{L_WEEK}" border="0" /></a></td>
								<td><a class="psf" href="month.php?cal={CAL}&amp;getdate={GETDATE}"><img src="templates/{TEMPLATE}/images/month_on.gif" alt="{L_MONTH}" border="0" /></a></td>
								<td><a class="psf" href="year.php?cal={CAL}&amp;getdate={GETDATE}"><img src="templates/{TEMPLATE}/images/year_on.gif" alt="{L_YEAR}" border="0" /></a></td>
							</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
	<br />
	<table border="0" width="670" cellspacing="0" cellpadding="0">
		<tr>
			<td width="210" valign="top" align="left">
				{MONTH_MEDIUM|01}
			</td>
			<td width="20">
				<img src="images/spacer.gif" width="20" height="1" alt=" " />
			</td>
			<td width="210" valign="top" align="left">
				{MONTH_MEDIUM|02}
			</td>
			<td width="20">
				<img src="images/spacer.gif" width="20" height="1" alt=" " />
			</td>
			<td width="210" valign="top" align="left">
				{MONTH_MEDIUM|03}
			</td>
			<td width="20" rowspan='8'>
				<img src="images/spacer.gif" width="20" height="1" alt=" " />
			</td>
			<td rowspan='8' valign='top'>{SIDEBAR}</td>
		</tr>
		<tr>
			<td colspan="5">
				<img src="images/spacer.gif" width="1" height="20" alt=" " />
			</td>
		</tr>
		<tr>
			<td width="210" valign="top" align="left">
				{MONTH_MEDIUM|04}
			</td>
			<td width="20">
				<img src="images/spacer.gif" width="20" height="1" alt=" " />
			</td>
			<td width="210" valign="top" align="left">
				{MONTH_MEDIUM|05}
			</td>
			<td width="20">
				<img src="images/spacer.gif" width="20" height="1" alt=" " />
			</td>
			<td width="210" valign="top" align="left">
				{MONTH_MEDIUM|06}
			</td>
		</tr>
		<tr>
			<td colspan="5">
				<img src="images/spacer.gif" width="1" height="20" alt=" " />
			</td>
		</tr>
		<tr>
			<td width="210" valign="top" align="left">
				{MONTH_MEDIUM|07}
			</td>
			<td width="20">
				<img src="images/spacer.gif" width="20" height="1" alt=" " />
			</td>
			<td width="210" valign="top" align="left">
				{MONTH_MEDIUM|08}
			</td>
			<td width="20">
				<img src="images/spacer.gif" width="20" height="1" alt=" " />
			</td>
			<td width="210" valign="top" align="left">
				{MONTH_MEDIUM|09}
			</td>
		</tr>
		<tr>
			<td colspan="5">
				<img src="images/spacer.gif" width="1" height="20" alt=" " />
			</td>
		</tr>
		<tr>
			<td width="210" valign="top" align="left">
				{MONTH_MEDIUM|10}
			</td>
			<td width="20">
				<img src="images/spacer.gif" width="20" height="1" alt=" " />
			</td>
			<td width="210" valign="top" align="left">
				{MONTH_MEDIUM|11}
			</td>
			<td width="20">
				<img src="images/spacer.gif" width="20" height="1" alt=" " />
			</td>
			<td width="210" valign="top" align="left">
				{MONTH_MEDIUM|12}
			</td>
		</tr>
	</table>
</center>
{FOOTER}
