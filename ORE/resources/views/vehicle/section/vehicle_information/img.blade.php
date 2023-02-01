<div class="col-xs-12 col-md-6 img-blade-field">
            <div class="imageFrame ">
			@if($return_array['params_vechType'] == 'cpo')
				 <img   src="{{ $return_array['photo_URL'] }}"  alt="driveFCA" class="centerHV vd_ImageUrl"   onerror="this.onerror=null;this.src='https://d1jougtdqdwy1v.cloudfront.net/images/loader.gif';"  />
			@else
           						  <img alt="driveFCA"
                                            src="https://www.{!! $return_array['make_url'] !!}.com/mediaserver/iris?client=FCAUS&market=U&brand={!! $return_array['make_code'] !!}&vehicle={!! substr($return_array['trim_code'],3,4) !!}_{!! substr($return_array['trim_code'],9,2) !!}&sa={!! substr($return_array['trim_code'],9,6) !!},{!! substr($return_array['trim_code'],17,3) !!},{{ $return_array['upper_level_pkg_cd'] }}&paint={{ $return_array['exterior_color_code'] }}&pov=fronthero&width=800&height=600&bkgnd=transparent&resp=png"   
                                          
                                            
                                           class="centerHV vd_ImageUrl"   onerror="this.onerror=null;this.src='https://d1jougtdqdwy1v.cloudfront.net/images/loader.gif';"  />
			@endif							   
											  
											 
											 
											 
            </div>
        </div>
		
		
		