<!DOCTYPE html>
<html>
<head>
</head>
<body>
<table cellpadding="0" cellspacing="0" border="0" align="center" width="650px" style="font-family: arial; font-size: 17px; line-height: 34px; border: 1px solid #05485e; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
	<tr>
		<td style="background: #05485e; text-align: center; padding: 10px; color: #ffffff; padding: 20px;" valign="top">Drive Alfa Romeo - Production Health Check</td>
	</tr>
	<tr>
		<td style="padding: 20px" valign="top">
				<p style="margin-top: 0px;"> Hello Team,</p> 				
				<p style="margin-bottom: 10px;">Below Services maybe down on the Drive Alfa Romeo platform. Please review the below list:</p>				
				<table border="1" cellpadding="5" cellspacing="1" > 
				<tbody>
					<th style="text-align: center;">S.No</th>
					<th>Vendor Service</th>
					<th>Service Status</th>
					<th>Validation Time</th>					
				</tbody>
				@foreach($failure_ApiList as $key => $val)
					<tr bgcolor="FF0000" style="color:#F8F8FF;">
						<td style="text-align: center;">{{ $loop->iteration }}</td>
						<td style="text-align: center;">{{ ucfirst(str_replace('_',' ',$key)) }}</td>
						<td style="text-align: center;">{{ $val }}</td>
						<td style="text-align: center;">{{ $uptime }}</td>
					</tr>
				@endforeach
				 
				@foreach($success_ApiList as $key => $val)
					<tr bgcolor="008000" style="color:#F8F8FF;">
						<td style="text-align: center;">{{ $loop->iteration }}</td>
						<td style="text-align: center;">{{ ucfirst(str_replace('_',' ',$key)) }}</td>
						<td style="text-align : center;">{{ $val }}</td>
						<td style="text-align: center;">{{ $uptime }}</td>
					</tr>
				@endforeach
				 
				</table> 
		</td>
	</tr>
	<tr>
		<td valign="middle" style="background: #05485e;padding: 20px;color: #ffffff; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; font-size: 14px; line-height: 20px;">
			 <p style="font-size: 14px; line-height: 22px">
				Sincerely,<BR/>
			    V2Soft Support Team.
			</p>
		</td>
	</tr>
</table>


</body>
</html>