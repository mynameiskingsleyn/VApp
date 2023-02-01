<div class="row marginY-2">
            <div class="col-xs-12 text-center">
                <div class="greyBg fwd-bck-btns">
                	<button style="display: none;" class="btn size-15 rBold nav-back-btn mRight-3 gcss-button">Prev: <span class="reg"></span></button>	
                   <button class="btn size-15 rBold cNxt-btn mRight-3 gcss-button hide-on-review-tab">Next: <span class="reg">Trade In</span></button> 
				   
				   
				   {{--
				   <button class="btn text-uppercase size-15 rBold gcss-button-secondary mRight-3 explore-review explore-finance" style="display: none; border-radius: 0;">Apply for Credit</button>
				   --}}
				   
				 @if($return_array['mdoca']['status'] == 'available')  
				 <button class="btn gcss-button-secondary rBold btm_submitdealer submitToDealer" data-toggle="modal" data-target="#reviewSubmitPopUp" data-source="explore-finance-options" style="display:none">Apply for Credit</button>
				  @endif
				  
                   <button class="btn size-15 rBold theme hidden mRight-3 iframe-hide btm_submitdealer submitToDealer" style="display: none;" data-source="submit-to-dealer">Review And Submit</button> 
				   
                   <button id="submitBtn" class="btn size-15 rBold mRight-3 gcss-button btm_submitdealer submitToDealer" data-toggle="modal" data-target="#reviewSubmitPopUp" data-submit-text="Submit" style="display: none;"  data-source="submit-to-dealer">Submit to Dealer</button>
				   
				   
				   
                </div>
                </div>
        </div>	
         