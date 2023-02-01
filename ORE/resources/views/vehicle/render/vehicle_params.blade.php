	@php
	$params = ['vin',  'seating_capacity','engine_horse_power','city_mpg','drive_type','body_style','ext_color_raw','eng_desc','transmission_desc'];
	$caption = ['','vin', 'Seating','Horse Power','Mpg (City/HWY)','Drive','Body Style', 'Exterior Color','Engine','Transmission'];
	$inc = 0;
	//'eng_desc','transmission_desc','body_style'
	
	@endphp
	
	   
	@foreach(range(1, count($caption)-1) as $key => $value)
	 
		 @php
				$statement = true;
				$vehi_caption = $caption[$key+1];
				$vehi_values  = array_key_exists($params[$key],$vehicle ) ? $vehicle[$params[$key]] : "";
				$vehi_hwy_mpg  = $vehicle['hwy_mpg'];
				$pre=""; $after="";
				
			if($vehi_caption == 'vin' && ($vehi_values=='' || strlen($vehi_values)!=17) ) $statement = false;
			if($vehi_caption == 'Seating' && $vehi_values<=0 ) $statement = false;
			if($vehi_caption == 'Horse Power' && $vehi_values<=0 )$statement = false;
			
		if($vehi_caption == 'Mpg (City/HWY)' && ($vehi_values<=0 || $vehi_hwy_mpg<=0)) $statement = false; 	 
					
			if($vehi_caption == 'Towing Capacity - Max' && $vehi_values<=0 ) $statement = false;
			if($vehi_caption == 'Drive' && $vehi_values=='' ) $statement = false;
			
			if($vehi_caption == 'Exterior Color' && $vehi_values=='' ) $statement = false;
			if($vehi_caption == 'Engine' && $vehi_values=='' ) $statement = false;
			
			if($vehi_caption == 'Transmission' && $vehi_values=='' ) $statement = false;
			if($vehi_caption == 'Body Style' && $vehi_values=='' ) $statement = false;
			
			if($vehi_caption == 'Horse Power')  $after=" HP"; 
			if($vehi_caption == 'Mpg (City/HWY)'){$pre="Up to ";$after=' /'.$vehi_hwy_mpg;}
			if($vehi_caption == 'Towing Capacity - Max')  $after=" LBS";

			if($vehi_caption == 'Engine Displacement')  $after=" CID";			
			
		@endphp
		
		@if($loop->first or ($loop->iteration-1) % 3 == 0)
			<div class="row">
		@endif
 
                                  <div class="col-xs-12 col-md-4">
                                      <div class="row no-gutters contentBlock">
											  <div class="col-xs-6">
												  <span class="rBold text-uppercase">
												  {{ $vehi_caption }}
												  @if($vehi_caption == 'Mpg (City/HWY)') 
												  <span id="l_q_mpg" class="badge disclaimer span_disclaimers">7</span> 
												  @endif
												  </span>
											  </div>
											  <div class="col-xs-6">
												  <span class="reg text-uppercase vd_vin">
												 @if($vehi_caption == 'Mpg (City/HWY)')
												 	@if($statement)  
														{{ $pre }} {{ $vehi_values }} {{ $after }}
													@else
													    {{ $pre }}  
													@endif
												@else
													{{ $pre }} {{ $vehi_values }} {{ $after }} 
												@endif
												  </span>
											  </div>
                                      </div>
                                  </div>   
		 @if($loop->last == true or $loop->iteration % 3 == 0)
				</div>  
		@endif

	
	@endforeach  

	<div id="l_m_mpg" class="disclaimerMessage" style="display:none;">
							<p>
							            
							7. EPA estimated mileage. Actual mileage may vary.
							</p>
							<span id="l_c_mpg" class="closeDisclaimer">X</span>
						</div>