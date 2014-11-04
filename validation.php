<?php
/*
* 2007-2013 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2013 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

include(dirname(__FILE__). '/../../config/config.inc.php');
include(dirname(__FILE__). '/../../init.php');

/* will include backward file */
include(dirname(__FILE__). '/skybankaim.php');


$skybankaim = new SkyBankAIM();

/* Does the cart exist and is valid? */
$cart = Context::getContext()->cart;

if (!Tools::getValue('x_invoice_num'))
{
	Logger::addLog('Missing x_invoice_num', 4);
	die('An unrecoverable error occured: Missing parameter');
}

if (!Validate::isLoadedObject($cart))
{
	Logger::addLog('Cart loading failed for cart '.(int)Tools::getValue('x_invoice_num'), 4);
	die('An unrecoverable error occured with the cart '.(int)Tools::getValue('x_invoice_num'));
}

if ($cart->id != Tools::getValue('x_invoice_num'))
{
	Logger::addLog('Conflict between cart id order and customer cart id');
	die('An unrecoverable conflict error occured with the cart '.(int)Tools::getValue('x_invoice_num'));
}

$customer = new Customer((int)$cart->id_customer);
$invoiceAddress = new Address((int)$cart->id_address_invoice);
$currency = new Currency((int)$cart->id_currency);

if (!Validate::isLoadedObject($customer) || !Validate::isLoadedObject($invoiceAddress) && !Validate::isLoadedObject($currency))
{
	Logger::addLog('Issue loading customer, address and/or currency data');
	die('An unrecoverable error occured while retrieving you data');
}
$params_f = array(
	'UserName' => Configuration::get('SKYBANK_AIM_USERNAME'),
	'Password' => Configuration::get('SKYBANK_AIM_PASSWORD'),
);
if (Tools::getValue('x_type')== 'check')
{
	$error_no = 2;
	$url = 'https://skybank.payment-gate.net/ws/transact.asmx/ProcessCheck';
	$extras = array(
		'TransType' => 'Sale',
		'CheckNum' => Tools::safeOutput(Tools::getValue('x_check_no')),
		'TransitNum' => Tools::safeOutput(Tools::getValue('x_transit_no')),
		'AccountNum' => Tools::safeOutput(Tools::getValue('x_account_no')),
		'MICR' => '',
		'NameOnCheck' => Tools::safeOutput(Tools::getValue('nameoncheck')),
		'Amount' => (float)$cart->getOrderTotal(true, Cart::BOTH),
		'CheckType' => 'Personal',
		'DL' => '',
		'SS' => '',
		'DOB' => '',
		'StateCode' => '',
		'ExtData' => '',
	);
	$order_status = Configuration::get('SKYBANK_AIM_CHECK_OS');
}else{
	
	$error_no = 1;
	$url = 'https://skybank.payment-gate.net/ws/transact.asmx/ProcessCreditCard';
	$trx_type = Configuration::get('SKYBANK_AIM_SALE');
	$extras = array(
		'TransType' => $trx_type ? 'Sale' : 'Auth',
		'MagData' => '',
		'CardNum' => Tools::safeOutput(Tools::getValue('x_card_num')),
		'CVNum' => Tools::safeOutput(Tools::getValue('x_card_code')),
		'ExpDate' => Tools::safeOutput(Tools::getValue('x_exp_date_m').Tools::getValue('x_exp_date_y')),
		'NameOnCard' => Tools::safeOutput(Tools::getValue('name')),
		'Amount' => number_format((float)$cart->getOrderTotal(true, Cart::BOTH), 2, '.', ''),
		'InvNum' => (int)Tools::getValue('x_invoice_num')+102,
		'PNRef' => $trx_type ? (int)Tools::getValue('x_invoice_num') : '',
		'Street' => Tools::safeOutput($invoiceAddress->address1.' '.$invoiceAddress->address2),
		'Zip' => Tools::safeOutput($invoiceAddress->postcode),
		'ExtData' => '',
	);
	$order_status = Configuration::get('SKYBANK_AIM_CARD_OS');
}
$params = array_merge($params_f, $extras);

/* Do the CURL request ro SkyBank.net */
$response = $skybankaim->getResponse($url, $params);

$pnref = $response->PNRef;

$message = $response->Message;
$payment_method = 'SkyBank APM';
$re_occur = (bool)Configuration::get('SKYBANK_AIM_REOCC');


