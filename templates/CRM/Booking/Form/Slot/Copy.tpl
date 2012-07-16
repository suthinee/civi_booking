{*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.1                                                |
 +--------------------------------------------------------------------+
 | Copyright Compucorp Ltd. (c) 2004-2011                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*}
<div class="crm-form-block">
    <table class="form-layout-compressed">
        <tr class="crm-slot-form-block-week-commitment" >
            <td class="label"><label>Week commitment:</label></td>
            <td class="view-value">{$startDate} - {$endDate}  
            <span id="sDate" style="display:none;">{$sDate}</span> 
            </td>
        </tr>
         <tr class="crm-slot-form-block-num-of-week" >
            <td class="label">{$form.weeks.label}</td>
            <td class="view-value">{$form.weeks.html}       
            </td>
        </tr> 
        <tr class="crm-slot-form-block-submit" >
            <td class="label"></td>
            <td class="view-value"> {include file="CRM/common/formButtons.tpl" } 
              <div class='loading' style="position:relative; top:0px; left:100px"></div>
            </td>
        </tr> 
    </table>
    {$form.proceed.html}     
</div>
<div id="dialog">
 <div>
    <h2>Uncreatable slots</h2></div>
    <table id="slots">
        <thead>
            <tr>
                <th>Slot Date</th>
                <th>Clinician</th>
                <th>Attended Clinician</th>
                <th>Start time</th>
                <th>End time</th>
                <th>Room no</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>
{literal}
<script type="text/javascript">
    var crmajaxURL = '{/literal}{php} print base_path(); {/php}{literal}civicrm/ajax/rest';

    cj(document).ready(function() {
        cj('#proceed').attr('style', 'display: none');
        cj('#slots').dataTable({
            "bPaginate": false,
            "bLengthChange": false,
            "bFilter": false,
            "bSort": true,
            "bInfo": false,
            "bAutoWidth": false
        });

        //cj('.crm-button_qf_Copy_submit').append("<div class='loading'></div>");


       cj('#_qf_Copy_submit').click(function(e){
          e.preventDefault();
          cj("#_qf_Copy_submit").prop('value', 'Please Wait....').prop('disabled', true); 
          cj(".loading").show();
          var sd = jQuery.trim(cj('#sDate').text());
          var weeks =  cj('select[name="weeks"]').val(); 
          cj().crmAPI ('slot','copy',{'version' :'3', 
                                          'sequential' :'1',
                                          'sd' : sd,
                                          'weeks' : weeks,
                                          'proceed' : 0
                                          },{
                             ajaxURL: crmajaxURL,
                             success:function (data){ 
                             //Button label back
                             cj("#_qf_Copy_submit").prop('value', 'Copy slots').prop('disabled', false); ; 
                             cj(".loading").hide(); //hide indicator

                             if(data.count > 0){
                                var oTable = cj('#slots').dataTable();
                                oTable.fnClearTable();
                                for(var index in data.values){
                                  var slotStatus = 'Available';
                                  if( data.values[index].status == '2'){
                                    slotStatus = 'Appointment';
                                  }
                                  oTable.fnAddData( [
                                        data.values[index].slot_date,
                                        data.values[index].clinician_1,
                                        data.values[index].clinician_2,
                                        data.values[index].start_time,
                                        data.values[index].end_time,
                                        data.values[index].room_no,
                                        slotStatus
                                        ]); 
                                }
                                cj( "#dialog" ).dialog('open');  
                              }else{
                                //sumit but set proceed to 0 to avoid duplicate query
                                cj('#proceed').val(0);
                                alert('done');
                               // cj('#Copy').submit();
                              }
                            }
          });
        }); 



        cj( "#dialog" ).dialog({ 
          autoOpen: false,
          resizable: false,
          draggable: false,
          width:800,
          height:400,
          modal: true,
          buttons : {      
          Proceed : function() {
            cj('#proceed').val(1);
            cj('#Copy').submit();
          },
          Cancel : function() {
            cj(this).dialog("close");
          }
        }       
        });
     });


</script>
{/literal}

