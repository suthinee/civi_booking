function createForm(ms){
	clearSlot();
	jQuery(".loading").show();
	var basePath = Drupal.settings.basePath;
	var url = basePath + "slots/get";
	var now = new Date();
	if(ms != undefined) url += "/" + ms;
	var component = jQuery('#webform-component-seleted-slot-id');
	component.append("<div class='slot'></div>");
	component = jQuery('#webform-component-seleted-slot-id .slot');
	jQuery.getJSON(url, function(data){
		if(data.is_error == 0){
			var slots = data.results;
			var start = new Date((data.lastWeek+604800)*1000);//1209600
			var end = new Date((data.nextWeek-86400)*1000);
			var show = "Monday " + (start.getUTCDate() +1) + " " + start.getMonthName() + " - " + " Saturday " + end.getUTCDate() + " " + end.getMonthName() + " " + end.getUTCFullYear();
			component.append("<div class='change-week'><span class='pre' onclick='createForm("+data.lastWeek+");'><</span><span class='show'>"+show+"</span><span class='next' onclick='createForm("+data.nextWeek+");'>></span></div>");
			component.append("<div class='options'>Show booked <input type='radio' name='mark' class='show' CHECKED> | Hide booked <input type='radio' name='mark' class='hide' ></div>");
			component.append("<ul class='group left'><li class='head item'><div class='first left'>Date</div><div class='middle left'>Time</div><div class='last left'>Select</div></li></ul>");
			component.append("<ul class='group right'><li class='head item'><div class='first left'>Date</div><div class='middle left'>Time</div><div class='last left'>Select</div></li></ul>");
			
			bindFilter();
			var mark = false;
			var markLeft = false;
			var markRight = false;
			for(var slotId in slots){
				mark = true;
				if(slots[slotId].slot_day == "Monday" || slots[slotId].slot_day == "Wednesday" || slots[slotId].slot_day == "Friday"){
					markLeft = true;
					booked = '';
					if(slots[slotId].status == 2){
						booked = "booked";
					}
					var tempLeft = "<li class='item "+ booked +"'>";
					tempLeft += "<div class='first left'>" + slots[slotId].slot_day + " " + slots[slotId].slot_date  + "</div>";
					tempLeft += "<div class='middle left'>" + slots[slotId].start_time + " - " + slots[slotId].end_time  + "</div>";
					if(ms == undefined){
						if(booked == "booked"){
							tempLeft += "<div class='last left book'><input type='radio' name='group' value='"+slotId+"' disabled></div>";
						}else{
							tempLeft += "<div class='last left nobook'><input type='radio' name='group' value='"+slotId+"'></div>";
						}
					}else{
						if((ms*1000) >= now.getTime()){ // Next
							if(booked == "booked"){
								tempLeft += "<div class='last left book'><input type='radio' name='group' value='"+slotId+"' disabled></div>";
							}else{
								tempLeft += "<div class='last left nobook'><input type='radio' name='group' value='"+slotId+"'></div>";
							}
						}else{ // Prev
							tempLeft += "<div class='last left book'><input type='radio' name='group' value='"+slotId+"' disabled></div>";
						}
					}
					tempLeft += "</li>";
					jQuery("ul.left",component).append(tempLeft);
				}
				if(slots[slotId].slot_day == "Tuesday" || slots[slotId].slot_day == "Thursday" || slots[slotId].slot_day == "Saturday"){
					markRight = true;
					booked = '';
					if(slots[slotId].status == 2){
						booked = "booked";
					}
					var tempRight = "<li class='item "+ booked +"'>";
					tempRight += "<div class='first left'>" + slots[slotId].slot_day + " " + slots[slotId].slot_date  + "</div>";
					tempRight += "<div class='middle left'>" + slots[slotId].start_time + " - " + slots[slotId].end_time  + "</div>";
					if(ms == undefined){
						if(booked == "booked"){
							tempRight += "<div class='last left book'><input type='radio' name='group' value='"+slotId+"' disabled></div>";
						}else{
							tempRight += "<div class='last left nobook'><input type='radio' name='group' value='"+slotId+"'></div>";
						}
					}else{
						if((ms*1000) >= now.getTime()){ // Next
							if(booked == "booked"){
								tempRight += "<div class='last left book'><input type='radio' name='group' value='"+slotId+"' disabled></div>";
							}else{
								tempRight += "<div class='last left nobook'><input type='radio' name='group' value='"+slotId+"'></div>";
							}
						}else{ // Prev
							tempRight += "<div class='last left book'><input type='radio' name='group' value='"+slotId+"' disabled></div>";
						}
					}
					tempRight += "</li>";
					jQuery("ul.right",component).append(tempRight);
				}
			}
			if(jQuery("#edit-submitted-seleted-slot-id").val() != ''){
				jQuery(".item input[name=group]").each(function(key,item){
					if(jQuery(item).val() == jQuery("#edit-submitted-seleted-slot-id").val()){
						jQuery(item).attr("CHECKED","CHECKED");
					}
				});
			}
			jQuery('#webform-component-seleted-slot-id').slideDown('555', function() {
		    // Animation complete.
		    jQuery(".loading").hide();
			});
			if(mark == true){
				bindRadio();
			}else{
				jQuery("div.options").hide();
				
			}
			if(markLeft == false){
				jQuery("ul.left li.head").hide();
			}
			if(markRight == false){
				jQuery("ul.right li.head").hide();
			}
			if(ms != undefined){
				if((ms*1000) >= now.getTime()){
					jQuery("ul.group.left",component).append('<li class="item">Unable to find a slot? Join waiting list <input id="unable-callback" type="checkbox" onclick="setUnableSlot1(0);" /></li>');
				}else{
					jQuery("ul.group.left",component).append('<li class="item">Unable to find a slot? Join waiting list <input id="unable-callback" type="checkbox" onclick="setUnableSlot(0);" /></li>');
				}
			} else{
				jQuery("ul.group.left",component).append('<li class="item">Unable to find a slot? Join waiting list <input id="unable-callback" type="checkbox" onclick="setUnableSlot1(0);" /></li>');
			}
		}
	});
}

function setUnableSlot(value){
	if(!jQuery("#unable-callback").is(':checked')){
		jQuery("#edit-submitted-seleted-slot-id").val('');
	}else{
		jQuery("#edit-submitted-seleted-slot-id").val(value);
	}
}
function setUnableSlot1(value){
	if(!jQuery("#unable-callback").is(':checked')){
		jQuery("#edit-submitted-seleted-slot-id").val('');
		jQuery(".item div.nobook input[type=radio]").attr("disabled","");
	}else{
		jQuery("#edit-submitted-seleted-slot-id").val(value);
		jQuery(".item div.nobook input[type=radio]").attr("disabled","disabled");
	}
}
function clearSlot(){
	if(jQuery("#edit-submitted-seleted-slot-id").val() == 0){
		jQuery("#edit-submitted-seleted-slot-id").val('');
	}
	jQuery("#webform-component-seleted-slot-id").slideUp('slow');
	jQuery("#webform-component-seleted-slot-id .group").remove();
	jQuery("#webform-component-seleted-slot-id .slot").remove();
}	
function bindFilter(){
	jQuery(".options .show").click(function(){
		jQuery(".item.booked").fadeIn(333);
	});
	jQuery(".options .hide").click(function(){
		jQuery(".item.booked").fadeOut(333);
	});
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
		createForm();
		$("#webform-component-civicrm-1-case-1-cg6-custom-9").append("<div class='loading'></div>");
	});
})(jQuery);

