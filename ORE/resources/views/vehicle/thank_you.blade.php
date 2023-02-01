<html>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
 
<body>
<script>
var e = jQuery.Event( "keydown", { keyCode: 87 } );
$("document").trigger(e, function() {
	alert();
});

var prevKey = ""
$(document).keydown(function (e) {//Enter and F5 button press
    if (e.keyCode == "116" || e.keyCode == "13") {
        window.onbeforeunload = null;
    }//Ctrl + w
    else if (e.ctrlKey == true && e.keyCode == "87") {//FOR CTRL + W
        alert("Ctrl + W");
        window.onbeforeunload = ConfirmLeave;
    }//(Ctrl or Alt) + F4
    else if ((e.ctrlKey == true || e.altKey == true) && e.keyCode == "115") {
        window.onbeforeunload = ConfirmLeave;
    }
});
</script>
<h3>Thanks you.</h3>
</body>
</html>