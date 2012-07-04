
(function ($) {
	$(document).ready(function () { 
		/*
		if($("#edit-submitted-select-a-slot").length > 0){
			$("#edit-submitted-select-a-slot").attr("disabled", "disabled");
		}*/

		$('#edit-submitted-civicrm-1-activity-1-cg7-custom-15').change(function(){
			clearSlot();
			var centre = $('select[name="submitted[civicrm_1_case_1_cg6_custom_9]"]').val(); 
			if(centre != '' && $(this).val != ''){
				generateAvailableSlots();
				$('#edit-submitted-seleted-slot-id').removeAttr('disabled');
			}else{
				$('#edit-submitted-seleted-slot-id').attr("disabled", "disabled");
			}	
		});

		$('#edit-submitted-civicrm-1-case-1-cg6-custom-9').change(function(){
			clearSlot();
			var service = $('select[name="submitted[civicrm_1_activity_1_cg7_custom_15]"]').val(); 
			if(service != '' && $(this).val != ''){
				generateAvailableSlots();
				$('#edit-submitted-seleted-slot-id').removeAttr('disabled');
			}else{
				$('#edit-submitted-seleted-slot-id').attr("disabled", "disabled");
				
			}
			//$("#edit-submitted-select-a-slot").attr("disabled", "disabled");
		});
		/*
		$('#edit-submitted-select-a-slot').change(function(){
			var selectedSlot = $('select[name="submitted[select_a_slot]"]').val(); 
			//$('select[name="submitted[slot_id]"]').val(slotId); 
			//var slotId = $('select[name="submitted[slot_id]"]').val(); 
		}); */

		var selected;

		if($('label[for="edit-submitted-seleted-slot-id"]').length){
			if($('#edit-submitted-seleted-slot-id').length){
				selected = $('#edit-submitted-seleted-slot-id').val();
				$('#edit-submitted-seleted-slot-id').remove();
				var slotList = '<select id="edit-submitted-seleted-slot-id" name="submitted[seleted_slot_id]" class="form-select"><option value="" selected="selected">- Select -</option>';
				slotList += '<option value="0">Unable to find a slot, Request a call back</option></select>';
				$('label[for="edit-submitted-seleted-slot-id"]').after(slotList);
				$('#edit-submitted-seleted-slot-id').attr("disabled", "disabled");
			}
		};

		if($('#edit-submitted-civicrm-1-case-1-cg6-custom-9').val() != 0 && $('#edit-submitted-civicrm-1-activity-1-cg7-custom-15').val() != 0){
			generateAvailableSlots();
		}


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
			var component = $('#webform-component-seleted-slot-id');
			var list = $('#edit-submitted-seleted-slot-id');
			$.getJSON(url, function(data){
				if(data.is_error == 0){
					var slots = data.results;
					for(var slotId in slots){
						if(selected == slotId){
							optionVal = '<option value="'+ slotId +'" selected="selected">'+ slots[slotId].slot_day + ' - ' + slots[slotId].slot_date + ', ' + slots[slotId].start_time + ' - ' + slots[slotId].end_time + '</option>';
						}else{
							optionVal = '<option value="'+ slotId +'">'+ slots[slotId].slot_day + ' - ' + slots[slotId].slot_date + ', ' + slots[slotId].start_time + ' - ' + slots[slotId].end_time + '</option>';
						}
						list.append(optionVal);
					}
				}
			});
			list.removeAttr('disabled');
		}		
		
	});
})(jQuery);

