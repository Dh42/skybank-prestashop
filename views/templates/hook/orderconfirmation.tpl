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

{if $status == 'ok'}
	<p>{l s='Your order on' mod='skybankaim'} <span class="bold">{$shop_name|escape:'htmlall'}</span> {l s='is complete.' mod='skybankaim'}
		<br /><br /><span class="bold">{l s='Your order will be sent as soon as possible.' mod='skybankaim'}</span>
		<br /><br />{l s='For any questions or for further information, please contact our' mod='skybankaim'} <a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='customer support' mod='skybankaim'}</a>.
	</p>
{else}
	<p class="warning">
		{l s='We noticed a problem with your order. If you think this is an error, you can contact our' mod='skybankaim'}
		<a href="{$link->getPageLink('contact', true)|escape:'html'}">{l s='customer support' mod='skybankaim'}</a>.
	</p>
{/if}
