{*
* 2014 SkyBank Financial
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@skybankfinancial.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author SkyBank Financial <contact@skybankfinancial.com>
* @copyright  2014 SkyBank Financial
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of SkyBank Financial
*}

<link rel="shortcut icon" type="image/x-icon" href="{$module_dir|escape:'html'}img/secure.png" />
<p class="payment_module" >	
{if $isFailed == 1}		
	<p style="color: red;">			
	{if !empty($smarty.get.message)}
		{l s='Error detail from SkyBank : ' mod='skybankaim'}
		{$smarty.get.message|htmlentities}
	{else}	
		{l s='Error, please verify the card information' mod='skybankaim'}
	{/if}
	</p>
{/if}
<form name="skybankaim_form" id="skybankaim_form" action="{$module_dir}validation.php" method="post">
 <span style="border: 1px solid #595A5E;display: block;padding: 0.6em;text-decoration: none;margin-left: 0.7em;">
  <a id="click_skybankaim" href="#" title="{l s='Pay by Credit/Debit Card' mod='skybankaim'}" style="display: block;text-decoration: none; font-weight: bold;">
  {if $cards.visa == 1}<img src="{$module_dir}img/visa.png" alt="{l s='Visa Logo' mod='skybankaim'}" style="vertical-align: middle;" />{/if}
  {if $cards.mastercard == 1}<img src="{$module_dir}img/mc.png" alt="{l s='Mastercard Logo' mod='skybankaim'}" style="vertical-align: middle;" />{/if}
    {if $cards.ax == 1}<img src="{$module_dir}img/amex.png" alt="{l s='American Express Logo' mod='skybankaim'}" style="vertical-align: middle;" />{/if}
  {if $cards.discover == 1}<img src="{$module_dir}img/discover.png" alt="{l s='Discover Logo' mod='skybankaim'}" style="vertical-align: middle;" />{/if}
  	&nbsp;&nbsp;{l s='Pay by Credit/Debit Card' mod='skybankaim'}			
  </a>
  {if $isFailed == 0 || $isFailed == 2}						
  	<div id="skybank_tpws" style="display:none">				
  {else}						
	<div id="skybank_tpws" style="display:inline-flex">				
  {/if}				
	 <br /><br />				
	 <div style="width: 136px; height: 350px; padding-top:40px; padding-right: 20px; border-right: 1px solid #DDD;">
	  <img width="130" src="{$module_dir}img/logo_skybank.png" alt="secure payment" />
	 </div>				
	 <div style="width: 556px; height: 350px; padding-top:40px; padding-right: 20px;">
	 <input type="hidden" name="x_type" value="card"/>				
	 <input type="hidden" name="x_invoice_num" value="{$x_invoice_num|escape:'htmlall':'UTF-8'}" />
	 <input type="hidden" name="x_currency_code" value="{$currency->iso_code|escape:'htmlall':'UTF-8'}" />
	 <label style="margin-top: 4px; margin-left: 35px;display: block;width: 90px;float: left;">{l s='Full name' mod='skybankaim'}</label> 
	 <input type="text" name="name" id="skybank_fullname" size="30" maxlength="255" style="width:190px;"/><img src="{$module_dir}img/secure.png" alt="" style="margin-left: 5px;" /><br /><br />
	 <label style="margin-top: 4px; margin-left: 35px; display: block;width: 90px;float: left;">{l s='Card Type' mod='skybankaim'}</label>
	 <select id="skybank_cardType" name="x_cardType" style="width:185px;">					
	  {if $cards.ax == 1}<option value="AmEx">American Express</option>{/if}
	  {if $cards.visa == 1}<option value="Visa">Visa</option>{/if}
	  {if $cards.mastercard == 1}<option value="MasterCard">MasterCard</option>{/if}	
      {if $cards.discover == 1}<option value="Discover">Discover</option>{/if}				
      {if $cards.discover == 1}<option value="DinersClub">DinersClub</option>{/if}
      {if $cards.discover == 1}<option value="JCB">JCB</option>{/if}
      </select>				
         <img src="{$module_dir}img/secure.png" alt="" style="margin-left: 5px;" /><br /><br />
         <label style="margin-top: 4px; margin-left: 35px; display: block; width: 90px; float: left;">{l s='Card Number' mod='skybankaim'}</label> 
         <input type="text" name="x_card_num" id="skybank_cardnum" size="30" maxlength="16" autocomplete="Off" value="" style="width:190px;"style="width:190px;" /><img src="{$module_dir}img/secure.png" alt="" style="margin-left: 5px;" /><br /><br />
         <label style="margin-top: 4px; margin-left: 35px; display: block; width: 90px; float: left;">{l s='EXP Date' mod='skybankaim'}</label>	
         <select id="skybank_x_exp_date_m" name="x_exp_date_m" style="width:80px;">
	  {section name=date_m start=01 loop=13}					
	  	<option value="{$smarty.section.date_m.index}">{$smarty.section.date_m.index}</option>
	  {/section}				
         </select>/
         <select name="x_exp_date_y" style="width:100px;">
	  {section name=date_y start=14 loop=26}
	  	<option value="{$smarty.section.date_y.index}">20{$smarty.section.date_y.index}</option>
	  {/section}				
         </select>				
         <img src="{$module_dir}img/secure.png" alt="" style="margin-left: 5px;" /><br /><br />				
         <label style="margin-top: 4px; margin-left: 35px; display: block; width: 90px; float: left;">{l s='CVV' mod='skybankaim'}</label> 
         <input type="text" name="x_card_code" id="skybank_x_card_code" size="4" maxlength="4" style="width:190px;"/>
         <img src="{$module_dir}img/secure.png" alt="" style="margin-left: 5px;"/>
         <span style="position:relative">
			<img src="{$module_dir}img/help.png" id="cvv_help" title="{l s='the 3 last digits on the back of your credit card' mod='skybankaim'}" alt="" />
	        <img style="display:none; position:absolute;left: 20px; top: -100px" src="{$module_dir}img/cvv2.png" id="cvv_help_img" title="{l s='the 3 last digits on the back of your credit card' mod='skybankaim'}" alt="" />
         </span>
         
		<br><br>
		{if $reocc == 1}
		<p class="checkbox">
			<label style="margin-top: 4px; margin-left: 35px; display: block; width: 260px; float: left;color:black;font-weight:bold;" for="toggle-autoship">{l s='Would you like to Auto-Ship this order?' mod='skybankaim'}</label>
			<input type="checkbox" id="togle-autoship" value="1" style="float:left" />
		</p>
		{/if}
		
		
		<p class="submit">
			<input type="button" id="tpwssubmit" value="{l s='Process Payment' mod='skybankaim'}" style="margin-left: 174px; padding-left: 25px; padding-right: 25px;" class="button" />
		</p>

	 </div>				
         {if $reocc == 1 && !isset($disable_autoship)}
	 	<div class="autoship-box" style="width: 326px; height: 200px; padding-top:40px;  padding-left: 20px;padding-right: 20px;border: 2px solid skyblue; display:none">
	
		<h4 style="text-align: center;">{l s='Set this Order to Auto-Ship' mod='skybankaim'}</h4>
		<label style="margin-top: 4px; margin-left: 5px; display: block;width: 140px;float: left;">{l s='Ship this Order every' mod='skybankaim'}</label>
		<select id="skybank_autoShip" name="x_autoShip" >
			<option value="">{l s=' Choose' mod='skybankaim'}</option>
			<option value="7 ">{l s='7 days ' mod='skybankaim'}</option>
			<option value="14">{l s='14 days ' mod='skybankaim'}</option>
			<option value="30">{l s='30 days' mod='skybankaim'}</option>
			<option value="60">{l s='2 Months' mod='skybankaim'}</option>
			<option value="90">{l s='3 months ' mod='skybankaim'}</option>
			<option value="180">{l s='6 months' mod='skybankaim'}</option>
			
                </select>
		<br class="clear" />
         	<input type="text" name="x_order_name" id="skybank_order_name" size="30" maxlength="40" placeholder="{l s='Name Order' mod='skybankaim'}"/>
		<br />	
	 	</div>				
          {/if}
