function createForm(ms){
	clearSlot();
	jQuery(".loading").show();
	var centre = jQuery('select[name="submitted[civicrm_1_case_1_cg6_custom_9]"]').val(); 
	var service = jQuery('select[name="submitted[civicrm_1_activity_1_cg7_custom_15]"]').val(); 
	var basePath = Drupal.settings.basePath;
	var url = basePath + "slot/get/" + service + "/" + centre;
	var now = new Date();
	if(ms != undefined) url += "/" + ms;
	var component = jQuery('#webform-component-seleted-slot-id');
	component.append("<div class='slot'></div>");
	component = jQuery('#webform-component-seleted-slot-id .slot');
	if(centre != '' && service != ''){
		jQuery.getJSON(url, function(data){
			var check_unable = false;
			if(data.is_error == 0){
				var slots = data.results;
				var start = new Date((data.lastWeek+604800)*1000);//1209600
				var end = new Date((data.nextWeek-86400)*1000);
				var show = start.getDayName() + " " + start.getUTCDate() + " " + start.getMonthName() + " - " + end.getDayName() + " " + end.getUTCDate() + " " + end.getMonthName() + " " + end.getUTCFullYear();
				component.append("<div class='change-week'><span class='pre' onclick='createForm("+data.lastWeek+");'><</span><span class='show'>"+show+"</span><span class='next' onclick='createForm("+data.nextWeek+");'>></span></div>");
				// component.append("<div class='options'>Show booked <input type='radio' name='mark' class='show' CHECKED> | Hide booked <input type='radio' name='mark' class='hide' ></div>");
				
				if(ms >= now.getTime()){
					component.append("<ul class='group'><li class='head item'><div class='first left'>Date</div><div class='middle left'>Time</div><div class='last left'>Select</div></li></ul>");
				}else{
					component.append("<ul class='group'><li class='head item'><div class='first left'>Date</div><div class='middle left'>Time</div></li></ul>");
				}
				var mark = false;
				for(var slotId in slots){
					mark = true;
					var temp = "<li class='item'>";
					temp += "<div class='first left'>" + slots[slotId].slot_day + "<br/>" + slots[slotId].slot_date  + "</div>";
					temp += "<div class='middle left'>" + slots[slotId].start_time + " - " + slots[slotId].end_time  + "</div>";
					if(ms >= now.getTime()){
						temp += "<div class='last left'><input type='radio' name='group' value='"+slotId+"'></div>";
					}
					temp += "</li>";
					jQuery("ul",component).append(temp);
				}
				jQuery('#webform-component-seleted-slot-id').slideDown('555', function() {
			    // Animation complete.
			    jQuery(".loading").hide();
				});
				if(mark == true){
					bindRadio();
				}else{
					check_unable = true;
					// Unable to find a slot? Request a call back
					jQuery(".head",component).remove();
					jQuery("ul.group",component).append('<li class="item">Unable to find a slot? Request a call back <input id="unable-callback" type="checkbox" onclick="setUnableSlot(0);" /></li>');
				}
			}
			if(ms < now.getTime() && check_unable == false){
				jQuery("ul.group",component).append('<li class="item">Unable to find a slot? Request a call back <input id="unable-callback" type="checkbox" onclick="setUnableSlot(0);" /></li>');
			}
		});
	}
}

function setUnableSlot(value){
	if(!jQuery("#unable-callback").is(':checked')){
		jQuery("#edit-submitted-seleted-slot-id").val('');
	}else{
		jQuery("#edit-submitted-seleted-slot-id").val(value);
	}
	
}

function clearSlot(){
	jQuery("#edit-submitted-seleted-slot-id").val('');
	jQuery("#webform-component-seleted-slot-id").slideUp('slow');
	jQuery("#webform-component-seleted-slot-id .group").remove();
	jQuery("#webform-component-seleted-slot-id .slot").remove();
}	

function bindRadio(){
	jQuery("#webform-component-seleted-slot-id ul.group input[type=radio]").each(function(key, item){
		jQuery(this).click(function(){
			jQuery("#edit-submitted-seleted-slot-id").val(jQuery(this).val());
		});
	});
}		

(function ($) {
	$(document).ready(function () { 

		$("#webform-component-civicrm-1-case-1-cg6-custom-9").append("<div class='loading'></div>");

		$('#edit-submitted-civicrm-1-activity-1-cg7-custom-15').change(function(){
			var centre = $('select[name="submitted[civicrm_1_case_1_cg6_custom_9]"]').val(); 
			if(centre != '' && $(this).val != ''){
				createForm();
			}
		});

		$('#edit-submitted-civicrm-1-case-1-cg6-custom-9').change(function(){
			var service = $('select[name="submitted[civicrm_1_activity_1_cg7_custom_15]"]').val(); 
			if(service != '' && $(this).val != ''){
				createForm();
			}
		});

		if($('#edit-submitted-civicrm-1-case-1-cg6-custom-9').val() != 0 && $('#edit-submitted-civicrm-1-activity-1-cg7-custom-15').val() != 0){
			createForm();
		}

	});
})(jQuery);

