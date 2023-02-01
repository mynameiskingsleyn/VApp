 (function(d, s, id) {
 var js, cnjs = d.getElementsByTagName('head')[0]; 
 if (d.getElementById(id)) return;
 js = d.createElement(s); js.id = id;
 js.src = "https://app.carnow.com/dealers/carnow_plugin.js?key=2EwJG1ujFvevJJ87xkjBakeJ526oJxh4rtuyEAnbgscJaxws";
 cnjs.appendChild(js, cnjs); 
 
     js.onload = function () {
        CarNowPlugin.init();
        
        var cn_first = $('#cn_first').val();
        var cn_last = $('#cn_last').val();
        var cn_contact_email= $('#cn_contact_email').val();
        var cn_contact_phone= $('#cn_contact_phone').val();
                
       // if(cn_first!='' && cn_contact_email!=''){
                CarNowPlugin.setupPartnerParams({
                      key: "vHxh3kGxIkse8woo7oo70d6g4ynHbsLl",
                      department: 2184,
                      pt: "fcaore",
                      firstName: cn_first,
                      lastName: cn_last,
                      email: cn_contact_email,
                      phone: cn_contact_phone
                });
        //}

        
        CarNowPlugin.addListener(function(data) {
            if (data.event == "start_session") {
                var sessionId = data.id; 
                var vin = $('#vin').val();
                console.log(vin);
                console.log(sessionId);
                    if(!localCache.exist(vin+':'+sessionId)){ 
                        vehiclepage.invChat(sessionId, 'inventory',vin );
                    }

 
                // save this session ID on your server, if you need to make any server side calls.
            }
        });
    };

}(document, 'script', 'com-carnow-plugin'));  