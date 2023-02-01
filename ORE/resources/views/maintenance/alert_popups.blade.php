@extends('layouts.maintenance')

@section('title', 'Alfa Romeo | Maintenance')

@section('content')
<div class="container">
    <div class="row">
          <img alt="Maintenance" class="image_maintenance" src="{{ asset('images/banner_new.jpg')}}"/>
    </div>
</div>

<!-- Modal -->
<div class="modal fade maintenance_model_wrapper" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
        <div class="modal-body modelbody_maintenance">
         <img class="maintanance_logo" src="{{ asset('images/maintanance.png')}}" alt="maintanance logo"/>
		     <h5 class="fontsize_apologize fontsize_apologize_margin">WE APOLOGIZE FOR THE INCONVENIENCE</h5>
			 <h5 class="fontsize_apologize">OUR WEBSITE IS UNDER MAINTENANCE</h5>
			 <hr>
			 <h5 class="fontsize_apologize onlineshortly" style="color:#c3aec0;">WE WILL BE BACK ONLINE SHORTLY.</h5>
		</div>
      </div>
    </div>
</div>
<!-- Modal -->
<script type="text/javascript">
    $(window).on('load',function(){
        $('#exampleModalCenter').modal('show');
    });
	$('#exampleModalCenter').modal({backdrop: 'static', keyboard: false})  
</script>

@endsection