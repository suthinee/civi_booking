
(function ($) {
	$(document).ready(function () { 
		
		if($("#edit-submitted-select-a-slot").length > 0){
			$("#edit-submitted-select-a-slot").attr("disabled", "disabled");
		}


		$('#edit-submitted-civicrm-1-activity-1-cg7-custom-15').change(function(){
			clearSlot();
			var centre = $('select[name="submitted[civicrm_1_case_1_cg6_custom_9]"]').val(); 
			if(centre != '' && $(this).val != ''){
				generateAvailableSlots();
				$('#edit-submitted-select-a-slot').removeAttr('disabled');
			}else{
				$("#edit-submitted-select-a-slot").attr("disabled", "disabled");
			}	
		});

		$('#edit-submitted-civicrm-1-case-1-cg6-custom-9').change(function(){
			clearSlot();
			var service = $('select[name="submitted[civicrm_1_activity_1_cg7_custom_15]"]').val(); 
			if(service != '' && $(this).val != ''){
				generateAvailableSlots();
				$('#edit-submitted-select-a-slot').removeAttr('disabled');
			}else{
				$("#edit-submitted-select-a-slot").attr("disabled", "disabled");
				
			}	
		});

	function clearSlot(){
		$("#edit-submitted-select-a-slot").attr("disabled", "disabled");
		var options = '<option value="" selected="selected">- Select -</option>';
		options += '<option value="0">Unable to find a slot, Request a call back</option>';
		$("#edit-submitted-select-a-slot").html(options);
	}	
	
		
	function generateAvailableSlots(){
		var centre = $('select[name="submitted[civicrm_1_case_1_cg6_custom_9]"]').val(); 
		var service = $('select[name="submitted[civicrm_1_activity_1_cg7_custom_15]"]').val(); 
		var basePath = Drupal.settings.basePath;
		var url = basePath + "slot/get/" + service + "/" + centre;
		var slotList = $('#edit-submitted-select-a-slot');
		$.getJSON(url, function(data){
			if(data.is_error == 0){
				var slots = data.results;
				for(var slotId in slots){
					optionVal = '<option value="'+ slotId +'">'+ slots[slotId].slot_day + ' - ' + slots[slotId].slot_date + ', ' + slots[slotId].start_time + ' - ' + slots[slotId].end_time + '</option>';
					slotList.append(optionVal);
				}
			}
		});

	}		
		
	});
})(jQuery);

