
@if($return_array['mdoca']['status'] == 'available')
<div class="row iframe-field" style="display: none;"> 
	@if($return_array['route_validate'])
	<div class="container-fluid thanks-first-fluid text-center">
	   <img class="thanks_tick" src="{{ cdn('images/thanks_tick.jpg') }}" alt="thanks tick" title="thanks tick" />
	   <h2>Routeone Application Received!</h2>
	   <p class="thanks-paragraph-1">Thank you for getting in touch.</p>
	   <p class="thanks-paragraph-2">We appreciate you contacting us about pre-financing query. One of our customer happiness members will be getting back to you shortly.<br>While we do our best to answer your queries quickly, it may take about 10 hours to receive a response from us during peak hours.<br><br>Thanks in advance for your patience.<br><br>Have a great day!</p>
	</div>
	@else
		@if(env('APP_ENV') == 'dev' || (env('APP_ENV') == 'local') || (env('APP_ENV') == 'test'))
			<h4 color="red"> =============== PRODUCTION ENVIRONMENT CREDENTIALS APPLIED HERE =========</h4>
			<h4 color="red"> ===============DON'T SUBMT ANY LEAD  =========</h4>
			<iframe src="{!! $return_array['rone_link'] !!}" width="800" height="500" id="routeone-iframe"></iframe> 
		@else
			<iframe src="{!! $return_array['rone_link'] !!}" width="800" height="500" id="routeone-iframe"></iframe> 
		@endif
	@endif 
 </div>
 @endif
 <div id="review" class="tab-pane fade reg">
	<div class="header">
	   <span class="current_tab_title">Lease</span>
	   <h5 class="">Review &  Submit</h5>
	   <span class="current-toggle-tab review-submit-toggle">
	   	<label class="toggle-tab-switch">
		  <input type="checkbox">
		  <span class="slider round"><span class="slide-ex">Lease</span></span>
		</label>
	   </span>
	</div>
	{{--	{!! QrCode::size(230)->generate($return_array['qrcode']); !!} --}}
	<h5 class="sub-header">Vehicle Information</h5>
	<div id="vehicleInfo" class="tab-pane fade in active rBold vehicleTable"> {!! $return_array['vehicle'] !!} </div>
	<div class="pkgOptns">
	   <h5 class="sub-header">Dealer's Information</h5>
	   <div class="row">
		  <div class="col-xs-12 col-sm-6 col-md-6">
		  @if( $return_array['dlr_dba_name'] != '') 
			 <h4>{{ $return_array['dlr_dba_name'] }} </h4>
			 <i class="fa fa-map-marker" aria-hidden="true"></i>
			 <a target="_blank" href="https://www.google.com/maps/search/?api=1&query={{ $return_array['dealerAddress1']}} {{ $return_array['dealerAddress2']}}{{ $return_array['dealerCity']}}{{ $return_array['dealerState']}}{{ $return_array['dealerZip']}}" style="color: #000;" class="review_gmap" data-dealername="{{ $return_array['dealerName'] }}" data-gmapaddress="{{ $return_array['dealerAddress1']}} {{ $return_array['dealerAddress2']}}{{ $return_array['dealerCity']}}{{ $return_array['dealerState']}}{{ $return_array['dealerZip']}}">
			 {{ $return_array['dealerAddress1']}}  {{ $return_array['dealerAddress2']}}<br> {{ $return_array['dealerCity']}}, {{ $return_array['dealerState']}} {{ $return_array['dealerZip']}}
			 </a>
			 @else
			  <h4 class="text-danger"> <i class="fa fa-times text-danger" aria-hidden="true"></i>  No Dealer Information found this dealer code.</h4>
			@endif
		  </div>
		  <div class="col-xs-12 col-sm-6 col-md-6">
		  @if($return_array['phoneNumber']!='')
			 <h5>
				<i class="fa fa-phone-square" aria-hidden="true"></i> 
				<a href="tel:{{ $return_array['phoneNumber'] }}" target="_blank" title="{{ $return_array['phoneNumber'] }}" alt="{{ $return_array['phoneNumber'] }}" class="review-telephone">
				@php 
				$data = $return_array['phoneNumber'];
				if($data!=""){
				echo "(".substr($data, 0, 3).") ".substr($data, 3, 4)."".substr($data,7);
				}
				@endphp
				</a>
			 </h5>
			 @endif
			 @if($return_array['demail']!='')
			 <h5><i class="fa fa-envelope-open" aria-hidden="true"></i> <a href="mailto:{{ $return_array['demail'] }}&subject=fcaore"  title="{{ $return_array['demail'] }}" alt="{{ $return_array['demail'] }}" class="review_email">{{ $return_array['demail'] }}</a></h5>
			 @endif
		  </div>
	   </div>
	</div>
	<div class="pkgOptns">
	   <h5 class="sub-header">Package and Options</h5>
	   <div class="row">
		  @if (count($return_array['opt']) > 0)
		  @foreach ($return_array['opt'] as $ov => $ovov)
		  @if($ovov!="")	
		  <div class="col-xs-12 col-sm-6 col-md-4 packageandoption_ul">
			 <ul class="avoid">
				<li>{{ $ovov }}</li>
			 </ul>
		  </div>
		  @endif
		  @endforeach
		  @else
		  <div class="col-sm-12">
			 <p>No Package Availalbe.</p>
		  </div>
		  @endif			
	   </div>
	</div>
	<div id="ore_review" class="submittodealer_wrapper"> </div>
 </div>
 </div>
 <div class="modal fade" id="reviewSubmitPopUp"	 data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
	   <!-- Modal content-->
	   <div class="modal-content">
		  <div class="modal-header">
			 <button type="button" class="close submit-header-close" data-dismiss="modal">&times;</button>
			 <h4 class="modal-title submit-header">Submit to Dealer</h4>
		  </div>
		  @include('landing.confirmation')
		  @include('landing.waitlist_confirmation')
		  @foreach ($return_array['stages'] as $stage)
		  <div class="modal-body reg mylead_thanks">
			 <form id="reviewSubmit" >
			 <input name="current_tier_value" type="hidden" value="{{ $return_array['tier'] }}" />
			 <input name="dealerZip" id="dealerZip" type="hidden" value="{{ $return_array['dealerZip'] }}" />
			 <input name="current_submit_type" id="current_submit_type" type="hidden" value="submit-to-dealer" />
			 <div class="form-row">
    			<div class="form-group col-md-6">
					<label for="first" class="hide bmd-label-floating">First Name<span class="required"> * </span></label>
					<input type="name" class="form-control" id="first" name="first" placeholder="First Name *"  data-form-attr="names" data-error-msg="Please enter First Name" value="{{ $return_array['initial_form_first'] }}" tabindex="21" max-length="50" autofocus>
				</div>
				<div class="form-group col-md-6">
					<label for="last" class="hide bmd-label-floating">Last Name<span class="required"> * </span></label>
					<input type="name" class="form-control" id="last" name="last" placeholder="Last Name *" data-form-attr="names" data-error-msg="Please enter Last Name" value="{{ $return_array['initial_form_last'] }}" tabindex="22"  max-length="50" >
				</div>
			</div>
				<!-- <div class="form-group">
				   <label for="streetline1">Home Address<span class="required"> </span></label>
				   <input type="text" class="form-control" id="streetline1" name="streetline1" placeholder="Street Line1" value="{{ $stage->streetline1 }}"   tabindex="23">
				</div>
				<div class="form-group">
				   <input type="hidden" class="form-control" id="streetline2" name="streetline2"  placeholder="Street Line2"  >
				</div>
				<div class="form-group row">
				   <div class="col-xs-12 col-md-6">
					  <label style="display: none;" for="city">city</label>
					  <input type="text" class="form-control" id="city" name="city" placeholder="City" value="{{ $stage->city }}" tabindex="24" >
				   </div>
				   <div class="col-xs-12 col-md-6">
					  <label style="display: none;" for="regioncode">State</label>
					  <select class="form-control" id="regioncode" tabindex="25" name="regioncode" onmousedown="if(this.options.length>8){this.size=8;}" onchange='this.size=0;' onblur="this.size=0;">
						 <option value="">Select Your State</option>
						 <option value="AK" @if($stage->state == "AK") selected="selected" @endif>Alaska</option>
						 <option value="AL" @if($stage->state == "AL") selected="selected" @endif>Alabama</option>
						 <option value="AR" @if($stage->state == "AR") selected="selected" @endif>Arkansas</option>
						 <option value="AZ" @if($stage->state == "AZ") selected="selected" @endif>Arizona</option>
						 <option value="CA" @if($stage->state == "CA") selected="selected" @endif>California</option>
						 <option value="CO" @if($stage->state == "CO") selected="selected" @endif>Colorado</option>
						 <option value="CT" @if($stage->state == "CT") selected="selected" @endif>Connecticut</option>
						 <option value="DC" @if($stage->state == "DC") selected="selected" @endif>District of Columbia</option>
						 <option value="DE" @if($stage->state == "DE") selected="selected" @endif>Delaware</option>
						 <option value="FL" @if($stage->state == "FL") selected="selected" @endif>Florida</option>
						 <option value="GA" @if($stage->state == "GA") selected="selected" @endif>Georgia</option>
						 <option value="HI" @if($stage->state == "HI") selected="selected" @endif>Hawaii</option>
						 <option value="IA" @if($stage->state == "IA") selected="selected" @endif>Iowa</option>
						 <option value="ID" @if($stage->state == "ID") selected="selected" @endif>Idaho</option>
						 <option value="IL" @if($stage->state == "IL") selected="selected" @endif>Illinois</option>
						 <option value="IN" @if($stage->state == "IN") selected="selected" @endif>Indiana</option>
						 <option value="KS" @if($stage->state == "KS") selected="selected" @endif>Kansas</option>
						 <option value="KY" @if($stage->state == "KY") selected="selected" @endif>Kentucky</option>
						 <option value="LA" @if($stage->state == "LA") selected="selected" @endif>Louisiana</option>
						 <option value="MA" @if($stage->state == "MA") selected="selected" @endif>Massachusetts</option>
						 <option value="MD" @if($stage->state == "MD") selected="selected" @endif>Maryland</option>
						 <option value="ME" @if($stage->state == "ME") selected="selected" @endif>Maine</option>
						 <option value="MI" @if($stage->state == "MI") selected="selected" @endif>Michigan</option>
						 <option value="MN" @if($stage->state == "MN") selected="selected" @endif>Minnesota</option>
						 <option value="MO" @if($stage->state == "MO") selected="selected" @endif>Missouri</option>
						 <option value="MS" @if($stage->state == "MS") selected="selected" @endif>Mississippi</option>
						 <option value="MT" @if($stage->state == "MT") selected="selected" @endif>Montana</option>
						 <option value="NC" @if($stage->state == "NC") selected="selected" @endif>North Carolina</option>
						 <option value="ND" @if($stage->state == "ND") selected="selected" @endif>North Dakota</option>
						 <option value="NE" @if($stage->state == "NE") selected="selected" @endif>Nebraska</option>
						 <option value="NH" @if($stage->state == "NH") selected="selected" @endif>New Hampshire</option>
						 <option value="NJ" @if($stage->state == "NJ") selected="selected" @endif>New Jersey</option>
						 <option value="NM" @if($stage->state == "NM") selected="selected" @endif>New Mexico</option>
						 <option value="NV" @if($stage->state == "NV") selected="selected" @endif>Nevada</option>
						 <option value="NY" @if($stage->state == "NY") selected="selected" @endif>New York</option>
						 <option value="OH" @if($stage->state == "OH") selected="selected" @endif>Ohio</option>
						 <option value="OK" @if($stage->state == "OK") selected="selected" @endif>Oklahoma</option>
						 <option value="OR" @if($stage->state == "OR") selected="selected" @endif>Oregon</option>
						 <option value="PA" @if($stage->state == "PA") selected="selected" @endif>Pennsylvania</option>
						 <option value="PR" @if($stage->state == "PR") selected="selected" @endif>Puerto Rico</option>
						 <option value="RI" @if($stage->state == "RI") selected="selected" @endif>Rhode Island</option>
						 <option value="SC" @if($stage->state == "SC") selected="selected" @endif>South Carolina</option>
						 <option value="SD" @if($stage->state == "SD") selected="selected" @endif>South Dakota</option>
						 <option value="TN" @if($stage->state == "TN") selected="selected" @endif>Tennessee</option>
						 <option value="TX" @if($stage->state == "TX") selected="selected" @endif>Texas</option>
						 <option value="UT" @if($stage->state == "UT") selected="selected" @endif>Utah</option>
						 <option value="VA" @if($stage->state == "VA") selected="selected" @endif>Virginia</option>
						 <option value="VT" @if($stage->state == "VT") selected="selected" @endif>Vermont</option>
						 <option value="WA" @if($stage->state == "WA") selected="selected" @endif>Washington</option>
						 <option value="WI" @if($stage->state == "WI") selected="selected" @endif>Wisconsin</option>
						 <option value="WV" @if($stage->state == "WV") selected="selected" @endif>West Virginia</option>
						 <option value="WY" @if($stage->state == "WY") selected="selected" @endif>Wyoming</option>
					  </select>
				   </div>
				   <input type="hidden" id="apartment" name="apartment" value=""  /> 
				</div> -->
				<div class="form-row">
				   <div class="col-xs-12 col-md-6 form-group ">
					  <label for="postalcode" class="hide">Zip<span class="required"> * </span></label>
					  <input type="tel" data-form-attr="zip" class="form-control noControls" id="postalcode" data-max-length="5" placeholder="Zip Code *" name="postalcode" data-error-msg="Please enter valid Zip Code" value="{{ $return_array['initial_form_postalcode'] }}" tabindex="26" inputmode="numeric" pattern="[0-9]*">
				   </div>
				   <div class="col-xs-12 col-md-6 form-group ">
					  <label for="contact_phone" class="hide">Phone<span class="required"> * </span></label>
					  <input type="tel"  class="form-control noControls" id="contact_phone"   data-max-length="12" placeholder="Phone" data-form-attr="phone" name="contact_phone" data-error-msg="Please enter valid phone number" value="{{ $return_array['initial_form_contact_phone'] }}" tabindex="27" inputmode="numeric" pattern="[0-9]*">
				   </div>
				</div>
				<div class="col-md-12 form-group ">
					<label for="contact_email" class="hide">Email<span class="required"> * </span></label>
					<input type="text" class="form-control noControls" id="contact_email" placeholder="Email *" name="contact_email" data-form-attr="email" data-error-msg="Please enter valid Email" value="{{ $return_array['initial_form_contact_email'] }}" tabindex="28"> 
				</div>
				<div class="col-md-12 form-group text-center">
					<div class="custom-control custom-checkbox">
					    <input type="checkbox" name="chk_box_home_delivery" class="custom-control-input" id="review_chk_box_home_delivery" {{ isset($return_array['initial_form_home_delivery']) ? ($return_array['initial_form_home_delivery'] == 'false' ? '' :'checked') :'checked' }}/>
					     
						<label class="custom-control-label" for="chk_box_home_delivery"> I would like to learn about delivery options </label> <span id="l_q_wouldreview" class="badge disclaimer span_disclaimers">1</span>
					</div> 

					
				</div>
				{{--
				<div class="row">
				   <div class="col-xs-12 col-md-12">
					  <div class="row">
						 <div class="col-xs-12 col-md-12">
							<label for="captcha">Fill the captcha for anti-spam:</label>
						 </div>
						 <div class="col-xs-12 col-md-12">
							<div class="row">
							   <div class="col-xs-12 col-md-4 form-group">
								  <input type="text" class="form-control noControls" id="captcha" name="captcha_challenge" pattern="[A-Z]{6}" placeholder="Captcha" data-form-attr="name" data-error-msg="Please enter the captcha"  autocomplete="off" tabindex="29" max-length="6" />   
							   </div>
							   
							   
							   <div class="col-xs-12 col-md-4">
								  <span class="captch_loader">
								  <img src="{{ env('APP_URL') }}/image_color/load" alt="CAPTCHA" class="captcha-image">
								  <button type="button" class="btn theme" tabindex="30" value="null" id="refresh" ><i class="fa fa-refresh"></i><span style="display: none;"> button</span></button> 
								  </span> 
							   </div>
							  
							</div>
						 </div>
					  </div>
				   </div>
				</div>
				--}}
				<div id="l_m_wouldreview" class="disclaimerMessage" style="display:none;">
							<p>1. Available through participating dealers.							</p>
							<span id="l_c_wouldreview" class="closeDisclaimer">X</span>
						</div>
				<div class="form-group text-center ore_leadsubmit_wrapper">
					<input tabindex="31" type="button" class="btn theme gcss-button size-15 ore_leadsubmit review-popup-submit" value="Submit Request">
				</div>
				<div id="responseTxt" class="alert text-center marginY-5 hideMe"></div>
				<div class="form-group text-center lead_bottom">
				   <div class="row">
					  <div class="col-xs-12 col-md-12">
						 <div class="col-xs-12 col-md-12">
							FCA US LLC does not intentionally market to children under 16 years old. By submitting this request, you are confirming you are 16 years of age or older. FCA US LLC understands the importance of your online privacy. By submitting your mobile phone number, you also acknowledge that FCA US LLC or its parent, subsidiary or affiliated companies or one of their authorized dealers or representatives may send you commercial text messages. Such contact may use automated technology. You consent and agree to that type of contact and more generally to the FCA US LLC online privacy policy when you submit your registration. Standard text message and data rates may apply. You can opt out at any time. You are not required to agree to this as a condition of purchasing any property, goods, or services.  
						 </div>
					  </div>
				   </div>
				</div>
			 </form>
		  </div>
		  @endforeach
	   </div>
	</div>
 </div>
 </div>