<br class="clear" />			
</div>		

</span>	
</form>
</p>
{if $echeck == 1 && !isset($disable_autoship)}		
<p class="payment_module" >	
	{if $isFailed == 2}		
	<p style="color: red;">			
	{if !empty($smarty.get.message)}
		{l s='Error detail from SkyBank : ' mod='skybankaim'}
		{$smarty.get.message|htmlentities}
	{else}	
		{l s='Error, please verify the check information' mod='skybankaim'}
	{/if}
	</p>
	{/if}
	<form name="skybankcheck_form" id="skybankcheck_form" action="{$module_dir}validation.php" method="post">
	 <span style="border: 1px solid #595A5E;display: block;padding: 0.6em;text-decoration: none;margin-left: 0.7em;">
	 <a id="click_skybank_echeck" href="#" title="{l s='Pay by Check' mod='skybankaim'}" style="display: block;text-decoration: none; font-weight: bold;"><img src="{$module_dir}img/echeck.jpg" alt="{l s='Echeck Logo' mod='skybankaim'}" style="vertical-align: middle;" />
          &nbsp;&nbsp;{l s='Pay by Electronic Check' mod='skybankaim'}			
         </a>
  {if $isFailed == 0 || $isFailed == 1}						
  	<div id="skybank_check" style="display:none">				
  {else}						
	<div id="skybank_check" style="display:inline-flex">				
  {/if}				
	  <br /><br />				
	 <div style="width: 136px; height: 350px; padding-top:40px; padding-right: 20px; border-right: 1px solid #DDD;">
	  <img width="130" src="{$module_dir}img/logo_skybank.png" alt="secure payment" />
	 </div>				
	 <div style="width: 556px; height: 350px; padding-top:40px; padding-right: 20px;">
	  <input type="hidden" name="x_type" value="check"/>				
	  <input type="hidden" name="x_invoice_num" value="{$x_invoice_num|escape:'htmlall':'UTF-8'}" />
	  <input type="hidden" name="x_currency_code" value="{$currency->iso_code|escape:'htmlall':'UTF-8'}" />
	  <label style="margin-top: 4px; margin-left: 35px;display: block;width: 140px;float: left;">{l s='Name on Account' mod='skybankaim'}</label> 
	  <input type="text" name="nameoncheck" id="skybank_nameoncheck" size="30" maxlength="25S" /><img src="{$module_dir}img/secure.png" alt="" style="margin-left: 5px;" /><br /><br />
	  <label style="margin-top: 4px; margin-left: 35px; display: block; width: 140px; float: left;">{l s='Check Number' mod='skybankaim'}</label> 
	  <input type="text" name="x_check_no" id="skybank_x_check_no" size="30" maxlength="4" /><img src="{$module_dir}img/secure.png" alt="" style="margin-left: 5px;" /><br /><br />
	  <label style="margin-top: 4px;margin-left: 35px; display: block; width: 140px; float: left;">{l s='Routing Number' mod='skybankaim'}</label> 
	  <input type="text" name="x_transit_no" id="skybank_x_transit_no" size="30" maxlength="14" /><img src="{$module_dir}img/secure.png" alt="" style="margin-left: 5px;" /><br /><br />
	  <label style="margin-top: 4px; margin-left: 35px; display: block; width:140px; float: left;">{l s='Account Number' mod='skybankaim'}</label> 
	  <input type="text" name="x_account_no" id="skybank_x_account_no" size="30" maxlength="14" /><img src="{$module_dir}img/secure.png" alt="" style="margin-left: 5px;" /><br /><br />
{if $drvlno == 1}		
	  <label style="margin-top: 4px; margin-left: 35px; display: block; width:140px; float: left;">{l s='Drivers License Number' mod='skybankaim'}</label> 
	  <input type="text" name="x_drvlno" id="skybank_x_drvlno" size="30" maxlength="14" /><img src="{$module_dir}img/secure.png" alt="" style="margin-left: 5px;" /><br /><br />
{/if}				

          <input type="button" id="checksubmit" value="{l s='Process Payment' mod='skybankaim'}" style="margin-left: 124px; padding-left: 25px; padding-right: 25px;" class="button" />
          <br class="clear" />			
	 </div>		
