<table width="735" border="0" cellspacing="1" cellpadding="2" class="monthback">
	<tr>
		<!-- loop weekday on -->
		<td valign="top" width="105" height="12" class="dateback">
			<center class="V9BOLD">{LOOP_WEEKDAY}</center>
		</td>
		<!-- loop weekday off -->
	</tr>
	<!-- loop monthweeks on -->
	<tr>
		<!-- loop monthdays on -->
		<!-- switch notthismonth on -->
		<td class="monthoff">
			<div align="right">
				<a class="psf" href="day.php?cal={CAL}&amp;getdate={DAYLINK}">{DAY}</a>
			</div>
			{ALLDAY}
			{EVENT}	
		</td>
		<!-- switch notthismonth off -->
		<!-- switch istoday on -->
		<td class="monthon">
			<div align="right">
				<a class="psf" href="day.php?cal={CAL}&amp;getdate={DAYLINK}">{DAY}</a>
			</div>
			{ALLDAY}
			{EVENT}	
		</td>
		<!-- switch istoday off -->
		<!-- switch ismonth on -->
		<td class="monthreg">
			<div align="right">
				<a class="psf" href="day.php?cal={CAL}&amp;getdate={DAYLINK}">{DAY}</a>
			</div>
			{ALLDAY}
			{EVENT}	
		</td>
		<!-- switch ismonth off -->
		<!-- loop monthdays off -->
	</tr>
	<!-- loop monthweeks off -->
</table>
