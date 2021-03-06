<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
?>
<a href="<?php echo $this->url_prefix;?>/index.php/customer">[ <?php echo lang('undochanges'); ?> ]</a>

<table cellpadding=0 border=0 cellspacing=0 width=720>
<td valign=top width=360>
<form action="<?php echo $this->url_prefix;?>/index.php/customer/save" method=post>
	<table cellpadding=5 cellspacing=1 border=0 width=360>

	<td bgcolor="#ccccdd" width=180><b><?php echo lang('signupdate');?></b></td>
	<td width=180 bgcolor="#ddddee"><?php echo $signup_date?></td><tr>

	<td bgcolor="#ccccdd"><b><?php echo lang('name');?></b></td>
	<td bgcolor="#ddddee"><input name="name" type=text value="<?php echo $name?>"></td><tr>

	<td bgcolor="#ccccdd"><b><?php echo lang('company');?></b></td>
	<td bgcolor="#ddddee"><input name="company" type=text value="<?php echo $company?>"></td><tr>

	<td bgcolor="#ccccdd"><b><?php echo lang('street');?></b></td>
	<td bgcolor="#ddddee"><input name="street" type=text value="<?php echo $street?>"></td><tr>

	<td bgcolor="#ccccdd"><b><?php echo lang('city');?></b></td>
	<td bgcolor="#ddddee"><input name="city" type=text value="<?php echo $city?>"></td><tr>

	<td bgcolor="#ccccdd"><b><?php echo lang('state');?></b></td>
	<td bgcolor="#ddddee"><input name="state" type=text value="<?php echo $state?>" size=3></td>	<tr>

	<td bgcolor="#ccccdd"><b><?php echo lang('zip');?></b></td>
	<td bgcolor="#ddddee"><input name="zip" size=5 type=text value="<?php echo $zip?>"></td><tr>

	<td bgcolor="#ccccdd"><b><?php echo lang('phone');?></b></td><td bgcolor="#ddddee">
	<input name="phone" type=text value="<?php echo $phone?>"></td><tr>

	<td bgcolor="#ccccdd"><b><?php echo lang('alt_phone');?></b></td>
	<td bgcolor="#ddddee"><input name="alt_phone" type=text value="<?php echo $alt_phone?>">
	</td><tr>

	<td bgcolor="#ccccdd"><b><?php echo lang('fax');?></b></td><td bgcolor="#ddddee">
	<input name="fax" type=text value="<?php echo $fax?>"></td><tr>

	<td bgcolor="#ccccdd"><b><?php echo lang('notes');?></b></td><td bgcolor="#ddddee">
	<input name="notes" type=text value="<?php echo $notes?>" colspan=2></td><tr>
	</table>

</td>
<td valign=top width=360>
	<table cellpadding=5 cellspacing=1 width=360>

	<td width=180 bgcolor="#ccccdd"><b><?php echo lang('billingstatus');?></b></td>
	<td width=180 bgcolor="#ffbbbb"></td><tr>
	<td width=180 bgcolor="#ccccdd"><b><?php echo lang('canceldate');?></b></td>
	<td width=180 bgcolor="#ddddee"><input name="cancel_date" value="<?php echo $cancel_date?>">	</td><tr>
 
	<td bgcolor="#ccccdd"><b><?php echo lang('source');?></b></td>
	<td bgcolor="#ddddee"><input name="source" type=text value="<?php echo $source?>"></td><tr>

	<td bgcolor="#ccccdd"><b><?php echo lang('contactemail');?></b></td>
	<td bgcolor="#ddddee">
	<input name="contact_email" type=text value="<?php echo $contactemail?>"></td><tr>

	<td bgcolor="#ccccdd"><b><?php echo lang('secret_question');?></b></td>
	<td bgcolor="#ddddee">
	<input name="secret_question" type=text value="<?php echo $secret_question?>"></td><tr>

	<td bgcolor="#ccccdd"><b><?php echo lang('secret_answer');?></b></td>
	<td bgcolor="#ddddee">
	<input name="secret_answer" type=text value="<?php echo $secret_answer?>"></td><tr>

	<td bgcolor="#ccccdd"><b><?php echo lang('defaultbillingid');?></b></td>
	<td bgcolor="#ddddee"><input type=hidden name=default_billing_id 
	value="<?php echo $default_billing_id?>"><?php echo $default_billing_id?></td><tr>

	<td bgcolor="#ccccdd"><b><?php echo lang('country');?></b></td>
	<td bgcolor="#ddddee"><input name="country" type=text value="<?php echo $country?>"></td><tr>

	<td width=180 bgcolor="#ccccdd"><b><?php echo lang('cancelreason');?></b></td>
	<td width=180 bgcolor="#ddddee">

	<select name="cancel_reason" style="font-size: 7pt;">
		<option value=""></option>
<?php
// print the current reason and
// print list of reasons to choose from
foreach ($cancelreasons as $myresult)
{
   $myid = $myresult['id'];
   $myreason = $myresult['reason'];
   if ($cancel_reason_id == $myid) 
   {
     echo "<option value=\"$myid\" selected>$myreason</option>";
   } 
   else 
   {
     echo "<option value=\"$myid\">$myreason</option>";
   }
}
?>
	</select></td><tr>
	</table>
</td>
<tr>

<td colspan=2>
<center>

<!-- include hidden fields that hold the old street, city, state, zip, phone, and fax for checking against new ones if an update is needed for the billing record -->
<input name=old_street type=hidden value="<?php echo $street?>">
<input name=old_city type=hidden value="<?php echo $city?>">
<input name=old_state type=hidden value="<?php echo $state?>">
<input name=old_zip type=hidden value="<?php echo $zip?>">
<input name=old_country type=hidden value="<?php echo $country?>">
<input name=old_phone type=hidden value="<?php echo $phone?>">
<input name=old_fax type=hidden value="<?php echo $fax?>">
<input name=old_contact_email type=hidden value="<?php echo $contactemail?>">

<input name=save type=submit class=smallbutton value="<?php echo lang('savechanges');?>">
<input type=hidden name=load value=customer>
<input type=hidden name=type value=module>
<input type=hidden name=edit value=on>

</center>
</td>
</table>
</form>

