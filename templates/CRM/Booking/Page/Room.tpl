{* *}
<table id="rooms">
    <thead>
        <tr>
            <th>Room No</th>
            <th>Room Type</th>
            <th>Room Size</th>
            <th>Floor</th>
            <th>Building</th>
            <th>Active?</th>
            <td></th>
        </tr>
    </thead>
    <tbody>
    	{foreach from=$rooms key=key item=room}
        <tr>
  			<td>{$room.room_no}</td>
            <td>{$room.type}</td>
            <td>{$room.size}</td>
            <td>{$room.floor}</td>
            <td>{$room.building}</td>
            <td>{$room.is_active}</td>
            <td><a href="/tccr/civicrm/booking/room/edit/?id={$room.id}&reset=1">Edit</a></td>
        </tr>
        {/foreach}	
    </tbody>
</table>
{literal}
<script type="text/javascript">
	var crmajaxURL = '{/literal}{php} print base_path(); {/php}{literal}civicrm/ajax/rest';

	cj(document).ready(function() {
    	cj('#rooms').dataTable({
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
