@extends('layouts.vehicle')

@section('title')
  {{ ucwords($return_array['params_make']) }} Search New Inventory|  Vehicle Details
@endsection

@section('content')
		
		<section id="about" class="site-padding">            
               <div id="detailPage">
			     
			   <input type="hidden" name="make" id="make" value="{{ $return_array['params_make'] }}" />
			   <input type="hidden" name="model" id="model" value="{{ $return_array['params_model'] }}" />
			   <input type="hidden" name="year" id="year" value="{{ $return_array['params_year'] }}" />
			  <input type="hidden" name="vehicle_type" id="vehicle_type" value="{{ $return_array['params_vechType'] }}" />
			   <input type="hidden" name="vin" id="vin" value="{{ $return_array['vehicle_params']['vin'] }}" />
			   <input type="hidden" name="dealer_code" id="dealer_code" value="{{ $return_array['dlr_code'] }}" />
			   <input type="hidden" name="dlr_dba_name" id="dlr_dba_name" value="{{ $return_array['dlr_dba_name'] }}" />
			   <input type="hidden" name="tier" id="tier" value="{{ $return_array['tier'] }}" />
			    <input type="hidden" name="initialPopup" id="initialPopup" value="{{ $return_array['user_experience_popup_status'] }}" />
			   @foreach ($return_array['stages'] as $stage)
			<input type="hidden" id="cn_first" name="cn_first" value="{{ $stage->first_name }}">
			<input type="hidden" id="cn_last" name="cn_last" value="{{ $stage->last_name }}">
			<input type="hidden" id="cn_contact_email" name="cn_contact_email" value="{{ $stage->email }}">
			<input type="hidden" id="cn_contact_phone" name="cn_contact_phone" value="{{ $stage->phone }}">
			
			@endforeach
					@include('vehicle.section.vehicle_information.tabs',['return_array' => $return_array])
					<div class="container detailBody">
						@include('vehicle.section.vehicle_information.information',['return_array' => $return_array])
						@include('vehicle.section.vehicle_information.more_information',['return_array' => $return_array])
					</div>
				</div>
             
        </section> 
        @include('vehicle.render.initial_popup',['return_array' => $return_array])
		@include('vehicle.render.available_program_popup',['return_array' => $return_array])
@endsection 




