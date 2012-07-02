
(function ($) {
	$(document).ready(function () { 
		
		if($("#edit-submitted-select-a-slot").length > 0){
			$("#edit-submitted-select-a-slot").attr("disabled", "disabled");
		}
		
		/*
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
		*/

		$('#edit-submitted-civicrm-1-activity-1-cg7-custom-15').change(function(){
			var centre = $('select[name="submitted[civicrm_1_case_1_cg6_custom_9]"]').val(); 
			if(centre != '' && $(this).val != ''){
				generateAvailableSlots();
				$('#edit-submitted-select-a-slot').removeAttr('disabled');
			}else{
				$("#edit-submitted-select-a-slot").attr("disabled", "disabled");
			}	
		});

		$('#edit-submitted-civicrm-1-case-1-cg6-custom-9').change(function(){
			var service = $('select[name="submitted[civicrm_1_activity_1_cg7_custom_15]"]').val(); 
			if(service != '' && $(this).val != ''){
				generateAvailableSlots();
				$('#edit-submitted-select-a-slot').removeAttr('disabled');
			}else{
				$("#edit-submitted-select-a-slot").attr("disabled", "disabled");
				
			}	
		});

		
	function generateAvailableSlots(){
		var centre = $('select[name="submitted[civicrm_1_case_1_cg6_custom_9]"]').val(); 
		var service = $('select[name="submitted[civicrm_1_activity_1_cg7_custom_15]"]').val(); 
		var basePath = window.location.hostname + '/';
		console.log(basePath);
		var url = "/tccr/apis/slot/get/" + centre + "/" + service;
		console.log(url);
		$.getJSON(url, function(data){
				console.log(data);
		});

	}

		//var basePath = window.location.hostname + '/';
		
		

	});
})(jQuery);

