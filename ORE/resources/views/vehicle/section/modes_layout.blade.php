<div class="col-xs-12 col-md-12">
            <div class="row">
                <div class="col-xs-12 reg">
                    <div class="itemInfo">
                            @include('vehicle.section.modes.title',['return_array' => $return_array]) 
                        <div class="tabsContainer">
                                @include('vehicle.section.modes.tabs',['return_array' => $return_array]) 
                            <div class="tab-content">
							@if($return_array['params_vechType'] == 'new')
                                @include('vehicle.section.modes.lease',['return_array' => $return_array]) 
							@endif
                                @include('vehicle.section.modes.finance',['return_array' => $return_array]) 
                                @include('vehicle.section.modes.cash',['return_array' => $return_array])   
                              
                                @include('vehicle.render.lease_popup',['return_array' => $return_array])
                                @include('vehicle.render.finance_popup',['return_array' => $return_array]) 
                                @include('vehicle.render.cash_popup',['return_array' => $return_array])  
                                <div class="xs-visible" style="display: none;">
                                     @include('vehicle.section.vehicle_information.img',['return_array' => $return_array])
                                </div>                   
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('vehicle.section.modes.navigator')       
        </div>