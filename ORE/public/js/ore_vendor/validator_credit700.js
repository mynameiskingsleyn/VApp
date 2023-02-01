$("input[name='credit700_cell']").keyup(function() {
            $(this).val($(this).val().replace(/^(\d{3})(\d{3})(\d)+$/, "($1) $2-$3"));
            }); 
	        $("input[name='credit700_home']").keyup(function() {
            $(this).val($(this).val().replace(/^(\d{3})(\d{3})(\d)+$/, "($1) $2-$3"));
            }); 		
			
            $(document).ready(function() {
				
				
            $('#credit700_firstname').keypress(function (e) {
             var regex = new RegExp(/^[a-zA-Z\s]+$/);
             var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
             if (regex.test(str)) {
             return true;
            }
            else {
             e.preventDefault();
             return false;
            }
            });
                 
            $('#credit700_lastname').keypress(function (e) {
             var regex = new RegExp(/^[a-zA-Z\s]+$/);
             var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
             if (regex.test(str)) {
             return true;
            }
            else {
             e.preventDefault();
             return false;
            }
            });
			
            $('#credit700_mname').keypress(function (e) {
             var regex = new RegExp(/^[a-zA-Z\s]+$/);
             var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
             if (regex.test(str)) {
             return true;
            }
            else {
             e.preventDefault();
             return false;
            }
            });
			
            $('#credit700_zip').keypress(function (e) {
             var regex = new RegExp(/^-?\d+\.?\d*$/);
             var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
             if (regex.test(str)) {
             return true;
            }
            else {
             e.preventDefault();
             return false;
            }
            });
            
            $('#credit700_cell').keypress(function (e) {
             var regex = new RegExp(/^-?\d+\.?\d*$/);
             var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
             if (regex.test(str)) {
             return true;
            }
            else {
             e.preventDefault();
             return false;
            }
            });
            
			$('#credit700_home').keypress(function (e) {
             var regex = new RegExp(/^-?\d+\.?\d*$/);
             var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
             if (regex.test(str)) {
             return true;
            }
            else {
             e.preventDefault();
             return false;
            }
            });
				 
            
            $('#credit-form').validate({  
             onfocusout: function(element) {  
             this.element(element);  
            },
            onkeyup: function(element) {     
             this.element(element); 
            },
            rules: {
             credit700_firstname: {
                 required: true
             },
             credit700_lastname: {
                 required: true
             },
             credit700_address: {
                 required: true
             },
             credit700_zip: {
                 required: true,
                 minlength: 5,
                 maxlength: 5
                 
             },
             credit700_city: {
                 required: true
             },
             credit700_state: {
                 required: true
             },
             credit700_cell: {
                 required: true
             },
             email_User: {
                 required: true,
                 email: true
             },
             checkbox: {
                 required: true
             }
            },
               

            messages: {
             credit700_firstname: "Enter your First Name",
             credit700_lastname: "Enter your Last Name",
             credit700_address: "Enter your Address",
             credit700_zip: {
                 required: "Please enter Zip"
             },
             credit700_city: "Please enter your City",
             credit700_state: "Please enter your State",
             credit700_cell: "Please enter your Cell Number",
             email_User: {
                 required: "Please enter your Email",
                 email: "Enter a valid Email"
             },
                checkbox: {
                 required: ""
            }
            },
			
            submitHandler : function(){
             $("#modal-container").fadeIn(500).css("visibility", "visible");
            }
                
            });
                  
            $('#errorMessageOne').hide();  
            $("#inputCheck").click(function(){
            $("#errorMessageOne").hide();
            });
            $('#credit-form').submit(function(e) {
             e.preventDefault();
            
            
            $(".error2").remove();
            
            if (!($('#inputCheck').prop('checked'))) {
             $('#errorMessageOne').show();
            }
            
            });
             
                  
            $("#inputEmail").keypress(function(){
              $("#inputEmail-error").hide();
            });
                  
             $('#inputEmail').attr('autocomplete', 'off');
                  
            
            
            });
            
            $("#credit-form").submit(function(e){
             e.preventDefault();
             $.ajax({
               type : 'POST',
               data: $("#credit-form").serialize(),
               url : 'url',
               success : function(data){
                 $("#exampleModalCenter").modal("show");
               }
             });
             return false;
            });
             
            $('input[name="ssnNumber"]').keypress(function (e) {
                var regex = new RegExp(/^-?\d+\.?\d*$/);
                var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
                if (regex.test(str)) {
                 return true;
                }
                else {
                 e.preventDefault();
                 return false;
                }
                });
            $("input[name='ssnNumber']").keyup(function() {
                  $(this).val($(this).val().replace(/^(\d{3})(\d{2})(\d)+$/, "$1-$2-$3"));
                }); 
            $(document).ready(function() { 
                $('#almostThereForm').validate({  
                rules: {
                 ssnNumber: {
                     required: true,
                     minlength: 11,
                     maxlength: 11
                 }
                },
                messages: {
                    ssnNumber: {
                     required: "Please enter a valid SSN",
                     minlength: "Please enter 9 characters",
                     maxlength: "Please enter 9 characters"
                    }
                },
                submitHandler: function(form) { 
                        $("#modal-container").fadeOut(500).css("visibility", "hidden");
                        $("#main-form-container").fadeOut(500).css("visibility", "hidden");
                        $("#thankYou-container-out").fadeIn(500).css("visibility", "visible");
                    } 
                });
                $("#noThankYou").click(function(){
                        $("#modal-container").fadeOut(500).css("visibility", "hidden");
                        $("#main-form-container").fadeOut(500).css("visibility", "hidden");
                        $("#thankYou-container-out").fadeIn(500).css("visibility", "visible");
            });
            }); 