switch ($response->Result) // Response code
{
	case 0: // Payment accepted
		$transaction_id = $response->Message2->AuthCode;

		// if($re_occur && Tools::safeOutput(Tools::getValue('x_autoShip')) > 0)
		// {
		if (Tools::getValue('x_type') != 'check')
{
		$customerkey = Customer::getSkyBankCustomerkey($customer->id);
		if(!$customerkey)
		{
		$url = 'https://skybank.payment-gate.net/paygate/ws/recurring.asmx/ManageCustomer';
		$state = new State($invoiceAddress->id_state);
		$country = new Country($invoiceAddress->id_country);
		$state_iso_code = ($country->iso_code == 'US') ? Tools::safeOutput($state->iso_code) : '' ;
		if($country->iso_code == 'US') $country_iso_code = 'USA' ;
		else if($country->iso_code == 'CA') $country_iso_code = 'CAN' ;
		else $country_iso_code = '';
		$extras = array(
			'TransType' => 'ADD',
			'Vendor' => (int)Configuration::get('SKYBANK_AIM_VENDOR'),
			'CustomerKey' => '',
			'CustomerID' => $customer->id,
			'CustomerName' => Tools::safeOutput(Tools::getValue('name')),
			'FirstName' => Tools::safeOutput($customer->firstname),
			'LastName' => Tools::safeOutput($customer->lastname),
			'Title' => '',
			'Department' => 'Sales',
			'Street1' => Tools::safeOutput($invoiceAddress->address1),
			'Street2' => Tools::safeOutput($invoiceAddress->address2),
			'Street3' => '',
			'City' => Tools::safeOutput($invoiceAddress->city),
			'StateID' => $state_iso_code,
			'Province' => '',
			'Zip' => Tools::safeOutput($invoiceAddress->postcode),
			'CountryID' => Tools::safeOutput($country_iso_code),
			'DayPhone' => Tools::safeOutput($invoiceAddress->phone),
			'NightPhone' => Tools::safeOutput($invoiceAddress->phone_mobile),
			'Fax' => '',
			'Email' => Tools::safeOutput($customer->email),
			'Mobile' => Tools::safeOutput($invoiceAddress->phone_mobile),
			'Status' => 'ACTIVE',
			'ExtData' => '',
		);
		$params = array_merge($params_f, $extras);
		$response = $skybankaim->getResponse($url, $params);
		$customerkey = $response->CustomerKey;
		Db::getInstance()->insert('skybankaim_customer', array(
                        'id_customer' => (int)$customer->id,
                        'customerkey' => (int)$customerkey,
       			 ));
		}
		// } 



		/* Add CreditCardInfo */
		$url = 'https://skybank.payment-gate.net/paygate/ws/recurring.asmx/ManageCreditCardInfo';
		$extras = array(
			'TransType' => 'ADD',
			'Vendor' => (int)Configuration::get('SKYBANK_AIM_VENDOR'),
			'CustomerKey' => $customerkey,
			'CardInfoKey' => '',
			'CcAccountNum' => Tools::safeOutput(Tools::getValue('x_card_num')),
			'CcExpDate' => Tools::safeOutput(Tools::getValue('x_exp_date_m').Tools::getValue('x_exp_date_y')),
			'CcNameOnCard' => Tools::safeOutput(Tools::getValue('name')),
			'CcStreet' =>  Tools::safeOutput($invoiceAddress->address1.' '.$invoiceAddress->address2),
			'CCZip' =>  Tools::safeOutput($invoiceAddress->postcode),
			'ExtData' => '',
		);
		$params = array_merge($params_f, $extras);
		$response = $skybankaim->getResponse($url, $params);
		if(!empty($response->CcInfoKey))
		Db::getInstance()->insert('skybankaim_card', array(
                    'id_customer' => (int)$customer->id,
                    'card_number' => (string)Tools::substr(Tools::safeOutput(Tools::getValue('x_card_num')), -4),
                    'card_brand' => Tools::safeOutput(Tools::getValue('x_cardType')),
                    'card_expiration' => Tools::safeOutput(Tools::getValue('x_exp_date_m').'/'.Tools::getValue('x_exp_date_y')),
                    'card_holder' => Tools::safeOutput(Tools::getValue('name')),
                    'ccinfokey' => $response->CcInfoKey
           		 ));

		$skybankaim->setTransactionDetail(array(
			$transaction_id,
			Tools::safeOutput(Tools::getValue('x_card_num')),
			Tools::safeOutput(Tools::getValue('x_cardType')),
			Tools::safeOutput(Tools::getValue('name')))
		);
}
		// if check, only set to sale
		if(Tools::getValue('x_type') == 'check')
			$trx_type = 1;

		$extra_vars = array();
		$extra_vars['transaction_id'] = $pnref.'|'.($trx_type ? 'Sale' : 'Auth');
		$skybankaim->validateOrder((int)$cart->id,
			$order_status, (float)$cart->getOrderTotal(true, Cart::BOTH),
			$payment_method, $message, $extra_vars, null, false, $customer->secure_key);
		if($re_occur && Tools::safeOutput(Tools::getValue('x_autoShip')) > 0)
		{
			Db::getInstance()->insert('skybankaim_autoship_order', array(
                                'id_order'   => (int)$skybankaim->currentOrder,
                                'order_name' => Tools::safeOutput(Tools::getValue('x_order_name')),
                                'frequency'  => Tools::safeOutput(Tools::getValue('x_autoShip')),
				'last_run_date'     =>  date('Y-m-d'),
				'active'     => 1
                        ));
		}
		break;
	default:
		$error_message = (!empty($message)) ? urlencode(Tools::safeOutput($message)) : '';

		$checkout_type = Configuration::get('PS_ORDER_PROCESS_TYPE') ?
			'order-opc' : 'order';
		$url = _PS_VERSION_ >= '1.5' ?
			'index.php?controller='.$checkout_type.'&' : $checkout_type.'.php?';
		$url .= 'step=3&cgv=1&skybankerror='.$error_no.'&message='.$error_message;

		if (!isset($_SERVER['HTTP_REFERER']) || strstr($_SERVER['HTTP_REFERER'], 'order'))
			Tools::redirect($url);
		else if (strstr($_SERVER['HTTP_REFERER'], '?'))
			Tools::redirect($_SERVER['HTTP_REFERER'].'&skybankerror='.$error_no.'&message='.$error_message, '');
		else
			Tools::redirect($_SERVER['HTTP_REFERER'].'?skybankerror='.$error_no.'&message='.$error_message, '');

		exit;
}
$url = 'index.php?controller=order-confirmation&';
if (_PS_VERSION_ < '1.5')
	$url = 'order-confirmation.php?';
$auth_order = new Order($skybankaim->currentOrder);
Tools::redirect($url.'id_module='.(int)$skybankaim->id.'&id_cart='.(int)$cart->id.'&key='.$auth_order->secure_key);