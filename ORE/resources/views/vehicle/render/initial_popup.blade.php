<div class="modal fade" id="initialPopUpThanks" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content popup-tks">
			<div class="modal-header">
				<button type="button" class="close initialPopUpThanksClose" data-dismiss="modal">×</button>
				<h4 class="modal-title submit-header red-c" style="text-transform: capitalize">THANK YOU <span class="initial_popup_header_name year-clr" style="text-transform: uppercase;"></span></h4>
			</div>


			<div class="modal-body">
				<p class="para-text">We've submitted your contact information. We will be in contact with you soon and look forward to helping you become part of the {{isset($return_array['params_makeName']) ? ucwords($return_array['params_makeName']) : 'Alfa Romeo'}} family.

				</p>
				<hr>
				<button type="button" class=" btn size-15 rBold gcss-button initialPopUpThanksContinue" data-dismiss="modal">Continue</button>

				<br><br>
			</div>

		</div>
	</div>
</div>

<div class="modal fade" id="initialPopUp" data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
		<!-- Modal content-->
		<div class="modal-content popup-wh">
			<div class="modal-header">
				<button type="button" class="close initialPopUpClose" data-dismiss="modal">×</button>
				<div class="popup_steps">
					<h4 class="riftsoft">GETTING YOU INTO YOUR
						<span class="themeClr"> <span class="initial_popup_header_year year-clr"></span>&nbsp;<span class="initial_popup_header_trim year-clr"></span></span> IS AS EASY AS
						<span class="themeClr">1 - 2 - 3</span>!
					</h4>
					<div class="steps-field-parent">
						<div class="steps-field d-flex">
							<div class="field_car ">
								<div class="field_steps_no themeClr">1...</div>
								<div class="field_img">
									<img src="{{ cdn('images/initial_popup/popup_car.svg') }}" class="img-responsive" alt='step-1'>
								</div>
								<div class="field_desc themeClr">
									Locate Your Vehicle from Dealer Inventory
								</div>

								<div class="field_status riftsoft">
									<img src="{{ cdn('images/initial_popup/green_tick.svg') }}" class="" alt='Step-Done'>Done
								</div>
							</div>
							<div class="field_car field_calc">
								<div class="field_steps_no themeClr">2...</div>
								<div class="field_img">
									<img src="{{ cdn('images/initial_popup/popup_calc.svg') }}" class="img-responsive" alt='step-2'>
								</div>
								<div class="field_desc themeClr">
									Enter Preference to Estimate Payment
								</div>
							</div>
							<div class="field_car field_doc">
								<div class="field_steps_no themeClr">3...</div>
								<div class="field_img">
									<img src="{{ cdn('images/initial_popup/popup_doc.svg') }}" class="img-responsive" alt='step-3'>
								</div>
								<div class="field_desc themeClr">
									Save time in dealership with some optional steps online
								</div>
							</div>
							<div class="field_car field_hand">
								<div class="field_steps_no themeClr">4...</div>
								<div class="field_img">
									<img src="{{ cdn('images/initial_popup/popup_hand.svg') }}" class="img-responsive" alt='step-4'>
								</div>
								<div class="field_desc themeClr">
									Your dealer will be in contact to confirm preferences and finalize purchase
								</div>
							</div>
						</div>
						<div class="initial_popup_video_field">
							<button class="btn theme rift-soft mrkl_init_popup_video" id="video-trigger_initial" style="padding: 3px 7px;     font-size: 16px;">Watch
								<span class="play_btn">How it works <img src="{{ cdn('images/initial_popup/play_icon.svg') }}"></span> to learn more
							</button>
						</div>
					</div>
				</div>
				<div>
					<h4 class="modal-title submit-header rift-soft">Now Let's Get started...</h4>
					<h5 class="sub-txt">To Help Us Get You Into Your <span class="initial_popup_header_year year-clr"></span>&nbsp;<span class="initial_popup_header_trim year-clr"></span>, Let's gather some information first!</h5>
				</div>
			</div>


			<div class="modal-body reg initialpopup_thanks">
				<form id="reviewSubmitInitialPopUp" name="registration">

					<div class="form-row row">

						<div class="input-placeholder col-md-6 form-group">
							<label class="hide" for='init_pop_first'>First Name</label>
							<input type="name" required class="form-control" id="init_pop_first" data-form-attr="names" data-error-msg="Please enter First Name" value="" tabindex="21" max-length="50" autofocus>
							<div class="placeholder">
								First Name<span>*</span>
							</div>
							<span class="init_pop_first_issue text-danger"></span>
						</div>

						<div class="input-placeholder col-md-6 form-group">
							<label class="hide" for="init_pop_last">Last Name</label>
							<input type="name" required class="form-control" id="init_pop_last" name="init_pop_last" data-form-attr="names" data-error-msg="Please enter Last Name" value="" tabindex="22" max-length="50" autofocus>
							<div class="placeholder">
								Last Name<span>*</span>
							</div>
							<span class="init_pop_last_issue text-danger"></span>
						</div>




					</div>

					<div class="form-row row">
						<div class="input-placeholder col-md-6 form-group">
							<label class="hide" for="init_pop_postalcode">Zip Code</label>
							<input type="tel" required data-form-attr="zip" class="form-control noControls" id="init_pop_postalcode" data-max-length="5" maxlength="5" name="init_pop_postalcode" data-error-msg="Please enter valid Zip Code" value="" tabindex="26" pattern="\d*" inputmode="numeric">
							<div class="placeholder">
								Zip Code<span>*</span>
							</div>
							<span class="init_pop_zip_issue text-danger"></span>
						</div>



						<div class="input-placeholder col-xs-12 col-md-6 form-group ">
							<label class="hide" for="init_pop_contact_phone">Phone number</label>
							<input type="tel" class="form-control noControls" id="init_pop_contact_phone" data-max-length="12" placeholder="Phone" data-form-attr="phone" name="init_pop_contact_phone" data-error-msg="Please enter valid phone number" value="" tabindex="27" pattern="\d*" inputmode="numeric">
						</div>
					</div>

					<div class="form-row row">
						<div class="input-placeholder col-md-12 form-group">
							<label class="hide" for="init_pop_contact_email">Email</label>
							<input type="email" required class="form-control noControls" id="init_pop_contact_email" name="init_pop_contact_email" data-form-attr="email" data-error-msg="Please enter valid Email" value="" tabindex="28">
							<div class="placeholder">
								Email<span>*</span>
							</div>
							<span class="init_pop_email_issue text-danger"></span>
						</div>
					</div>

					<div class="form-group text-center">
						<div class="custom-control custom-checkbox">
							<input type="checkbox" name="initial_chk_box_home_delivery" class="custom-control-input" id="initial_chk_box_home_delivery" data-form-attr="initial_chk_box_home_delivery" checked/>

							<label class="custom-control-label" for="initial_chk_box_home_delivery">I would like to learn about delivery options </label> <span id="l_q_would" class="badge disclaimer span_disclaimers">1</span>


						</div>

					</div>
					

					<div class="form-row row">
						<div class="form-group col-md-12 text-center ore_initialpopup_wrapper">
							<input tabindex="31" type="button" class="btn theme rBold ore_initialpopup initialPopUpContinue" value="Save & Continue">
						</div>
					</div>
					
					<div id="responseTxt" class="alert text-center marginY-5 hideMe"></div>
					<div class="row">
						<div class="form-group text-center lead_bottom">
							<div class="row">
								<div class="col-xs-12 col-md-12">
									<div class="col-xs-12 col-md-12 footer-txt">
										FCA US LLC does not intentionally market to children under 16 years old. By submitting this request, you are confirming you are 16 years of age or older. FCA US LLC understands the importance of your online privacy. By submitting your mobile phone number, you also acknowledge that FCA US LLC or its parent, subsidiary or affiliated companies or one of their authorized dealers or representatives may send you commercial text messages. Such contact may use automated technology. You consent and agree to that type of contact and more generally to the FCA US LLC online privacy policy when you submit your registration. Standard text message and data rates may apply. You can opt out at any time. You are not required to agree to this as a condition of purchasing any property, goods, or services.
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
              
			</div>
			<div id="l_m_would" class="disclaimerMessage" style="display:none;">
						<p class="text_disclaimers">

							1. Available through participating dealers.
						</p>
						<span id="l_c_would" class="closeDisclaimer">X</span>
					</div>
		</div>
	</div>
