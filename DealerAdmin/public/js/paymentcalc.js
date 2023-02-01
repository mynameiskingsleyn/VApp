	 $(document).ready(function() {	 
		 filter.init();	 
		
	});
	
	var filter = {
			init: function(){
				$(document).on('click','.paymentcalc', function(e){ 
						 var vin = $(this).data('vin');
						 filter.paymentLoader(vin);
					})
			},
			paymentLoader: function(vin){
				var url = 'paymentcalc';
				var data={
					NameOfAPI:"paymentcalc",
					vin : vin
				};
				var method = "POST";
				filter.ajaxCall(url,method,data);
			},
			 ajaxCall: function(url,requestType,data){ 
					var APIURL = 'http://localhost:8000/' + url; 
					$.ajax({
						  headers: {
							'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
							'X-Frame-Options': 'sameorigin',
							  'X-Content-Type-Options': 'nosniff'
							},
						url: APIURL,
						type: requestType,
						data:data,
						datatype: 'json',
						success: function (response) { 
								console.log(' Payment Response ');
							 console.log(response);
						},
						error: function (jqXHR, textStatus, errorThrown) { console.log(textStatus) }
					});
			} 
	} 