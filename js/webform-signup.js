
(function ($) {
	$(document).ready(function () { 
		/*
		if($("#edit-submitted-select-a-slot").length > 0){
			$("#edit-submitted-select-a-slot").attr("disabled", "disabled");
		}
		*/

		$("#edit-submitted-select-a-slot").click(function() {
			var service = $('select[name="submitted[disabled_1_activity_1_cg7_custom_15]"]').val(); 
			var centre = $('select[name="submitted[civicrm_1_case_1_cg6_custom_9]"]').val(); 
			if(service == '' && centre == ''){
				alert('Please select service and prefered centre');
			}else if(service == ''){
				alert('Please select service');
			}else if(centre == ''){
				alert('Please select centre');
			}else{
				//var basePath = window.location.hostname + '/';
				var url = 'apis/slot/get/1/2/4/4'
				$.getJSON(url, function(data){
					alert(data);
				});
			}
		});

		//var basePath = window.location.hostname + '/';
		var url = 'apis/slot/get/1/2/4/4'
		$.getJSON(url, function(data){
			console.log(data);
		});
		/*
		$.getJSON("http://erawat-virtualbox/tccr/sites/all/modules/civicrm/extern/rest.php?fnName=civicrm/contact/search&json=1&login&name=admin&pass=tccrAdm1n&api_key=2e554f49c9fc5c47548da4b24da64681&key=beb16573216b3d7898c933b590fcffdc",
        function(data){
        	alert("success");
        });
		*/

	
	



	});
})(jQuery);