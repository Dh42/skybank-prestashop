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

<div id="orders">
    {capture name=path}
        <a href="{$link->getPageLink('my-account', true)|escape:'html'}">{l s='My account' mod='skybankaim' mod='skybankaim'}</a>
        <span class="navigation-pipe">{$navigationPipe|escape:'html'}</span>
        <a href="{$link->getModuleLink('skybankaim', 'orders')|escape:'html'}">{l s='Manage Autoship Orders' mod='skybankaim' mod='skybankaim'}</a>
                {if isset($current_wishlist)}
                <span class="navigation-pipe">{$navigationPipe|escape:'html'}</span>
                {$current_wishlist.name|escape:'html'}
                {/if}
    {/capture}

    <h1 class="page-heading bottom-indent">{l s='Manage Autoship Orders' mod='skybankaim'}</h1>
    <p class="info-title">{l s='Here are the autoship orders you\'ve placed since your account was created.' mod='skybankaim'}</p>
    <div class="block-center" id="block-history">
        {if $orders && count($orders)}
                <table id="order-list" class="table table-bordered footab">
                        <thead>
                                <tr>
                                        <th class="first_item" data-sort-ignore="true">{l s='Order name' mod='skybankaim'}</th>
                                        <th class="item">{l s='Frequency' mod='skybankaim'}</th>
                                        <th data-hide="phone" class="item">{l s='Total price' mod='skybankaim'}</th>
                                        <th data-sort-ignore="true" data-hide="phone,tablet" class="item">{l s='Payment' mod='skybankaim'}</th>
                                        <th class="item">{l s='Cancel' mod='skybankaim'}</th>
                                        <th class="item">{l s='Change' mod='skybankaim'}</th>
                                        <th data-sort-ignore="true" data-hide="phone,tablet" class="item">{l s='Invoice' mod='skybankaim'}</th>
                                        <th data-sort-ignore="true" data-hide="phone,tablet" class="last_item">&nbsp;</th>
                                </tr>
                        </thead>
			<tbody>
                                {foreach from=$orders item=order name=myLoop}
                                        <tr id="autoship_order_{$order.id_order}" class="{if $smarty.foreach.myLoop.first}first_item{elseif $smarty.foreach.myLoop.last}last_item{else}item{/if} {if $smarty.foreach.myLoop.index % 2}alternate_item{/if}">
                                                <td class="history_link bold">
                                                                {$order.order_name|escape:'html'}
                                                </td>
                                                <td class="history_date bold">
								<select id="frequency_{$order.id_order|escape:'html'}">
                        {section name=days start=1 loop=61}
                                <option value="{$smarty.section.days.index}" {if $smarty.section.days.index == $order.frequency}selected="selected" {/if}>{$smarty.section.days.index}</option>
                        {/section}
                </select>
                                                                &nbsp; days
                                                </td>
                                                <td class="history_price" data-value="{$order.total_paid|escape:'html'}">
                                                        <span class="price">
                                                                {displayPrice price=$order.total_paid currency=$order.id_currency no_utf8=false convert=false}
                                                        </span>
                                                </td>
						<td class="history_method">{$order.payment|escape:'html':'UTF-8'}</td>
                                                <td class="history_state">
							<a  href="javascript:cancelAutoship({$order.id_order|intval});" class="btn btn-default button button-small "><span>{l s='Cancel' mod='skybankaim'}</span></a>
                                                </td>
                                                <td class="history_state">
							<a  href="javascript:changeAutoship({$order.id_order|intval});" class="btn btn-default button button-small"><span>{l s='Update Frequency' mod='skybankaim'}</span></a>
                                                </td>
                                                <td class="history_invoice">
                                                        {if (isset($order.invoice) && $order.invoice && isset($order.invoice_number) && $order.invoice_number) && isset($invoiceAllowed) && $invoiceAllowed == true}
                                                                <a class="link-button" href="{$link->getPageLink('pdf-invoice', true, NULL, "id_order={$order.id_order}")|escape:'html':'UTF-8'}" title="{ mod='skybankaim'l s='Invoice'}" target="_blank">
                                                                        <i class="icon-file-text large"></i>{l s='PDF' mod='skybankaim'}
                                                                </a>
                                                        {else}
                                                                -
                                                        {/if}
                                                </td>
						<td class="history_detail">
                                                        <a class="btn btn-default button button-small" href="javascript:showOrder(1, {$order.id_order|intval}, '{$link->getPageLink('order-detail', true)|escape:'html':'UTF-8'}');">
                                                                <span>
                                                                        {l s='Details' mod='skybankaim'}<i class="icon-chevron-right right"></i>
                                                                </span>
                                                        </a>
                                                        {if isset($opc) && $opc}
                                                                <a class="link-button" href="{$link->getPageLink('order-opc', true, NULL, "submitReorder&id_order={$order.id_order|intval}")|escape:'html':'UTF-8'}" title="{ mod='skybankaim'l s='Reorder'}">
                                                        {else}
                                                                <a class="link-button" href="{$link->getPageLink('order', true, NULL, "submitReorder&id_order={$order.id_order|intval}")|escape:'html':'UTF-8'}" title="{ mod='skybankaim'l s='Reorder'}">
                                                        {/if}
                                                                {if isset($reorderingAllowed) && $reorderingAllowed}
                                                                        <i class="icon-refresh"></i>{l s='Reorder' mod='skybankaim'}
                                                                {/if}
                                                        </a>
                                                </td>
					</tr>
				{/foreach}	
			</tbody>
			</table>
			<div id="block-order-detail" class="unvisible">&nbsp;</div>
	{/if}	
	</div>
</div>
<script>
	function changeAutoship(id_order)
	{
		c = confirm('Are you sure you want to change Frequency ?.');
		if(c)
		{
		var frequency = $('#frequency_' + id_order).val();
		$.ajax({
                        type: 'GET',
                        async: true,
                        url: baseDir + 'modules/skybankaim/manageautoship.php',
                        data: 'type=frequency&id_order=' + id_order + '&frequency=' + frequency +'&refresh=' + false,
                        cache: false,
                        success: function(data)
                        {
				//$('#autoship_order_' + id_order).hide();
				alert('Autoship Frequency changed');
                        }
                });
		}
	}
	function cancelAutoship(id_order)
	{
        c = confirm('Are you sure you want to remove this order ?.');
        if(c)
        {
		$.ajax({
                        type: 'GET',
                        async: true,
                        url: baseDir + 'modules/skybankaim/manageautoship.php',
                        data: 'type=order&id_order=' + id_order + '&refresh=' + false,
                        cache: false,
                        success: function(data)
                        {
				$('#autoship_order_' + id_order).hide();
				alert('Autoship cancelled');
                        }
                });
        }
	}
</script>
