<a href="#" class="skybankaim-logo" target="_blank"><img src="{$module_dir}img/logo_skybank.png" alt="SkyBank" border="0" /></a>
<p class="skybankaim-intro">{l s='Start accepting payments through your PrestaShop store with SkyBank, the pioneering provider of ecommerce payment services.  SkyBank makes accepting payments safe, easy and affordable.' mod='skybankaim'}</p>
<p class="skybankaim-sign-up">{l s='Do you require a payment gateway account? ' mod='skybankaim'}<a href="#" target="_blank">{l s='Sign Up Now' mod='skybankaim'}</a></p>
<form action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}" method="post">
	<fieldset>
		<legend>{l s='Configure your Payment Details' mod='skybankaim'}</legend>
		<fieldset style="position: relative;float: left;width: 40%;">
			<label for="skybankaim_login_id" style="width:150px;">{l s='UserName' mod='skybankaim'}:</label>
			<div class="margin-form" style="margin-bottom: 0px;padding-left:60px;"><input type="text" size="20" id="skybankaim_username" name="skybankaim_username" value="{$SKYBANK_AIM_USERNAME}" /></div>
			<label for="skybankaim_key" style="width:150px;">{l s='Password' mod='skybankaim'}:</label>
			<div class="margin-form" style="margin-bottom: 0px;padding-left:60px;"><input type="text" size="20" id="skybankaim_password" name="skybankaim_password" value="{$SKYBANK_AIM_PASSWORD}" /></div>
			<label for="skybankaim_vendor" style="width:150px;">{l s='Vendor Number' mod='skybankaim'}:</label>
			<div class="margin-form" style="margin-bottom: 0px;padding-left:60px;"><input type="text" size="20" id="skybankaim_vendor" name="skybankaim_vendor" value="{$SKYBANK_AIM_VENDOR}" /></div>


		 <hr size="1" style="background: #BBB; margin: 0; height: 1px; clear:both" noshade="">
		 <div style="float:left;width:50%">
		 	<h4 for="skybankaim_mode">{l s='Account Mode' mod='skybankaim'}</h4>
		 	<div class="margin-form" id="skybankaim_mode"  style="padding-left:60px;">
			<input type="radio" name="skybankaim_mode" value="0" style="vertical-align: middle;" {if !$SKYBANK_AIM_SANDBOX}checked="checked"{/if} />
			<span>{l s='Live mode' mod='skybankaim'}</span><br/>
			<input type="radio" name="skybankaim_mode" value="1" style="vertical-align: middle;" {if $SKYBANK_AIM_SANDBOX}checked="checked"{/if} />
			<span>{l s='Test mode' mod='skybankaim'}</span><br/>
		 	</div>
		 </div>
		 <div style="float:left;width:50%">
		 	
		 	<h4>{l s='Enable Auto-Ship Mode' mod='skybankaim'}</h4>
		 	<div class="margin-form" id="skybankaim_reocc"  style="padding-left:60px;">
			<input type="radio" name="skybankaim_reocc" value="1" style="vertical-align: middle;" {if $SKYBANK_AIM_REOCC}checked="checked"{/if} />
			<span>{l s='Enabled' mod='skybankaim'}</span><br/>
			<input type="radio" name="skybankaim_reocc" value="0" style="vertical-align: middle;" {if !$SKYBANK_AIM_REOCC}checked="checked"{/if} />
			<span>{l s='Disabled' mod='skybankaim'}</span><br/>
		 	</div>
		 </div>

		 <div class="clear"></div>
		 <hr size="1" style="background: #BBB; margin: 0; height: 1px;" noshade="">
			<h4>{l s='Order status' mod='skybankaim'}</h4>
			<label for="skybankaim_cards" style="width:100px;">{l s='Cards' mod='skybankaim'}</label>
			<div class="margin-form" style="padding-left:100px;">
			<select id="skybankaim_card_os" name="skybankaim_card_os">
				{foreach from=$order_states item=os}
					<option value="{$os.id_order_state|intval}" {if ((int)$os.id_order_state == $SKYBANK_AIM_CARD_OS)} selected{/if}>
						{$os.name|stripslashes}
					</option>
				{/foreach}
			</select>
			</div>
			<label for="skybankaim_cards" style="width:100px;">{l s='Check' mod='skybankaim'}</label>
			<div class="margin-form" style="padding-left:100px;">
			<select id="skybankaim_check_os" name="skybankaim_check_os">
				// Hold for Review order state selection
				{foreach from=$order_states item=os}
					<option value="{$os.id_order_state|intval}" {if ((int)$os.id_order_state == $SKYBANK_AIM_CHECK_OS)} selected{/if}>
						{$os.name|stripslashes}
					</option>
				{/foreach}
			</select>
			</div>

		</fieldset>
		<fieldset style="position: relative;float: left;width: 40%;">
			<h4>{l s='Cards*' mod='skybankaim'}</h4>
			<div class="margin-form" style="padding-left:60px;" id="skybankaim_cards">
			<input type="checkbox" name="skybankaim_card_visa" {if $SKYBANK_AIM_CARD_VISA}checked="checked"{/if} />
				<img src="{$module_dir}/img/visa.gif" alt="visa" />
			<input type="checkbox" name="skybankaim_card_mastercard" {if $SKYBANK_AIM_CARD_MASTERCARD}checked="checked"{/if} />
				<img src="{$module_dir}/img/mastercard.gif" alt="visa" />
			<input type="checkbox" name="skybankaim_card_ax" {if $SKYBANK_AIM_CARD_AX}checked="checked"{/if} />
				<img src="{$module_dir}/img/amex.png" alt="American Express" />
			<input type="checkbox" name="skybankaim_card_discover" {if $SKYBANK_AIM_CARD_DISCOVER}checked="checked"{/if} />
				<img src="{$module_dir}/img/discover.png" alt="Discover" />
			</div>
		<hr size="1" style="background: #BBB; margin: 0; height: 1px;" noshade="">
	 	<h4>{l s='Default Transaction Mode' mod='skybankaim'}</h4>
	 	<div class="margin-form" id="skybankaim_sale"  style="padding-left:60px;">
			<input type="radio" name="skybankaim_sale" value="1" style="vertical-align: middle;" {if $SKYBANK_AIM_SALE}checked="checked"{/if} />
			<span>{l s='Sale' mod='skybankaim'}</span><br/>
			<input type="radio" name="skybankaim_sale" value="0" style="vertical-align: middle;" {if !$SKYBANK_AIM_SALE}checked="checked"{/if} />
			<span>{l s='Authorize Only' mod='skybankaim'}</span><br/>
	 	</div>

	 	<hr size="1" style="background: #BBB; margin: 0; height: 1px;" noshade="">
		 	<h4>{l s='Electronic Check Settings' mod='skybankaim'}</h4>
		 <div class="margin-form" id="skybankaim_check"  style="padding-left:60px;">
			<input type="radio" name="skybankaim_check" value="1" style="vertical-align: middle;" {if $SKYBANK_AIM_CHECK}checked="checked"{/if} />
			<span>{l s='Enabled' mod='skybankaim'}</span><br/>
			<input type="radio" name="skybankaim_check" value="0" style="vertical-align: middle;" {if !$SKYBANK_AIM_CHECK}checked="checked"{/if} />
			<span>{l s='Disabled' mod='skybankaim'}</span><br/>
			<input type="checkbox" name="skybankaim_drivers_license_no" {if $SKYBANK_DRV_LCNS_NO}checked="checked"{/if} />
			<label>{l s='Require Drivers License Number' mod='skybankaim'}</label><br/>
		 </div>
		 <hr size="1" style="background: #BBB; margin: 0; height: 1px;" noshade="">
		 	<h4>{l s='Cron' mod='skybankaim'}</h4>
			<p><strong>unique cron url :  <a href="{$cron_url}" onclick="return !window.open($(this).attr('href'));">{$cron_url}</a></strong></p>
			<center>
			<input type="submit" name="submitModule" value="{l s='Update settings' mod='skybankaim'}" class="button" />
			</center>
		</fieldset>
	</fieldset>
</form>
