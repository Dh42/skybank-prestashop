{*
* Copyright (C) SkyBank Financial - All Rights Reserved
* Unauthorized redistribution of this file or any of the code here in is strickly prohibited
* Proprietary and confidential
* Written by SkyBank Financial 2014
*}
{if $status == 'ok'}
	<p>{l s='Your order on' mod='skybankaim'} <span class="bold">{$shop_name}</span> {l s='is complete.'mod='skybankaim'}
		<br /><br /><span class="bold">{l s='Your order will be sent as soon as possible.' mod='skybankaim'}</span>
		<br /><br />{l s='For any questions or for further information, please contact our' mod='skybankaim'} <a href="{$link->getPageLink('contact', true)}">{l s='customer support' mod='skybankaim'}</a>.
	</p>
{else}
	<p class="warning">
		{l s='We noticed a problem with your order. If you think this is an error, you can contact our' mod='skybankaim'} 
		<a href="{$link->getPageLink('contact', true)}">{l s='customer support' mod='skybankaim'}</a>.
	</p>
{/if}
