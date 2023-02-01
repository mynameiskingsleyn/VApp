<div class="row">
        <div class="col-xs-12 col-md-12">    
              <div class="tab-content paddingY-5 moreaboutthis_vehicle">
               @include("vehicle.render.vehicle_more_information",['return_array' => $return_array])
               @include("vehicle.render.tradeIn")
               @include("vehicle.render.service_protection")
               @include("vehicle.render.review",['return_array' => $return_array]) 
              </div>
            </div>
    </div> 