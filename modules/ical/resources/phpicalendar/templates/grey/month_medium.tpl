<table border="0" width="210" cellspacing="0" cellpadding="0" class="calborder">
	<tr>
		<td align="center" class="medtitle">{MONTH_TITLE}</td>
	</tr>
	<tr>
		<td>
			<table border="0" width="210" cellspacing="1" cellpadding="0" class="yearmonth">
				<tr>
					<!-- loop weekday on -->
					<td class="yearweek">{LOOP_WEEKDAY}</td>
					<!-- loop weekday off -->
				</tr>
				<!-- loop monthweeks on -->
				<tr>
					<!-- loop monthdays on -->
					<!-- switch notthismonth on -->
					<td class="yearoff">
						<a class="psf" href="day.php?cal={CAL}&amp;getdate={DAYLINK}">{DAY}</a>
					</td>
					<!-- switch notthismonth off -->
					<!-- switch istoday on -->
					<td class="yearon">
						<a class="psf" href="day.php?cal={CAL}&amp;getdate={DAYLINK}">{DAY}</a>
						<div align="center">
							{ALLDAY}
							{EVENT}
						</div>
					</td>
					<!-- switch istoday off -->
					<!-- switch ismonth on -->
					<td class="yearreg">
						<a class="psf" href="day.php?cal={CAL}&amp;getdate={DAYLINK}">{DAY}</a>
						<div align="center">
							{ALLDAY}
							{EVENT}
						</div>
					</td>
					<!-- switch ismonth off -->
					<!-- loop monthdays off -->
				</tr>
				<!-- loop monthweeks off -->	
			</table>
		</td>
	</tr>
</table>