</div>




{{--

<div class="modal fade" id="initialPopUp"	 data-backdrop="static" data-keyboard="false">
	<div class="modal-dialog">
	   <!-- Modal content-->
	   <div class="modal-content">
		  <div class="modal-header">
		  <button type="button" class="close" data-dismiss="modal">×</button>
			 <h4 class="modal-title submit-header" style="text-transform: capitalize">To Help us get you into your <span class="initial_popup_header_year"></span> <span class="initial_popup_header_trim"></span></h4>
			 Let's gather some information first!
		  </div>


		  <div class="modal-body reg initialpopup_thanks">
			 <form id="reviewSubmit" name="registration">

			 <div class="form-row">
    			<div class="form-group col-md-6 init_pop_first">
          <label class="hide" for='init_pop_first'>First Name</label>
					<input type="name" class="form-control" id="init_pop_first" name="init_pop_first" placeholder="First Name *"  data-form-attr="names" data-error-msg="Please enter First Name" value="" tabindex="21" max-length="50" autofocus>
					<span class="init_pop_first_issue text-danger"></span>
				</div>

				<div class="form-group col-md-6 init_pop_last">
          <label class="hide" for='init_pop_last'>Last Name</label>
					<input type="name" class="form-control" id="init_pop_last" name="init_pop_last" placeholder="Last Name *" data-form-attr="names" data-error-msg="Please enter Last Name" value="" tabindex="22"  max-length="50" >
					<span class="init_pop_last_issue text-danger"></span>
				</div>

			</div>

				<div class="form-row">
				   <div class="col-xs-12 col-md-6 form-group ">
          <label class="hide" for='init_pop_postalcode'>Zip Code</label>
					  <input type="text" data-form-attr="zip" class="form-control noControls" id="init_pop_postalcode" data-max-length="5" placeholder="Zip Code *" name="init_pop_postalcode" data-error-msg="Please enter valid Zip Code" value="" tabindex="26">
					  <span class="init_pop_zip_issue text-danger"></span>
				   </div>
				   <div class="col-xs-12 col-md-6 form-group ">
          <label class="hide" for='init_pop_contact_phone'>Phone Number</label>
					  <input type="text"  class="form-control noControls" id="init_pop_contact_phone" data-max-length="12" placeholder="Phone" data-form-attr="phone" name="init_pop_contact_phone" data-error-msg="Please enter valid phone number" value="" tabindex="27">
				   </div>
				</div>
				<div class="col-md-12 form-group ">
          <label class="hide" for='init_pop_contact_email'>Email</label>
					<input type="text" class="form-control noControls" id="init_pop_contact_email" placeholder="Email *" name="init_pop_contact_email" data-form-attr="email" data-error-msg="Please enter valid Email" value="" tabindex="28">
				</div>


				<div class="form-group text-center ore_initialpopup_wrapper">
					<input tabindex="31" type="button" class="btn theme rBold ore_initialpopup" value="Save & Continue">
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

	   </div>
	</div>
 </div>
 --}}