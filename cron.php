<?php
/**
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
* @author    SkyBank Financial <contact@skybankfinancial.com>
* @copyright 2014 SkyBank Financial
* @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of SkyBank Financial
*/

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/AutoShipOrders.php');
include(dirname(__FILE__).'/skybankaim.php');

$skybankaim = new SkyBankAIM();
if (Tools::getValue('token'))
{
	$secureKey = md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME'));
	if (!empty($secureKey) && $secureKey == Tools::getValue('token'))
	{
		$orders = AutoShipOrders::getAllOrders();
		$orderStatus = Configuration::get('SKYBANK_AIM_CARD_OS');
		foreach ($orders as $order)
		{
echo 'Autoship Order #'.$order['id_order'].'<br>';
			$oldCart = new Cart(Order::getCartIdStatic((int)$order['id_order'], (int)$order['id_customer']));
			$duplication = $oldCart->duplicate();
			$cart = $duplication['cart'];
			Context::getContext()->currency = new Currency((int)$cart->id_currency);
			Context::getContext()->customer = new Customer((int)$cart->id_customer);
			$CCInfoKey = AutoShipOrders::getCCInfoKey((int)$order['id_customer']);
			$url = 'https://skybank.payment-gate.net/paygate/ws/recurring.asmx/ProcessCreditCard';
			$params = array(
				'UserName' => Configuration::get('SKYBANK_AIM_USERNAME'),
				'Password' => Configuration::get('SKYBANK_AIM_PASSWORD'),
				'Vendor' => (int)Configuration::get('SKYBANK_AIM_VENDOR'),
				'CCInfoKey' => $CCInfoKey,
				'Amount' => (float)$cart->getOrderTotal(true, Cart::BOTH),
				'InvNum' => (int)$cart->id,
				'ExtData' => '',
			);
			$response = $skybankaim->getResponse($url, $params);
			$extra_vars = array();
			$extra_vars['transaction_id'] = $response->PNRef.'|'.(Configuration::get('SKYBANK_AIM_SALE') ? 'Sale' : 'Auth');

			if ($response->Result == 0)
				$skybankaim->validateOrder(
					(int)$cart->id, (int)$orderStatus,
					$cart->getOrderTotal(true, Cart::BOTH), $skybankaim->displayName, $skybankaim->l('AutoShipOrder :').' '.
					$order['order_name'], $extra_vars, null, false, $cart->secure_key
					);
			else
				$skybankaim->validateOrder(
					(int)$cart->id, (int)Configuration::get('PS_OS_ERROR'),
					$cart->getOrderTotal(true, Cart::BOTH), $skybankaim->displayName, $skybankaim->l('AutoShipOrder :').' '.
					$order['order_name'], $extra_vars, null, false, $cart->secure_key
					);
			if ($skybankaim->currentOrder)
			{
				Db::getInstance()->insert('skybankaim_re_order', array('id_order'   => (int)$skybankaim->currentOrder));
				Db::getInstance()->update('skybankaim_autoship_order', array('last_run_date' => date('Y-m-d')), 'id_order = '.(int)$order['id_order']);
			}
		}
	}
}