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
    <tr class="crm-room-form-block-room_no" >
        <td class="label">{$form.room_no.label}</td>
        <td class="view-value">{$form.room_no.html}            
        </td>
    </tr>
     <tr class="crm-room-form-block-type" >
        <td class="label">{$form.type.label}</td>
        <td class="view-value">{$form.type.html}            
        </td>
    </tr> 
    <tr class="crm-room-form-block-size" >
        <td class="label">{$form.size.label}</td>
        <td class="view-value">{$form.size.html}            
        </td>
    </tr>
    <tr class="crm-room-form-block-floor" >
        <td class="label">{$form.floor.label}</td>
        <td class="view-value">{$form.floor.html}            
        </td>
    </tr>
    <tr class="crm-room-form-block-centre" >
        <td class="label">{$form.centre.label}</td>
        <td class="view-value">{$form.centre.html}            
        </td>
    </tr>  
     <tr class="crm-room-form-block-extension" >
        <td class="label">{$form.extension.label}</td>
        <td class="view-value">{$form.extension.html}            
    </td>
     <tr class="crm-room-form-block-status" >
        <td class="label">{$form.status.label}</td>
        <td class="view-value">{$form.status.html}            
    </td>
    </tr>
</table>
{include file="CRM/common/formButtons.tpl" }x
</div>
{literal}
<script type="text/javascript"> 
    
</script>
{/literal}
