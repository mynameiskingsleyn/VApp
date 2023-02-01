@php if($flag['Trade'])  { @endphp
          @if(array_key_exists("remainingvalue",$looptrade))
            <div class="trade-in-fea">
                <h5 class="sub-header">Trade-In</h5>
                <div class="row">
                    <div class="col-xs-12 col-sm-4 col-md-5">
                        <p><b>Your trade-in vehicle was assessed at {{ $looptrade['price']}}</b></p>
                          <p>You applied <b>
                          @php 
                          if (array_key_exists("remainingvalue",$looptrade)){
                            if($looptrade['remainingvalue']>0 ){ 
                              echo '$'.number_format($looptrade['remainingvalue']);
                            }else{ 
                                echo $looptrade['price'];
                            } 
                          }else {
                            echo $looptrade['price'];
                          }
                        @endphp 
                        </b> of your trade-in towards your new vehicle purchase. Your estimate is accurate until <b>{{ $tomorrow }}</b>. Upon Dealer inspection and market conditions, these values may be adjusted. Your dealer will confirm the final evaluation.</p>
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-3 text-md-top text-sm-left">
                     
                      <h6 class="rBold">Your Vehicle Details:</h6>
                      <p>{{ $looptrade['year']}} {{ $looptrade['make']}} {{ $looptrade['model']}} {{ $looptrade['series']}}<br/>Style: <span class="text-uppercase">{{ $looptrade['style']}}</span><br/> Mileage: {{ $looptrade['mileage']}}</p>
                    </div>
                    <div class="col-xs-12 col-sm-4 col-md-4 text-md-top text-sm-left">
                     
                      <h6 class="rBold">Options and Equipment:</h6>
                      <p>
                      @forelse($looptrade['options'] as $opt=>$val) 
                      {{ $val }}<br/> 
                      @empty  @endforelse 
                      </p>
                    </div>
                </div>
            </div>
          @endif
            @php  } @endphp 
			
            @php if($flag['Service'] && ($flag['Lease'] || $flag['finance']))  { @endphp
            <div class="service-pro-fea">
                    <h5 class="sub-header">MOPAR VEHICLE PROTECTION</h5>
                    <div class="row additionalCareBlocks display-flex">

                    @forelse($Service_lease as $slease)  
                            <div class="col-xs-12 col-md-6 contentBlock">
                                        <span class="categoryType bold">{{ $slease['toptext'] }}</span>
                                        <h4 class="mTop-1">{{ $slease['title'] }}</h4>
                                        <span class="reg">{{ $slease['subtitle'] }}</span>
                                        <p class="mTop-2">{{ $slease['desc'] }}</p>
                            </div>
                      @empty  @endforelse  

                      @forelse($Service_finance as $finance)  
                            <div class="col-xs-12 col-md-6 contentBlock">
                                        <span class="categoryType bold">{{ $finance['toptext'] }}</span>
                                        <h4 class="mTop-1">{{ $finance['title'] }}</h4>
                                        <span class="reg">{{ $finance['subtitle'] }}</span>
                                        <p class="mTop-2">{{ $finance['desc'] }}</p>
                            </div>
                      @empty  @endforelse       
                    </div>
            </div>

            @php  } @endphp 
            <div class="submitBlock text-center greyBg">
			   <button class="btn size-15 rBold from-bottom nav-back-btn mRight-3 gcss-button-secondary backto-service  merkle_bottom_serviceandprot">Prev: Service and Protection</button>  
			 @if($return_array['mdoca']['status'] == 'available')  
			<button class="btn gcss-button-secondary btm_submitdealer merkle_bottom_explorefinance" data-toggle="modal" data-target="#reviewSubmitPopUp" data-source="explore-finance-options">Apply for Credit</button>
			@endif
                <button class="btn gcss-button btm_submitdealer merkle_bottom_submittodealer" data-toggle="modal" data-target="#reviewSubmitPopUp" data-source="submit-to-dealer">Submit to Dealer</button>
            </div>