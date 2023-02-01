var global = {
 init: function() {

 },
 loaderShow: function(divid) {
	$('.dealerAdminLoader').addClass('tempo'); 
 },
 loaderHide: function(divid) {
	$('.dealerAdminLoader').removeClass('tempo'); 
 }
}


var ajax = {
 promise: function(url, requestType, data) {
  $.ajaxSetup({
   headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    'X-Frame-Options': 'sameorigin',
    'X-Content-Type-Options': 'nosniff',
   }
  });
  var appurl = $('#APP_URL').val();
  var tempurl = url;
  global.loaderShow('.loader');


  if (requestType == 'get') {
			   var jqxhr = $.get(appurl + url, function(result) {
				global.loaderHide('.loader');
			   }).fail(function(response, textStatus, jqXHR) {
				ajax.failure(url, response, textStatus, jqXHR);
				global.loaderHide('.loader');
			   });
  } else {
		   $.when($.ajax({
					 url: appurl + url,
					 cache: true,
					 type: requestType,
					 data: JSON.parse(data)
			}))
			.done(function(result, textStatus, jqXHR) {
					ajax.success(url, result);
			}).fail(function(response, textStatus, jqXHR) {
					 global.loaderHide('.loader');
					 ajax.failure(url, response, textStatus, jqXHR);

			});
  }
 },
 success: function(url, result) {
    url = 'api/v1/'+url;
  switch (url) {
   case "getAllMakes":
      
    break;
   case "model":

    break;
  }

 },
 failure: function(url, response, textStatus, jqXHR) {
  switch (url) {
   case "payment-calcultor":
    break;
   case "initial_lead":

    break;
  }


 },
 statusCode: function(url, responseObject, textStatus, jqXHR) {
  switch (url) {
   case "payment-calcultor":

    break;
  }
 }


}