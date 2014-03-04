<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
		"http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<html>
<head>
	<meta http-equiv="content-type" content="text/html;charset={CHARSET}">
	<title>{CAL}</title>
	<link rel="stylesheet" type="text/css" href="../templates/{TEMPLATE}/default.css">
</head>
<body>
<center>
	<table border="0" width="430" cellspacing="0" cellpadding="0" class="calborder">
		<tr>
			<td align="center" class="sideback"><div style="height: 17px; margin-top: 3px;" class="G10BOLD">{CAL_TITLE_FULL}</div></td>
		</tr>
		<tr>
			<td align="left" class="V12">
				<div style="margin-left: 10px; margin-bottom:10px;">
					<p>{VTODO_TEXT}</p>
					<!-- switch description on -->
					<p>{DESCRIPTION}</p>
					<!-- switch description off -->
					<p>
					<!-- switch status on -->
					<b>{L_STATUS}</b>: {STATUS}<br />
					<!-- switch status off -->
					<!-- switch priority on -->
					<b>{L_PRIORITY}</b>: {PRIORITY}<br />
					<!-- switch priority off -->
					<!-- switch start_date on -->
					<b>{L_CREATED}</b>: {START_DATE}<br />
					<!-- switch start_date off -->
					<!-- switch due_date on -->
					<b>{L_DUE}</b>: {DUE_DATE}<br />
					<!-- switch due_date off -->
					</p>
				</div>
			</td>
		</tr>
	</table>
</center>
</body>
</html>
