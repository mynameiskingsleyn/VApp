<header>
	<div class="header-scroll brandPage-header">
		<div class="container">
			<div class="row no-gutters">
				<div class="col-xs-12 col-sm-5 text-sm-left left-menu-field ">
					<div class="brandLogos logoimagewrapper">
						<ul class="list-inline d-flex">
							<li>

								@if($page == 'landing')
								@elseif($page == 'sni')

								@else

								@endif
							</li>
						</ul>
					</div>
					<div class="navigation how-itworks-nav">
						<ul class="list-inline how-itworks-field">
							<li>
								<a tabindex="1" id="video-trigger" class="how-itworks-link" href="javascript:void(0);">
									<span>How it Works</span>
									<img src="{{ cdn('images/video_icon.svg') }}" />
								</a>
							</li>
						</ul>
					</div>
				</div>
				<div class="col-sm-2 center-icon text-center logoimagewrapper">
				<a tabindex="2" href="javascript: void(0);" class="imageFrame alfa-sni-frame top-logo-vehicle">
				 @if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')
							<img class="" alt="{{ $params_make }}" title="{{ $params_make }}" src="{{ asset('images/logo-drivefca.png') }}">
						  @else
								<img class="" alt="{{ $params_make }}" title="{{ $params_make }}" src="{{ secure_asset('images/logo-drivefca.png') }}">
							@endif
					</a>		
				</div>

				<div class="col-xs-12 col-sm-5 text-right text-sm-center right-menu-field dealer_rightmenu_address">
					<div class="@if($page == 'landing') navigation @endif  rBold">
						<ul class="list-inline">
							@if($page == 'landing')

							@elseif($page == 'sni')
							<li class="sni3">
								<span class="dealerAddress">
									{!! $return_dealer_header !!}
								</span>
							</li>
							@else
							<li class="sni-vehicle-logo sni3">
								<span class="dealerAddress">
									{!! $return_array['header'] !!}
								</span>
							</li>
							@endif
						</ul>
					</div>
				</div>

					 <div class="brand-logo">
					 	<a tabindex="2">
						@if($params_make == 'ram' || $params_make == 'fiat')
							@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')
								<img class="" alt="{{ $params_make }}" title="{{ $params_make }}" src="{{ asset('images/logos/2x/logo-'.$params_make.'.png') }}" /> 
							@else
								<img class="" alt="{{ $params_make }}" title="{{ $params_make }}" src="{{ secure_asset('images/logos/2x/logo-'.$params_make.'.png') }}" /> 
							@endif
						
						@else
							@if($params_make == 'jeep' || $params_make == 'chrysler')
								<img class="" alt="{{ $params_make }}" title="{{ $params_make }}" src="{{ cdn('images/logos/2x/cdjr-logo-'.$params_make.'tm.png') }}" />
							@else
								<img class="" alt="{{ $params_make }}" title="{{ $params_make }}" src="{{ cdn('images/logos/2x/cdjr-logo-'.$params_make.'.png') }}" />
							@endif
					
						@endif
					</a>
					 </div>
			</div>
		</div>
	</div>

	<div id="vidBox" style="display: none;">
		<div id="videCont">
			<video id="howitswork" loop controls>
				<source src="{{ cdn('videos/hiw_cdjr.mp4')}}" type="video/mp4">
			</video>
		</div>


	</div>
<div id="vidBox_initial" style="display: none;">
		<div id="videCont_initial">
			<video id="howitswork_initial" loop controls>
				<source src="{{ cdn('videos/hiw_cdjr.mp4')}}" type="video/mp4">
			</video>
		</div> 
	</div>

</header>
<div class="scroll-buttons d-fixed">
	<a class="up" style="display: none;" href="javascript: void(0);">
		<img src="{{ cdn('images/scroll-top.png') }}" alt="scroll top"></a>
</div>