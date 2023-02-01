<div class="mylead_thanks_done" style="display:none;">	 
				<h5 class="text-center font-weight-bold margin-bottom-15">Thank you for your interest in acquiring one of our legendary {{isset($return_array['params_makeName']) ? ucwords($return_array['params_makeName']) : ''}} vehicles. </h5>
				<p class="text-center font-weight-bold margin-bottom-15">Your submission has been received and a representative from the dealership will be calling you shortly to confirm your preferences and vehicle availability. You will also receive an email confirmation with further details.</p>
          <p class="text-center font-weight-bold margin-bottom-15" >We appreciate your patience and your passion for style and race-inspired performance.</p>
		  
		  <p class="text-center font-weight-bold margin-bottom-15">{{$return_array['dealerName']}}</p>
		  <p class="text-center font-weight-bold margin-bottom-15">{{$return_array['dealerAddress1']}}</p>
		  <p class="text-center font-weight-bold margin-bottom-15 phone-number-field" x-ms-format-detection="none">{{$return_array['phoneNumber']}}</p>
		  <p class="text-center font-weight-bold margin-bottom-15">{{$return_array['params_year']}}/{{$return_array['params_makeName']}}/{{ $return_array['params_model'] }}/{{ $return_array['vin'] }}</p>
					 
			<div class="text-center" style="margin-bottom: 12px;"> 
			
			<div class="redirect_home" style="display: none;" class="bold"> </div>
			<div class="closing_home" style="display: none;" class="bold"> 
				
				<div style="padding-bottom: 10px">
					<span class="closing_home_btn">
						<button class="btn btn-danger" onclick="vehiclepage.CloseWebPage();">Close the Window</button>
					</span>
				</div>
				<span class="closing_home_timer"></span>

				
			</div>
				 
			</div>
</div>

