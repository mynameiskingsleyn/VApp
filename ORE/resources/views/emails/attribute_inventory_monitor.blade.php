<!DOCTYPE html>
<html>

<head>
	<STYLE TYPE="text/css">
	.tableContainer th, .tableContainer td { font-family: Arial; font-size: 13px; }
</STYLE>
</head>

<body>
	<table cellpadding="0" cellspacing="0" border="0" align="center" width="650px" style="font-family: arial; font-size: 17px; line-height: 34px; border: 1px solid #05485e; border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">
		<tr>
			<td style="background: #05485e; text-align: center; padding: 10px; color: #ffffff; padding: 20px;" valign="top">Drive FCA - Audit Information</td>
		</tr>
		<tr>
			<td style="padding: 20px" valign="top">
				<p style="margin-top: 0px;"> Hello Team,</p>
				@if (isset($AuditDetails))
					@if (count($AuditDetails) > 0)			
						<p style="margin-bottom: 10px;">We have listed the IB File details as below. Please review the list:</p>	
						<table border="1" cellpadding="5" cellspacing="1" class ="tableContainer"> 
							<tbody>
								<!-- <th style="text-align: center;">Audit Sid</th> -->
								<th>Processed date</th> 
								<th>Incoming file name</th> 
								<th>File type</th>	
								<th>Total records(#)</th>	
								<th>Exception  records (#)</th>	
								<th>Inserted records in input table(#)</th>	
								<th>Categories (#)</th>	
								<th>Sub categories (#)</th>	
								<th>Category vehicle (#)</th>	
							</tbody>
								@foreach($AuditDetails as $k => $items)
								<tr>
									@foreach($items as $key => $val)
										@if($key != 'audit_sid')
										<td style="text-align: center;">{{ $val }}</td>
										@endif
									@endforeach 
								</tr>
								@endforeach 
							</tbody>				 
						</table> 
						<!-- <th>Processed date</th> 
								<th>Incoming file name</th> 
								<th>File type (IB/Dealer)</th>	
								<th>Total Number of records in the file</th>	
								<th>Number of exception  records</th>	
								<th>Number of records inserted in input table</th>	
								<th>Number of records inserted in Categories</th>	
								<th>Number of records inserted in sub Categories</th>	
								<th>Number of records inserted in Category vehicle</th>	 
						-->
					@endif
				@endif
				<p style="margin-bottom: 10px;">Below Attribute doesn't Match Filters:</p>
				<table border="1" cellpadding="5" cellspacing="1" class ="tableContainer">
					<tbody>
						<th style="text-align: center;">S.No</th>
						<th>Attribute</th>
						<th>Validation Time</th>
					</tbody>
					<tr>
						<td colspan="3" style="text-align: center;"><h3 style="color:black">Drive Type</h3></td>
					</tr>
					@if (!empty($Attribute['drive_type'])) 
						@foreach($Attribute['drive_type'] as $key => $val)
						<tr>
							<td style="text-align: center;">{{ $loop->iteration }}</td>
							<td style="text-align: center;">{{ $val }}</td>
						 
							<td style="text-align: center;">{{ $uptime }}</td>
						</tr>
						@endforeach
					@else
						<tr>
							<td colspan="3" style="text-align: center;">No attributes available</td>
						</tr>
					@endif
                    <tr>
						<td colspan="3" style="text-align: center;"><h3 style="color:black">Engine Description</h3></td>

					</tr>
					@if (!empty($Attribute['eng_desc']))
						@foreach($Attribute['eng_desc'] as $key => $val)
						<tr>
							<td style="text-align: center;">{{ $loop->iteration }}</td>
							<td style="text-align: center;">{{ $val }}</td>
							<td style="text-align: center;">{{ $uptime }}</td>
						</tr>
						@endforeach
					@else
						<tr>
							<td colspan="3" style="text-align: center;">No attributes available</td>
						</tr>
					@endif
					<tr>
						<td colspan="3" style="text-align: center;"><h3 style="color:black">Transmission Description</h3></td>

					</tr>
					@if (!empty($Attribute['transmission_desc']))
						@foreach($Attribute['transmission_desc'] as $key => $val)
						<tr>
							<td style="text-align: center;">{{ $loop->iteration }}</td>
							<td style="text-align: center;">{{ $val }}</td>
							 
							<td style="text-align: center;">{{ $uptime }}</td>
						</tr>
						@endforeach
					@else
						<tr>
							<td colspan="3" style="text-align: center;">No attributes available</td>
						</tr>
					@endif

				</table>
			</td>
		</tr>
		<tr>
			<td valign="middle" style="background: #05485e;padding: 20px;color: #ffffff; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px; font-size: 14px; line-height: 20px;">
				<p style="font-size: 14px; line-height: 22px">
					Sincerely,<BR />
					V2Soft Support Team.
				</p>
			</td>
		</tr>
	</table>


</body>

</html>