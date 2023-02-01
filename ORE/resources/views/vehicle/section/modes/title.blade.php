<h4 class="header rBold text-uppercase vd_title">
<h4 class="header rBold text-uppercase vd_title">
 @php
				$combine_vehicle_name =  $return_array['vehicle_params']['model'].' '.$return_array['vehicle_params']['trim_desc'];
				$combine_name_replace = strpos(strtolower($return_array['vehicle_params']['trim_desc']), strtolower( $return_array['vehicle_params']['model']), 0);
				if(trim($combine_name_replace)==0 && trim($combine_name_replace)!='') 
							$combine_vehicle_name = $return_array['vehicle_params']['trim_desc'];
			@endphp
			{{ $return_array['vehicle_params']['year'] }} {{ $combine_vehicle_name }} {{ $return_array['drive_type'] }}

     
</h4>

</h4>