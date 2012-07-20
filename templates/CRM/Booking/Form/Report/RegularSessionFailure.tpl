<div class="crm-form-block">
<table class="form-layout-compressed">
    <tr class="crm-room-form-block-room_no" >
        <td class="label">{$form.log_date.label}</td>
        <td class="view-value">{include file="CRM/common/jcalendar.tpl" elementName=log_date}             
        </td>
    </tr>
     <tr class="crm-room-form-block-status" >
        <td class="label">{$form.status.label}</td>
        <td class="view-value">{include file="CRM/common/formButtons.tpl" } </td>
    </tr>
</table>
</div>
{if isset($logs)}
	{if $logs|@count > 0}
	<div class="messages">
	        {ts}The sessions listed below were unable to be automatically rolled forward by the system. This is most likely because there was not a suitable slot for these sessions available. Please review these in turn to confirm that no appointment is required or to reschedule the appointment.{/ts}
	</div>
	<table id="logs">
	    <thead>
	        <tr>
	            <th>Case Id</th>
	            <th>Activity Id</th>
	            <th>Activity Date Time</th>
	            <th>Client</th>
	            <th>Reason</th>
	            <th></th>
	            
	        </tr>
	    </thead>
	    <tbody>
	    	{foreach from=$logs key=key item=log}
	        <tr>
	  			<td>{$log.case_id}</td>
	            <td>{$log.entity_id}</td>
	            <td>{$log.activity_date_time}</td>
	            <td>{$log.display_name}</td>
	            <td>{$log.data}</td>
	            <td><a href="{php} print base_path(); {/php}civicrm/contact/view/case?reset=1&id={$log.case_id}&cid={$log.contact_id}&action=view&context=dashboard&selectedChild=case">View Case</a></td>
	        </tr>
	        {/foreach}	
	    </tbody>
	</table>
	{else}
	<div class="messages status">
	    <div class="icon inform-icon"></div>  &nbsp;
	        {ts}No failure log found.{/ts}
	</div>
	{/if}
{/if}
{literal}
<script type="text/javascript">

	cj(document).ready(function() {
    	cj('#logs').dataTable({
    		"bPaginate": false,
	        "bLengthChange": false,
	        "bFilter": true,
	        "bSort": true,
	        "bInfo": false,
	        "bAutoWidth": false
    	});

     
	
	});

</script>
{/literal}
