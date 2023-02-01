var endpoints = {
	 init: function(name) {
		 var appurl = $('#APP_URL').val();
		switch (name) {
		   case "primary-filter":
				return appurl+"/api/v1/primary-filter";
			break;
		   case "secondary-filter":
				 return appurl+"/api/v1/secondary-filter";
			break;
		  }
	 } 
}

 