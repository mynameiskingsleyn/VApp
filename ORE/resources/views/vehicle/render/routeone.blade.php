 <iframe src="{{ Config::get('ore.routeone.endpoint')}}{{ Config::get('ore.routeone.endpoint_params')}}&partnerDealerId=MDOCAU&dealerId=ED4XU&referenceId={{ $current_session }}&vehicleYear={{ $VehicleInfo['year'] }}&vehicleMake={{ $VehicleInfo['make']}}" width="800" height="500"></iframe>
  
  {{--
  <form name="oemWaoSample" id="oemWaoSample" method="post"  action="{{ Config::get('ore.routeone.endpoint')}}{{ Config::get('ore.routeone.endpoint_params')}}&referenceId={{ $current_session }}">
            <input type="hidden" name="first_name" value="">
            <input type="hidden" name="last_name" value="">
            <input type="hidden" name="zip" value=""> 
            <input type="hidden" name="email" value=""> 
            <input type="hidden" name="phone" value=""> 
			 <input type="hidden" name="address" value=""> 
            <input type="hidden" name="address2" value=""> 
            <input type="hidden" name="city" value="">   
			
			<input type="hidden" name="trade_radio" value="@if($tradein['make']!='') Y @endif" />
			<input type="hidden" name="tradeinVehicleMake" value="@if($tradein['make']!='') $tradein['make'] @endif"> 
			<input type="hidden" name="tradeinVehicleModel" value="@if($tradein['make']!='') $tradein['model'] @endif "> 
			<input type="hidden" name="tradeinVehicleYear" value="@if($tradein['year']!='') $tradein['year'] @endif"> 
			<input type="hidden" name="veh_stock_num" value=""> 
			<input type="hidden" name="trade_in_vehicle_style" value="@if($tradein['style']!='') $tradein['style'] @endif"> 
			<input type="hidden" name="contractTerms_trade_owed" value="@if($tradein['price']!='') $tradein['price'] @endif"> 
			
	<input type="hidden" name="dealership_name" value="{{ $DealerInfo['dlr_dba_name'] }}"> 
    <input type="hidden" name="dealership_address" value="{{ $DealerInfo['dlr_shw_addr1'] }}{{ $DealerInfo['dlr_shw_addr2'] }}"> 
    <input type="hidden" name="dealership_city" value="{{ $DealerInfo['dlr_shw_city'] }}"> 
    <input type="hidden" name="dealership_state" value="{{ $DealerInfo['dlr_shw_state'] }}"> 
    <input type="hidden" name="dealership_zip" value="{{ $DealerInfo['dlr_shw_zip'] }}"> 
    <input type="hidden" name="dealership_phone" value="{{ $DealerInfo['dlr_shw_phone'] }}"> 
    <input type="hidden" name="dealership_contact_name" value="">  
    <input type="hidden" name="dealership_email" value="{{ $DealerInfo['dlr_email_dlr'] }}">
	
	<input type="hidden" name="vehicleYear" value="{{ $VehicleInfo['year'] }}">  
    <input type="hidden" name="vehicleMake" id="FormROnevehicleMake" value="{{ $VehicleInfo['make']}}"> 
    <input type="hidden" name="vehicleModel" value="{{ $VehicleInfo['model']}}"> 
    <input type="hidden" name="contractTerms_vehiclestyle" value="{{ $VehicleInfo['drive_type']}}"> 
    <input type="hidden" name="vehicle_vin" value="{{ $VehicleInfo['vin']}}"> 
    <input type="hidden" name="contractTerms_msrp" value="{{ $VehicleInfo['msrp']}}">
			
            <input type="hidden" name="birth_date" value=""> 
            <input type="hidden" name="coapplicantExists" value="N" > 
            <input type="hidden" name="co_first_name" value=""> 
            <input type="hidden" name="co_last_name" value=""> 
            <input type="hidden" name="co_relationship_code" value=""> 
            <input type="hidden" name="co_email" value=""> 
            <input type="hidden" name="co_phone" value=""> 
            <input type="hidden" name="co_birth_date" value="">
            <input type="hidden" name="residence_type" value=""> 
            <input type="hidden" name="time_at_address" value=""> 
            <input type="hidden" name="time_at_address1" value=""> 
            <input type="hidden" name="rent_mortgage" value=""> 
            <input type="hidden" name="co_address" value=""> 
            <input type="hidden" name="co_address2" value=""> 
            <input type="hidden" name="co_city" value=""> 
            <input type="hidden" name="co_State" value=""> 
            <input type="hidden" name="co_zip" value=""> 
            <input type="hidden" name="co_residence_type" value=""> 
            <input type="hidden" name="co_time_at_address" value=""> 
            <input type="hidden" name="co_time_at_address1" value=""> 
            <input type="hidden" name="co_rent_mortgage" value="">  
            
            <input type="hidden" name="partnerDealerId" value="MDOCAU">
            <input type="hidden" name="dealerId" value="ED4XU">
            <input type="hidden" name="fncSrcId" value="{{ $DealerInfo['dlr_code'] }}">
    </form>
	
  --}}
	