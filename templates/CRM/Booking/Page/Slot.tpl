{* this template is used for displaying slot calendar *}
<div id="content">
<div>
	<div class="schedule_title">
	</div>
	<div class="schedule_dates">
		{$startDate} - {$endDate}
	</div>
</div>

<div style="text-align: center; margin: auto;">
<div class="legend reservable">Reservable</div>
<div class="legend unreservable">Unreservable</div>
<div class="legend reserved">Reserved</div>
<!-- <div class="legend reserved mine">My Reservation</div>-->
<!-- <div class="legend reserved pending">Pending</div> -->
<div class="legend pasttime">Past</div>
<!-- <div class="legend restricted">Restricted</div> -->
</div>

<div style="height:10px">&nbsp;</div>


{foreach from=$daysOfNextweek key=dayKey item=day}
<table id={$dayKey} class="reservations" border="0" cellpadding="0" width="100%">
			<tbody><tr>
			<td class="resdate">{$day}</td>
				{$timeRange}
				{foreach from=$timeOptions item=time}
					<td class="reslabel">{$time}</td>
				{/foreach}				
			</tr>
			{foreach from=$rooms item=room}
				{foreach from=$room key=k item=v}
					{if $k eq 'room_no'}
						<tr class="slots">
							<td class="resourcename">{$v}</td>
							{foreach from=$timeOptions key=timeKey item=time}
								{assign var=dayAndRoom value="$dayKey"|cat:$v}
    							{assign var=classId value="$dayAndRoom"|cat:$timeKey}
					        	{if in_array($classId, $reservedSlots)} 
					        		<td colspan="1" class="reserved slot {$classId}">
					        	{else}
						        	<td colspan="1" class="reservable slot {$classId}">
					        	{/if}
					        		<div class='time hide'>{$timeKey}</div>
							       	<div class='roomNo hide'>{$v}</div>
							       	<div class='roomId hide'></div>
							       	<div class='date hide'>{$day}</div>
							       	<div class='unixDate hide'>{$dayKey}</div>
					        	</td>
							{/foreach}								
			   			</tr>
					{/if}  
				{/foreach}
			{/foreach}
						
	</tbody>
</table>
{/foreach}
</div>
{literal}

<script type="text/javascript">
	var crmajaxURL = '{/literal}{php} print base_path(); {/php}{literal}civicrm/ajax/rest';
  	var startTime = null;
    var unixDate = null;
    var roomNo = null;
	cj(function() {
		cj("td.slot").live('click', function(){
			if(cj(this).hasClass('reservable')){
	        	startTime = cj(this).find('div.time').text();
	        	date = cj(this).find('div.date').text();
	        	unixDate = cj(this).find('div.unixDate').text();
	        	roomNo = cj(this).find('div.roomNo').text();
	        	//roomId = cj(this).find('div.roomId').text();

	        	cj('#dateHolder').text(date);
	        	cj('#roomNo').text(roomNo);
				cj('#startSelect option[value=' +startTime+ ']').attr('selected', 'selected');
				//cj('#startSelect option[value=' +startTime+ ']').attr('selected', 'selected');

	        	cj( "#slotDialog" ).data('obj', cj(this))
	        					   .dialog('open');   
        	}
		}).hover(function(){
			if(cj(this).hasClass('reservable')){
   				cj(this).css("background","#40d288");
   			}
    	},function(){
			if(cj(this).hasClass('reservable')){
			    cj(this).css("background","#ffffff");
			}
		});
		
		cj( "#slotDialog" ).dialog({
			    autoOpen: false,
			    resizable: false,
			    draggable: false,
			    height:450,
   			    width:300,
			    modal: true,
			    buttons: {
			    	'Create a slot': function() {
			    		var contactId = cj('select[name="counsellor"]').val();    // get the value from a dropdown select
			    		var startTime = cj('select[name="startSelect"]').val(); 
			    		var endTime = cj('select[name="endSelect"]').val(); 
			    		var sessionService = cj('select[name="sessionService"]').val(); 
			    		var activityType = cj('select[name="activityType"]').val(); 
 		
			    	    cj().crmAPI ('slot','create',{'version' :'3', 
			    	    							  'sequential' :'1',
			    	    							  'contact_id' : contactId, 
			    	    							  'date' : unixDate, 
			    	    							  'start_time' : startTime, 
			    	    							  'end_time' : endTime, 
			    	    							  'session_service' :sessionService, 
			    	    							  'room_no' : roomNo, 
			    	    							  'activity_type' : activityType},{
				           ajaxURL: crmajaxURL,
				           success:function (data){ 
				            if(data.count != 0){
				              cj.each(data.values, function(key, value) {
				           		
					    		var slotId = value.slot_id;
						        var selectedStartTime = value.start_time;
						        var selectedEndTime = value.end_time;
						        var selectedDate = value.slot_date;
						        var selectedRoom = value.room_no;
	        
						        var timeRange = value.time_range;
						        
						        for(time in timeRange){
						        	cj('.' + selectedDate + selectedRoom+ timeRange[time]).removeClass("reservable").addClass("reserved");
		    				    }
    				            cj("#slotDialog").dialog('close');
    				            return;
				              });
				            }  
				          }
				        });
        			},
			        Cancel: function() {
			            cj(this).dialog('close');
			        }			        
			    }});

		});
</script>

{/literal}

<div id="slotDialog" title="Create a slot" class="ui-dialog-content ui-widget-content">
	<form>
			<ul>
				<li>
					<span class="label_text">Date: </span>
					<span id="dateHolder"></span> 
				</li>
				<li>
					<span class="label_text">Room: </span>
					<span id="roomNo"></span> 
					<span id="roomId"class="hide"></span> 
				</li>
				<li>
					<label for="startSelect" >Start Time: </label>
					<select id="startSelect"  disabled="disabled" name="startSelect">
						<option value="">Select Start time</option>
						{foreach from=$timeOptions key=k item=time}
							<option value="{$k}">{$time}</option>
						{/foreach}					
					</select>
				</li>
				<li>
					<label for="endSelect">End Time: </label>
					<select id="endSelect" name="endSelect">
						<option value="">Select End time</option>
						{foreach from=$timeOptions key=k item=time}
							<option value="{$k}">{$time}</option>
						{/foreach}					
					</select>
				</li>
				<li>
					<label for="activityType">Activity type: </label>
					<select id="activitySelect" name="activityType">
						{foreach from=$activityTypes key=k item=activityType}
							<option value="{$k}">{$activityType}</option>
						{/foreach}		
					</select>
				</li>
				<li>
					<label for="sessionService">Session service: </label>
					<select id="sessionSelect" name="sessionService">
						{foreach from=$sessionServices key=k item=sessionService}
							<option value="{$k}">{$sessionService}</option>
						{/foreach}		
					</select>
				</li>
				
				<li>
					<label for="counsellor">Counsellor: </label>
					<select id="counsellor" name="counsellor">
						<option value="">Select Counsellor</option>
						{foreach from=$contacts key=k item=contact}
							<option value="{$k}">{$contact}</option>
						{/foreach}		
					</select>
				</li>				
			</ul>
		</form>
</div>
