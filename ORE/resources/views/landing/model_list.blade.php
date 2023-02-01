			 <div class="row flex-row d-block-xs">
					<div class="col-md-6 col-sm-6 col-  -12 flex-center">
                        <div class="vCard gladiator_landing_models">                                
                                <div class="tab-content">                                      
                                            <div> 
					<input type="hidden" name="origin" class="g_origin" value="{{ $origin  }}" /> 

  <img src="{{ cdn('images/silver.png') }}"  class="gladiator_model" data-gname="{{ cdn('images') }}" alt="2020 JEEP® GLADIATOR
LAUNCH EDITION" title="2020 JEEP® GLADIATOR
LAUNCH EDITION" /> 
                               
                                                    <p>Please select your color:</p>
														<label class="radio-container dot dot-silver"   data-color="silver"> 
                            <span style="display: none;">vehicle label</span>
                              <input type="radio" name="vehicle_color" class="vehicle_color" value="silver" checked="checked" />
                              <span class="checkmark"></span>
                               
                            </label>
							
                      			<label class="radio-container dot dot-white" data-color="white">
                              <span style="display: none;">vehicle label</span>
                              <input type="radio" name="vehicle_color" class="vehicle_color"  value="white" />
                              <span class="checkmark"></span>
                               
                            </label>
                            <label class="radio-container dot dot-red" data-color="red">
                              <span style="display: none;">vehicle label</span>
                              <input type="radio" name="vehicle_color" class="vehicle_color" value="red" />
                              <span class="checkmark"></span>
                               
                            </label>
                            <label class="radio-container dot dot-black"  data-color="black">
                            <span style="display: none;">vehicle label</span> 
                              <input type="radio" name="vehicle_color" class="vehicle_color"  value="black" />
                              <span class="checkmark"></span>
                               
                            </label>
                            
                            <label class="radio-container dot dot-granite"   data-color="granite"> 
                              <span style="display: none;">vehicle label</span>
                              <input type="radio" name="vehicle_color" class="vehicle_color"  value="granite" />
                              <span class="checkmark"></span>
                              
                            </label>	
								
						<div class="color-description font-weight-light font-size-12">
								
							<span  style="display: block;" class="color-description-silver">{{ config('ore.vehicle_colors.silver')}} </span>
							
                              <span  style="display: none;"  class="color-description-white">{{ config('ore.vehicle_colors.white')}}</span>
                              <span style="display: none;" class="color-description-red">{{ config('ore.vehicle_colors.red')}}</span>
                              <span style="display: none;" class="color-description-black">{{ config('ore.vehicle_colors.black')}}</span> 
                              <span style="display: none;" class="color-description-granite">{{ config('ore.vehicle_colors.granite')}} </span>
                            </div>								
			                   </div>
                       </div>
                      </div>
                    </div>      
                     <div class="col-md-6 col-sm-6 col-xs-12 text-left vehicle-specs-div">
                      <p class="margin-0">2020 {!! trans('landing.jeep') !!} </p>
                      <h3 class="gladiator-title">Gladiator Launch  Edition</h3>

                     <ul class="vehicle-specs-list">
                        <li> Rubicon with Winch-Capable Steel Front Bumper</li>
                        <li> Forward-Facing TrailCam	</li>
                        <li> Removable Body Color Hard Top with Freedom Panels</li>
                        <li> Heated Black Leather Seats with Leather Wrapped Dash</li>
                        <li> <span>8.4" Touchscreen with Alpine<sup>&reg;</sup> 9-Speaker Premium Audio</span></li>
                        <li> <span>Blind Spot Monitoring <span id="d_q_blind" class="disclaimer">?</span>, Adaptive Cruise Control<span id="d_q_adaptive" class="disclaimer">?</span>, Forward Collision Warning<span id="d_q_forward" class="disclaimer">?</span></span></li>
						<li> Spray-in Bedliner, 115V Bed Power Outlet, Trail Rail System, and Tonneau Cover</li>
                        <li> Exclusive Launch Edition Wheels and Tailgate Badge</li>
                        <li> Automatic Transmission</li>
                      </ul>

                     {{--  <p class="mrsp-para">MSRP* (includes Destination Charge)</p>
                      <p class="rBold big">$<span>{{  number_format(62310)  }}</span></p>
						  
					  <button id="submitBtn" class="btn text-uppercase  theme glad_booking" data-toggle="modal" data-target="#reviewSubmitPopUp" data-submit-text="Submit">Order Now ></button>
					  
					    @include('landing.review') --}}
                    </div>   
					 </div>  
					 
					
 			
                    <div class="container btm-container">
                      <div class="row">
                        <div class="col-md-4">
                          <img class="img-responsive" src="{{ cdn('images/tribute1.jpg') }}" alt="Best-in-Class Payload and Towing" title="Best-in-Class Payload and Towin">
                          <h4 class="text-uppercase">A TRIBUTE TO TOLEDO</h4>
                          <p>We pay homage to the 419 area code of Toledo, Ohio–the historic home of the Gladiator–with exclusive Launch Edition badging on the tailgate.</p>
                        </div>
                        <div class="col-md-4">
                           <img class="img-responsive" src="{{ cdn('images/everything.jpg') }}" alt="The Most Off-Road Capable Midsize Pickup Truck Ever" title="The Most Off-Road Capable Midsize Pickup Truck Ever">
                          <h4 class="text-uppercase">The Most Off-Road Capable Midsize Pickup Truck Ever<span id="d_q_off-road" class="disclaimer">*</span></h4>
                          <p>At the forefront of {!! trans('landing.jeep') !!} Brand 4x4 capability, the Gladiator includes front and rear locking differentials, the 4:1 Rock-Trac<sup>&reg;</sup> 4x4 system, segment-exclusive sway bar disconnect, rock rails, and FOX<sup>&reg;</sup> shocks.</p>
                        </div>
                        <div class="col-md-4">
                           <img class="img-responsive" src="{{ cdn('images/four-wheels.jpg') }}" alt="Open Air Freedom" title="Open Air Freedom">
                          <h4 class="text-uppercase">Open Air Freedom</h4>
                          <p>Gladiator allows you to fold down the windshield<span id="d_q_windshield" class="disclaimer">*</span>, remove the doors<span id="d_q_doors" class="disclaimer">*</span> and the Freedom Top<sup>&reg;</sup> hardtop, or flip back the zipperless Sunrider<sup>&reg;</sup> soft top for multiple ways to feel the wind, have fun in the sun, or sleep under the stars.</p>
                        </div>
						
						<div id="d_m_payload" class="disclaimerMessage" style="display:none;">
							<p>
								{{ config('ore.disclaimer.payload') }} 
							</p>
							<span id="d_c_payload" class="closeDisclaimer">X</span>
						</div>
						
						
						<div id="d_m_towing" class="disclaimerMessage" style="display:none;">
							<p>
								{{ config('ore.disclaimer.towing') }}
							</p>
							<span id="d_c_towing" class="closeDisclaimer">X</span>
						</div>
						
						<div id="d_m_off-road" class="disclaimerMessage" style="display:none;">
							<p>
								{{ config('ore.disclaimer.off-road') }}
							</p>
							<span id="d_c_off-road" class="closeDisclaimer">X</span>
						</div>
						
						<div id="d_m_windshield" class="disclaimerMessage" style="display:none;">
							<p>
								{{ config('ore.disclaimer.windshield') }}
							</p>
							<span id="d_c_windshield" class="closeDisclaimer">X</span>
						</div>
						
						<div id="d_m_doors" class="disclaimerMessage" style="display:none;">
							<p>
								{{ config('ore.disclaimer.doors') }}
							</p>
							<span id="d_c_doors" class="closeDisclaimer">X</span>
						</div>
						
				  <div id="d_m_blind" class="disclaimerMessage" style="display:none; position: fixed;">
                     <p>
                        {{ config('ore.disclaimer.blind_spot') }} 
                     </p>
                     <span id="d_c_blind" class="closeDisclaimer">X</span>
                  </div>
                  
                  
                  <div id="d_m_adaptive" class="disclaimerMessage" style="display:none; position: fixed;">
                     <p>
                        {{ config('ore.disclaimer.adaptive_cruise') }}
                     </p>
                     <span id="d_c_adaptive" class="closeDisclaimer">X</span>
                  </div>
                  
                  <div id="d_m_forward" class="disclaimerMessage" style="display:none; position: fixed;">
                     <p>
                        {{ config('ore.disclaimer.forward_collision') }}
                     </p>
                     <span id="d_c_forward" class="closeDisclaimer">X</span>
                  </div>
                      </div>
                    </div>   
                </div> 

					<div id="redirect-model" class="modal fade" role="dialog" >
							<div class="modal-dialog"> 
							  <div class="modal-content">
								<div class="modal-body">
								  <p> <img width="100%" src="https://d1jougtdqdwy1v.cloudfront.net/images/gladi.jpg" alt="Banner Image" class="img-responsive"></p>
								  <br>
								  
								  <p><center><b>Thanks for your Interest. Please visit jeep.com for more reference</b></center></p>
								</div>       
							  </div>  
							</div>
						</div>				
                   
                

 