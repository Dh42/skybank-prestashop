<p><a href="#" class="skybankaim-logo" target="_blank"><img src="{$module_dir}img/logo_skybank.png" alt="SkyBank" border="0" /></a></p>
<p class="skybankaim-intro">{l s='Simple. Reliable. Secure. SkyBank has partnered with PrestaShop to provide our clients with an easy-to-use payment module that provides cart owners the flexibility they need to easily accept payments online. With hundreds of millions of dollars processed annually through our payment partner network, we have consistently proven that our clients benefit from using our secure technology.' mod='skybankaim'}</p>
<p class="skybankaim-intro"><a href="https://www.skybankfinancial.com/prestashop.php" target="_blank">{l s='Open your account today!  Sign up now' mod='skybankaim'}</a></p>
<p class="skybankaim-intro">Do you have a feature request that you would like to see in the next version of our module?  Email us at info@skybankfinancial.com and let us know!</p>
<div class="panel">
<div class="panel-heading">{l s='Configure your Payment Details' mod='skybankaim'}</div>
<form action="{$smarty.server.REQUEST_URI|escape:'htmlall':'UTF-8'}" method="post" class="defaultForm form-horizontal">
	<fieldset>
		<fieldset style="position: relative;float: left;width: 40%;">


			<div class="form-group">
				<label class="control-label col-lg-2">{l s='UserName' mod='skybankaim'}:</label>
				<div class="col-lg-6">
					<input type="text" size="20" id="skybankaim_username" name="skybankaim_username" value="{$SKYBANK_AIM_USERNAME}" />
				</div>
			</div>


			<div class="form-group">
				<label class="control-label col-lg-2">{l s='Password' mod='skybankaim'}:</label>
				<div class="col-lg-6">
					<input type="text" size="20" id="skybankaim_password" name="skybankaim_password" value="{$SKYBANK_AIM_PASSWORD}" />
				</div>
			</div>



			<div class="form-group">
				<label class="control-label col-lg-2">{l s='Vendor Number' mod='skybankaim'}:</label>
				<div class="col-lg-6">
					<input type="text" size="20" id="skybankaim_vendor" name="skybankaim_vendor" value="{$SKYBANK_AIM_VENDOR}" />
				</div>
			</div>


		 <hr size="1" style="background: #BBB; margin: 0; height: 1px;" noshade="">


		
		 <div style="float:left;width:100%">
		 	
		 	<h4>{l s='Auto-Ship Settings' mod='skybankaim'}</h4>
		 	<div class="margin-form" id="skybankaim_reocc"  style="padding-left:60px;">
			<input type="radio" name="skybankaim_reocc" value="1" style="vertical-align: middle;" {if $SKYBANK_AIM_REOCC}checked="checked"{/if} />
			<span>{l s='Enabled' mod='skybankaim'}</span><br/>
			<input type="radio" name="skybankaim_reocc" value="0" style="vertical-align: middle;" {if !$SKYBANK_AIM_REOCC}checked="checked"{/if} />
			<span>{l s='Disabled' mod='skybankaim'}</span><br/>
		 	</div>
		 	 <hr size="1" style="background: #BBB; margin: 0; height: 1px;" noshade="">
		 </div>

		 <hr size="1" style="background: #BBB; margin: 0; height: 1px;" noshade="">
	
<div style="position:relative;top:8px;">
	<h4>{l s='Default Order Status' mod='skybankaim'}</h4>
			<div class="form-group">
				<label class="control-label col-lg-2">{l s='Cards' mod='skybankaim'}</label>
				<div class="col-lg-6">
					<select id="skybankaim_card_os" name="skybankaim_card_os">
				{foreach from=$order_states item=os}
					<option value="{$os.id_order_state|intval}" {if ((int)$os.id_order_state == $SKYBANK_AIM_CARD_OS)} selected{/if}>
						{$os.name|stripslashes}
					</option>
				{/foreach}
			</select>
				</div>
			</div>
			</div><div>



			<div class="form-group">
				<label class="control-label col-lg-2">{l s='Check' mod='skybankaim'}</label>
				<div class="col-lg-6">
					<select id="skybankaim_check_os" name="skybankaim_check_os">
						// Hold for Review order state selection
						{foreach from=$order_states item=os}
							<option value="{$os.id_order_state|intval}" {if ((int)$os.id_order_state == $SKYBANK_AIM_CHECK_OS)} selected{/if}>
								{$os.name|stripslashes}
							</option>
						{/foreach}
					</select>
				</div>
			</div>
		</fieldset>
		<fieldset style="position: relative;float: left;width: 40%;">
			<h4>{l s='Credit/Debit Card Settings' mod='skybankaim'}</h4>
			<div class="margin-form" style="padding-left:60px;" id="skybankaim_cards">
			<input type="checkbox" name="skybankaim_card_visa" {if $SKYBANK_AIM_CARD_VISA}checked="checked"{/if} />
				<img src="{$module_dir}/img/visa.png" alt="Visa" />
			<input type="checkbox" name="skybankaim_card_mastercard" {if $SKYBANK_AIM_CARD_MASTERCARD}checked="checked"{/if} />
				<img src="{$module_dir}/img/mc.png" alt="Mastercard" />
			<input type="checkbox" name="skybankaim_card_ax" {if $SKYBANK_AIM_CARD_AX}checked="checked"{/if} />
				<img src="{$module_dir}/img/amex.png" alt="American Express" />
			<input type="checkbox" name="skybankaim_card_discover" {if $SKYBANK_AIM_CARD_DISCOVER}checked="checked"{/if} />
				<img src="{$module_dir}/img/discover.png" alt="Discover" />
			</div>
		<hr size="1" style="background: #BBB; margin: 0; height: 1px;" noshade="">
	 	<h4>{l s='Default Credit/Debit Transaction Mode' mod='skybankaim'}</h4>
	 	<div class="margin-form" id="skybankaim_sale"  style="padding-left:60px;">
			<input type="radio" name="skybankaim_sale" value="1" style="vertical-align: middle;" {if $SKYBANK_AIM_SALE}checked="checked"{/if} />
			<span>{l s='Sale' mod='skybankaim'}</span><br/>
			<input type="radio" name="skybankaim_sale" value="0" style="vertical-align: middle;" {if !$SKYBANK_AIM_SALE}checked="checked"{/if} />
			<span>{l s='Authorize Only' mod='skybankaim'}</span><br/>
	 	</div>
	 	<hr size="1" style="background: #BBB; margin: 0; height: 1px;" noshade="">
		 	<h4>{l s='Electronic Check Settings' mod='skybankaim'}</h4>
		 	<p><b>*Separate ACH Account Approval Required. Not Sure Call? 1-800-815-0935</b></p>
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
			<p><strong>Unique Cron Url :  <a href="{$cron_url}" onclick="return !window.open($(this).attr('href'));">{$cron_url}</a></strong></p>
			<center>
			<input type="submit" name="submitModule" value="{l s='Update settings' mod='skybankaim'}" class="btn btn-default" />
			</center>
		</fieldset>
	</fieldset>
</form>
</div>