<br class="clear" />			
</div>		
        </span>	
       </form>
</p>
<script type="text/javascript">
	var mess_error3= "{l s='Please enter Check information (Check Number, Transit NUmber & AccountNumber)' mod='skybankaim' js=1}";
	var mess_error4 = "{l s='Please specify Name on Account' mod='skybankaim' js=1}";
	var mess_error5 = "{l s='Please enter your Drivers License Number' mod='skybankaim' js=1}";
	{literal}		
	$(document).ready(function() {
		$('#click_skybank_echeck').click(function(e) {
			e.preventDefault();
			$('#click_skybank_echeck').fadeOut("fast", function() {
				$("#skybank_check").show();
				$("#skybank_check").css('display','inline-flex');
				$('#click_skybank_echeck').fadeIn('fast');
			});
			$('#click_skybank_echeck').unbind();
			$('#click_skybankaim').click(function(e) {
				e.preventDefault();
			});
		});
		$('#checksubmit').click(function() {
			if ($('#skybank_nameoncheck').val() == '') {
				alert(mess_error4);
			} else if ($('#skybank_x_check_no').val() == '' || $('#skybank_x_transit_no').val() == '' || $('#skybank_x_account_no').val() == '' || $('#skybank_x_micr').val() == '') {
				alert(mess_error3);
			} else if ($('#skybank_x_drvlno').length && $('#skybank_x_drvlno').val() == '') {
				alert(mess_error5);
			} else {
				$('#skybankcheck_form').submit();
			}
			return false;
		});
	});
	{/literal}
