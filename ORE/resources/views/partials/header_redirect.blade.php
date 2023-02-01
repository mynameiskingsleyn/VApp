<header>
    <div class="header-scroll">
        <div class="container">
            <div class="row no-gutters">
                <div class="col-xs-12 col-sm-5 text-sm-left left-menu-field ">
                    <div class="brandLogos logoimagewrapper">
                    </div>
                    <div class="navigation how-itworks-nav">
                    </div>
                </div>
                <div class="col-sm-2 center-icon text-center logoimagewrapper">
                    <a tabindex="2" href="{{  env('APP_URL') }}" class="imageFrame alfa-sni-frame top-logo-vehicle">
                       	@if(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'test' || env('APP_ENV') == 'training')
								<img class="" alt="DriveFCA" title="DriveFCA" src="{{ asset('images/logo-drivefca.png') }}" />
							@else
								<img class="" alt="DriveFCA" title="DriveFCA" src="{{ secure_asset('images/logo-drivefca.png') }}" />
							@endif
                    </a>
                </div>
                <div class="col-xs-12 col-sm-5 text-right text-sm-center right-menu-field dealer_rightmenu_address">
                </div>
            </div>
        </div>
    </div>
</header>
<div class="scroll-buttons d-fixed">
    <a class="up" style="display: none;" href="javascript: void(0);">
        <img src="{{ cdn('images/scroll-top.png') }}" alt="scroll top"></a>
</div>