</script>
{/if}
<script type="text/javascript">
	var mess_error = "{l s='Please check your credit card information (Credit card type, number and expiration date)' mod='skybankaim' js=1}";
	var mess_error2 = "{l s='Please specify your Full Name' mod='skybankaim' js=1}";
	{literal}		$(document).ready(function() {
		$('#skybank_x_exp_date_m').children('option').each(function() {
			if ($(this).val() < 10) {
				$(this).val('0' + $(this).val());
				$(this).html($(this).val())
			}
		});
		$('#click_skybankaim').click(function(e) {
			e.preventDefault();
			$('#click_skybankaim').fadeOut("fast", function() {
				$("#skybank_tpws").show();
				$("#skybank_tpws").css('display','inline-flex');
				$('#click_skybankaim').fadeIn('fast');
			});
			$('#click_skybankaim').unbind();
			$('#click_skybankaim').click(function(e) {
				e.preventDefault();
			});
		});
		$('#cvv_help').hover(
			function() {
				$("#cvv_help_img").stop().fadeIn();
			},
			function(){
				$("#cvv_help_img").stop().fadeOut();
			}
			);
		$('#tpwssubmit').click(function() {
			if ($('#skybank_fullname').val() == '') {
				alert(mess_error2);
			} else if (!validateCC($('#skybank_cardnum').val(), $('#skybank_cardType').val()) || $('#skybank_x_card_code').val() == '') {
				alert(mess_error);
			} else {
				$('#skybankaim_form').submit();
			}
			return false;
		});
	});{/literal}</script>


<script>

$(document).ready(function() {
	$('#togle-autoship').click(function() {
		if($(this).is(':checked'))
		{
			$('.autoship-box').show();

		} else {

			$('.autoship-box').hide();
			$('#skybank_autoShip').find('option:selected').removeAttr('selected');
			$('#skybank_autoShip').find('option:first').attr('selected', 'selected');
		}
	});
});
</script>	