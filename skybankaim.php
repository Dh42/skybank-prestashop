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

if (!defined('_PS_VERSION_'))
	exit;

class SkyBankAIM extends PaymentModule
{
	public function __construct()
	{
		$this->name = 'skybankaim';
		$this->tab = 'payments_gateways';
		$this->version = '1.0.0';
		$this->author = 'SkyBank';
		$this->module_key = "f37280b7ce05ebcd65a20c26baeb654a";
		$this->bootstrap = true;
		$this->aim_available_currencies = array('USD','AUD','CAD','EUR','GBP','NZD');
		$this->tab_class_name = 'AdminOrders';
		$this->subtab_class[1] = 'AdminReOccurOrders';
		$this->tab_name[1] = 'Re-occuringOrders';

		parent::__construct();

		$this->displayName = 'SkyBank Advanced Payment Module';
		$this->description = $this->l('Accept Credit, Debit, and ACH Payments Online With An Optional Auto-Ship Feature Today!');

		/* For 1.4.3 and less compatibility */
		$updateConfig = array(
			'PS_OS_CHEQUE' => 1,
			'PS_OS_PAYMENT' => 2,
			'PS_OS_PREPARATION' => 3,
			'PS_OS_SHIPPING' => 4,
			'PS_OS_DELIVERED' => 5,
			'PS_OS_CANCELED' => 6,
			'PS_OS_REFUND' => 7,
			'PS_OS_ERROR' => 8,
			'PS_OS_OUTOFSTOCK' => 9,
			'PS_OS_BANKWIRE' => 10,
			'PS_OS_PAYPAL' => 11,
			'PS_OS_WS_PAYMENT' => 12);

		foreach ($updateConfig as $u => $v)
			if (!Configuration::get($u) || (int)Configuration::get($u) < 1)
			{
				if (defined('_'.$u.'_') && (int)constant('_'.$u.'_') > 0)
					Configuration::updateValue($u, constant('_'.$u.'_'));
				else
					Configuration::updateValue($u, $v);
			}

		if (!is_callable('curl_exec'))
			$this->warning = $this->l('cURL extension must be enabled on your server to use this module.');

		/* Backward compatibility */
		require(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');

		$this->checkForUpdates();
	}

	public function install()
	{
		self::installSkyBankCustomerTable();
return parent::install() &&
$this->registerHook('orderConfirmation') &&
$this->registerHook('payment') &&
$this->registerHook('header') &&
$this->registerHook('backOfficeHeader') &&
$this->registerHook('adminOrder') &&
$this->registerHook('updateOrderStatus') &&
$this->registerHook('customerAccount') &&
$this->installTab() &&
Configuration::updateValue('SKYBANK_AIM_SANDBOX', 1) &&
Configuration::updateValue('SKYBANK_AIM_CHECK', 0) &&
Configuration::updateValue('SKYBANK_AIM_REOCC', 0) &&
Configuration::updateValue('SKYBANK_AIM_CARD_OS', _PS_OS_ERROR_) &&
Configuration::updateValue('SKYBANK_AIM_CHECK_OS', _PS_OS_ERROR_);
	}
	private function installTab()
	{
		$id_tab = Tab::getIdFromClassName($this->tab_class_name);
		$languages = Language::getLanguages();
		if ($id_tab)
		{
			foreach ($this->subtab_class as $k => $sub_tab)
			{
				$tab = new Tab();
				$tab->class_name = $sub_tab;
				$tab->id_parent = $id_tab;
				$tab->module = $this->name;
				foreach ($languages as $language)
					$tab->name[$language['id_lang']] = $this->tab_name[$k];
				$tab->add();
			}
			return true;
		}
		return false;
	}
	public function uninstall()
	{
		Configuration::deleteByName('SKYBANK_AIM_USERNAME');
		Configuration::deleteByName('SKYBANK_AIM_PASSWORD');
		Configuration::deleteByName('SKYBANK_AIM_VENDOR');
		Configuration::deleteByName('SKYBANK_AIM_SANDBOX');
		Configuration::deleteByName('SKYBANK_AIM_REOCC');
		Configuration::deleteByName('SKYBANK_AIM_CHECK');
		Configuration::deleteByName('SKYBANK_DRV_LCNS_NO');
		Configuration::deleteByName('SKYBANK_AIM_CARD_VISA');
		Configuration::deleteByName('SKYBANK_AIM_CARD_MASTERCARD');
		Configuration::deleteByName('SKYBANK_AIM_CARD_DISCOVER');
		Configuration::deleteByName('SKYBANK_AIM_CARD_AX');
		Configuration::deleteByName('SKYBANK_AIM_CARD_OS');
		Configuration::deleteByName('SKYBANK_AIM_CHECK_OS');

		foreach ($this->subtab_class as $sub_tab)
		{
			$id_tab = Tab::getIdFromClassName($sub_tab);
			if ($id_tab)
			{
				$tab = new Tab($id_tab);
				$tab->delete();
			}
		}
		return parent::uninstall();
	}
	private static function installSkyBankCustomerTable()
	{
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'skybankaim_autoship_order`');
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'skybankaim_customer`');
		Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'skybankaim_card`');
		Db::getInstance()->execute('
			CREATE TABLE `'._DB_PREFIX_.'skybankaim_autoship_order` (
				`id_order` INT  NOT NULL,
				`order_name` VARCHAR(64),
				`frequency` INT NOT NULL,
				`active` tinyint(1) unsigned NOT NULL DEFAULT 0,
				`last_run_date` date NOT NULL,
				INDEX `id_autoship_order` (`id_order`)
				)  ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');

				Db::getInstance()->execute('
				CREATE TABLE `'._DB_PREFIX_.'skybankaim_customer` (
						`id_customer` INT  NOT NULL,
						`customerkey` INT NOT NULL,
				PRIMARY KEY (`id_customer`, `customerkey`),
				INDEX `id_customer` (`id_customer`)
				)  ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');

		Db::getInstance()->execute('
				CREATE TABLE `'._DB_PREFIX_.'skybankaim_card` (
				`id_skybankaim_card` INT NOT NULL AUTO_INCREMENT,
				`id_customer` INT NOT NULL,
				`card_number` varchar(4) NOT NULL,
				`card_brand` varchar(32) NOT NULL,
				`card_expiration` varchar(32) NOT NULL,
				`card_holder` varchar(64) NOT NULL,
				`ccinfokey` INT NOT NULL,
				PRIMARY KEY (`id_skybankaim_card`),
				INDEX `id_customer` (`id_customer`)
				) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');
		}
	public function hookCustomerAccount($params)
	{
		return $this->display(__FILE__, 'views/templates/hook/my-account.tpl');
	}
	public function hookOrderConfirmation($params)
	{
		if ($params['objOrder']->module != $this->name)
			return;

		if ($params['objOrder']->getCurrentState() != Configuration::get('PS_OS_ERROR'))
		{
			Configuration::updateValue('SKYBANKAIM_CONFIGURATION_OK', true);
			$this->context->smarty->assign(array('status' => 'ok', 'id_order' => (int)($params['objOrder']->id)));
		}
		else
			$this->context->smarty->assign('status', 'failed');

		return $this->display(__FILE__, 'views/templates/hook/orderconfirmation.tpl');
	}

	public function hookBackOfficeHeader()
	{
		$this->context->controller->addJQuery();
		if (version_compare(_PS_VERSION_, '1.5', '>='))
			$this->context->controller->addJqueryPlugin('fancybox');

		$this->context->controller->addJS($this->_path.'js/skybankaim.js');
		$this->context->controller->addCSS($this->_path.'css/skybankaim.css');
	}

	public function getContent()
	{
		$html = '';

		if (Tools::isSubmit('submitModule'))
		{
			Configuration::updateValue('SKYBANK_AIM_SANDBOX', (int)Tools::getvalue('skybankaim_mode'));
			Configuration::updateValue('SKYBANK_AIM_CHECK', (int)Tools::getvalue('skybankaim_check'));
			Configuration::updateValue('SKYBANK_DRV_LCNS_NO', Tools::getvalue('skybankaim_drivers_license_no'));
			Configuration::updateValue('SKYBANK_AIM_REOCC', (int)Tools::getvalue('skybankaim_reocc'));
			Configuration::updateValue('SKYBANK_AIM_SALE', (int)Tools::getvalue('skybankaim_sale'));
			Configuration::updateValue('SKYBANK_AIM_CARD_VISA', Tools::getvalue('skybankaim_card_visa'));

			Configuration::updateValue('SKYBANK_AIM_CARD_MASTERCARD', Tools::getvalue('skybankaim_card_mastercard'));
			Configuration::updateValue('SKYBANK_AIM_CARD_DISCOVER', Tools::getvalue('skybankaim_card_discover'));
			Configuration::updateValue('SKYBANK_AIM_CARD_AX', Tools::getvalue('skybankaim_card_ax'));
			Configuration::updateValue('SKYBANK_AIM_CARD_OS', Tools::getvalue('skybankaim_card_os'));
			Configuration::updateValue('SKYBANK_AIM_CHECK_OS', Tools::getvalue('skybankaim_check_os'));
			Configuration::updateValue('SKYBANK_AIM_VENDOR', Tools::getvalue('skybankaim_vendor'));
			Configuration::updateValue('SKYBANK_AIM_USERNAME', Tools::getvalue('skybankaim_username'));
			Configuration::updateValue('SKYBANK_AIM_PASSWORD', Tools::getvalue('skybankaim_password'));

			$html .= $this->displayConfirmation($this->l('Configuration updated'));
		}

		// For "Hold for Review" order status
		$currencies = Currency::getCurrencies(false, true);
		$order_states = OrderState::getOrderStates((int)$this->context->cookie->id_lang);

		$this->context->smarty->assign(array(
			'available_currencies' => $this->aim_available_currencies,
			'currencies' => $currencies,
			'module_dir' => $this->_path,
			'order_states' => $order_states,
			'cron_url' => Tools::getShopDomain(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/cron.php?token='.md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME')),
			'SKYBANK_AIM_USERNAME' => Configuration::get('SKYBANK_AIM_USERNAME'),
			'SKYBANK_AIM_PASSWORD' => Configuration::get('SKYBANK_AIM_PASSWORD'),
			'SKYBANK_AIM_SANDBOX' => (bool)Configuration::get('SKYBANK_AIM_SANDBOX'),
			'SKYBANK_AIM_VENDOR' => Configuration::get('SKYBANK_AIM_VENDOR'),
			'SKYBANK_AIM_CHECK' => (bool)Configuration::get('SKYBANK_AIM_CHECK'),
			'SKYBANK_DRV_LCNS_NO' => Configuration::get('SKYBANK_DRV_LCNS_NO'),
			'SKYBANK_AIM_REOCC' => (bool)Configuration::get('SKYBANK_AIM_REOCC'),
			'SKYBANK_AIM_SALE' => (bool)Configuration::get('SKYBANK_AIM_SALE'),
			'SKYBANK_AIM_CARD_VISA' => Configuration::get('SKYBANK_AIM_CARD_VISA'),
			'SKYBANK_AIM_CARD_MASTERCARD' => Configuration::get('SKYBANK_AIM_CARD_MASTERCARD'),
			'SKYBANK_AIM_CARD_DINERS' => Configuration::get('SKYBANK_AIM_CARD_DINERS'),
			'SKYBANK_AIM_CARD_JCB' => Configuration::get('SKYBANK_AIM_CARD_JCB'),
			'SKYBANK_AIM_CARD_DISCOVER' => Configuration::get('SKYBANK_AIM_CARD_DISCOVER'),
			'SKYBANK_AIM_CARD_AX' => Configuration::get('SKYBANK_AIM_CARD_AX'),
			'SKYBANK_AIM_CARD_OS' => (int)Configuration::get('SKYBANK_AIM_CARD_OS'),
			'SKYBANK_AIM_CHECK_OS' => (int)Configuration::get('SKYBANK_AIM_CHECK_OS'),
		));
		if (version_compare(_PS_VERSION_, '1.6', '>=') )
			return $this->context->smarty->fetch(dirname(__FILE__).'/views/templates/admin/configuration.tpl');
		else
			return $this->context->smarty->fetch(dirname(__FILE__).'/views/templates/admin/configuration15.tpl');
	}

	public function hookPayment($params)
	{
		$currency = Currency::getCurrencyInstance($this->context->cookie->id_currency);

		if (!Validate::isLoadedObject($currency))
			return false;

		if (Configuration::get('PS_SSL_ENABLED') || (!empty($_SERVER['HTTPS']) && Tools::strtolower($_SERVER['HTTPS']) != 'off'))
		{
			$isFailed = Tools::getValue('skybankerror');

			$cards = array();
			$cards['visa'] = Configuration::get('SKYBANK_AIM_CARD_VISA') == 'on';
			$cards['mastercard'] = Configuration::get('SKYBANK_AIM_CARD_MASTERCARD') == 'on';
			$cards['discover'] = Configuration::get('SKYBANK_AIM_CARD_DISCOVER') == 'on';
			$cards['ax'] = Configuration::get('SKYBANK_AIM_CARD_AX') == 'on';
			$cards['diners'] = Configuration::get('SKYBANK_AIM_CARD_DINERS') == 'on';
			$cards['jcb'] = Configuration::get('SKYBANK_AIM_CARD_JCB') == 'on';

			if (method_exists('Tools', 'getShopDomainSsl'))
				$url = 'https://'.Tools::getShopDomainSsl().__PS_BASE_URI__.'/modules/'.$this->name.'/';
			else
				$url = 'https://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/'.$this->name.'/';

			$this->context->smarty->assign('x_invoice_num', (int)$params['cart']->id);
			$this->context->smarty->assign('cards', $cards);
			$this->context->smarty->assign('isFailed', $isFailed);
			$this->context->smarty->assign('new_base_dir', $url);
			$this->context->smarty->assign('echeck', (bool)Configuration::get('SKYBANK_AIM_CHECK'));
			$this->context->smarty->assign('drvlno', (bool)Configuration::get('SKYBANK_DRV_LCNS_NO'));
			$this->context->smarty->assign('reocc', (bool)Configuration::get('SKYBANK_AIM_REOCC'));
			$this->context->smarty->assign('currency', $currency);
			$this->context->smarty->assign('savedcards', Customer::getSkyBankCards($this->context->cookie->id_customer));
			// check the address, disable autoship for non-us addresses
			$address = Address::getCountryAndState($this->context->cart->id_address_invoice);
			if ($address['id_country'] != 21) { // US is supposed to be 21
				$this->context->smarty->assign(array(
					'disable_autoship'=>  true
				));
			}

			return $this->display(__FILE__, 'views/templates/hook/skybankaim.tpl');
		}
	}

	public function hookHeader()
	{
		if (_PS_VERSION_ < '1.5')
			Tools::addJS(_PS_JS_DIR_.'jquery/jquery.validate.creditcard2-1.0.1.js');
		else
			$this->context->controller->addJqueryPlugin('validate-creditcard');
	}
	public function getResponse($url, $params)
	{
		$postString = '';
		foreach ($params as $key => $value)
			$postString .= $key.'='.urlencode($value).'&';
		$postString = trim($postString, '&');
		$request = curl_init($url);
		curl_setopt($request, CURLOPT_HEADER, 0);
		curl_setopt($request, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($request, CURLOPT_POSTFIELDS, $postString);
		curl_setopt($request, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($request, CURLOPT_SSL_VERIFYHOST, false);
		$postResponse = curl_exec($request);

		curl_close($request);

		return  new SimpleXMLElement($postResponse);
	}
	/**
	 * Set the detail of a payment - Call before the validate order init
	 * correctly the pcc object
	 * See SkyBank documentation to know the associated key => value
	 * @param array fields
	 */
	public function setTransactionDetail($response)
	{
		// If Exist we can store the details
		if (isset($this->pcc))
		{
			$this->pcc->transaction_id = (string)$response[0];

			// 50 => Card number (XXXX0000)
			$this->pcc->card_number = (string)Tools::substr($response[1], -4);

			// 51 => Card Mark (Visa, Master card)
			$this->pcc->card_brand = (string)$response[2];

			$this->pcc->card_expiration = (string)Tools::getValue('x_exp_date');

			// 68 => Owner name
			$this->pcc->card_holder = (string)$response[3];
		}
	}

	private function checkForUpdates()
	{
		// Used by PrestaShop 1.3 & 1.4
		if (version_compare(_PS_VERSION_, '1.5', '<') && self::isInstalled($this->name))
			foreach (array('1.4.8', '1.4.11') as $version)
			{
				$file = dirname(__FILE__).'/upgrade/install-'.$version.'.php';
				if (Configuration::get('SKYBANK_AIM') < $version && file_exists($file))
				{
					include_once($file);
					call_user_func('upgrade_module_'.str_replace('.', '_', $version), $this);
				}
			}
	}

	public function getReoccurring($id_order)
	{
		return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT frequency
			FROM '._DB_PREFIX_.'skybankaim_autoship_order
			WHERE id_order = '.(int)$id_order);
	}

	public function removeReOccurring($id_order)
	{
		Db::getInstance()->delete('skybankaim_autoship_order', 'id_order = ' . (int)$id_order);
	}

	public function updateReOccurring($id_order, $new_freq)
	{
		Db::getInstance()->update('skybankaim_autoship_order', array('frequency' => (int)$new_freq),'id_order = ' . (int)$id_order);
	}


	private function totalRefund($id_order)
	{
		$order = new Order((int)$id_order);
		if (!Validate::isLoadedObject($order))
			return false;

		$currency = new Currency((int)$order->id_currency);
		if (!Validate::isLoadedObject($currency))
			$this->_errors[] = $this->l('Not a valid currency');

		if (count($this->_errors))
			return false;

		$order_payment = OrderPayment::getByOrderReference($order->reference);

		// get pnref out of the transaction ID
		$pnref = str_replace('|Sale', '', $order_payment[0]->transaction_id);
		$pnref = str_replace('|Auth', '', $pnref);

		$url = 'https://skybank.payment-gate.net/ws/transact.asmx/ProcessCreditCard';
		$params = array(
			'UserName' => Configuration::get('SKYBANK_AIM_USERNAME'),
			'Password' => Configuration::get('SKYBANK_AIM_PASSWORD'),
			'TransType' => 'Return',
			'CardNum' => '',
			'NameOnCard' => '',
			'ExpDate' => '',
			'InvNum' => (int)$order->invoice_number + 102,
			'PNRef' => $pnref,
			'Zip' => '',
			'Street' => '',
			'MagData' => '',
			'CVNum' => '',
			'Amount' => $order->total_paid_real,
			'ExtData' => ''
			);
		$response = $this->getResponse($url, $params);


		if ($response->RespMSG == 'Approved')
		{
			$history = new OrderHistory();
			$history->id_order = (int)$id_order;
			$history->changeIdOrderState((int)Configuration::get('PS_OS_REFUND'), $history->id_order);
			$history->addWithemail();
			$history->save();
			Tools::redirect($_SERVER['HTTP_REFERER']);
		}
		else
			$this->_errors[] = $response->Message;
	}


	private function forceTransaction($id_order, $cancel = false)
	{
		$order = new Order((int)$id_order);
		if (!Validate::isLoadedObject($order))
			return false;

		if (count($this->_errors))
			return false;

		$order_payment = OrderPayment::getByOrderReference($order->reference);

		$pnref = str_replace('|Sale', '', $order_payment[0]->transaction_id);
		$pnref = str_replace('|Auth', '', $pnref);

		$url = 'https://skybank.payment-gate.net/ws/transact.asmx/ProcessCreditCard';
		$params = array(
			'UserName' => Configuration::get('SKYBANK_AIM_USERNAME'),
			'Password' => Configuration::get('SKYBANK_AIM_PASSWORD'),
			'TransType' => $cancel ? 'Void' : 'Force',
			'CardNum' => '',
			'NameOnCard' => '',
			'ExpDate' => '',
			'InvNum' => (int)$order->invoice_number + 102,
			'PNRef' => $pnref,
			'Zip' => '',
			'Street' => '',
			'MagData' => '',
			'CVNum' => '',
			'Amount' => '',
			'ExtData' => ''
			);
		$response = $this->getResponse($url, $params);


		if (!$cancel)
		{
			$new_trid = $response->PNRef.'|Sale';
			Db::getInstance()->update('order_payment', array('transaction_id' => $new_trid), 'transaction_id = "'. $order_payment[0]->transaction_id.'"');
		}
		if ($response->RespMSG != 'Approved')
		{
			$this->_errors[] = $response->Message;
		} else {
			if ($cancel)
			{
				$history = new OrderHistory();
				$history->id_order = (int)$id_order;
				$history->changeIdOrderState((int)Configuration::get('PS_OS_CANCELED'), $history->id_order);
				$history->addWithemail();
				$history->save();
				Tools::redirect($_SERVER['HTTP_REFERER']);
			}
		}
	}

	public function hookAdminOrder($params)
	{
		$html = '';
		$order = new Order($params['id_order']);

		if (!Validate::isLoadedObject($order))
			return;

		$order_payment = OrderPayment::getByOrderReference($order->reference);


		if (Tools::isSubmit('submitRefund')) {

			$pnref = str_replace('|Sale', '', $order_payment[0]->transaction_id);
			$pnref = str_replace('|Auth', '', $pnref);
			$url = 'https://skybank.payment-gate.net/ws/transact.asmx/ProcessCreditCard';
			$params = array(
				'UserName' => Configuration::get('SKYBANK_AIM_USERNAME'),
				'Password' => Configuration::get('SKYBANK_AIM_PASSWORD'),
				'TransType' => 'Return',
				'CardNum' => '',
				'NameOnCard' => '',
				'ExpDate' => '',
				'InvNum' => (int)$order->invoice_number + 102,
				'PNRef' => $pnref,
				'Zip' => '',
				'Street' => '',
				'MagData' => '',
				'CVNum' => '',
				'Amount' => (int)Tools::getValue('refund_amount'),
				'ExtData' => ''
				);

			$response = $this->getResponse($url, $params);
			$currency = new Currency($order->id_currency);
			if ($response->RespMSG == 'Approved')
				$html .= $this->displayConfirmation($this->l('You refunded') . ' ' . Tools::displayPrice((int)Tools::getValue('refund_amount'), $currency) . ' ' . $this->l('Make sure you use the partial refund button above to fix the amount of this Prestashop Order'));
			else
				$html .= $this->displayError($response->Message);
		} else if (Tools::isSubmit('submitFullRefund'))
		{
			$this->totalRefund($order->id);
			if (!$this->_errors)
				$html .= $this->displayConfirmation($this->l('Transaction completely refunded'));
		}  else if (Tools::isSubmit('SubmitForce'))
		{
			$this->forceTransaction($order->id);
			if (!$this->_errors)
				Tools::redirect($_SERVER['HTTP_REFERER']);
		} else if (Tools::isSubmit('submitCancel'))
		{
			$this->forceTransaction($order->id, true);

			if (!$this->_errors)
				$html .= $this->displayConfirmation($this->l('Transaction canceled'));
		}
			if ($this->_errors)
				$html .= $this->displayError(implode($this->_errors, '<br />'));

		if (Configuration::get('EPAY_ENABLE_PAYMENTREQUEST') == 1 && ($order->total_paid - $order->getTotalPaid()) > 0)
		{
			if (Tools::isSubmit('sendpaymentrequest'))
				$html .= $this->createPaymentRequest(
					$order,
					$params['id_order'],
					Tools::getValue('epay_paymentrequest_amount'),
					$this->context->currency->iso_code,
					Tools::getValue('epay_paymentrequest_requester_name'),
					Tools::getValue('epay_paymentrequest_requester_comment'),
					Tools::getValue('epay_paymentrequest_recipient_email'),
					Tools::getValue('epay_paymentrequest_recipient_name'),
					Tools::getValue('epay_paymentrequest_replyto_email'),
					Tools::getValue('epay_paymentrequest_replyto_name')
				);
			$html .= $this->displayPaymentRequestForm($params).'<br>';
		}


		//  check if the transaction is in the batch, if it is, display refund, otherwise void


		$check_url = 'https://skybank.payment-gate.net/paygate/ws/trxdetail.asmx/GetCardTrx';
		$pnref = str_replace('|Sale', '', $order_payment[0]->transaction_id);
		$pnref = str_replace('|Auth', '', $pnref);
		$params = array(
			'UserName' => Configuration::get('SKYBANK_AIM_USERNAME'),
			'Password' => Configuration::get('SKYBANK_AIM_PASSWORD'),
			'RPNum' => Configuration::get('SKYBANK_AIM_VENDOR'),
			'BeginDt' => '',
			'EndDt' => '',
			'PaymentType' => '',
			'ExcludePaymentType' => '',
			'TransType' => 'GetStatus',
			'ExcludeTransType' => '',
			'ApprovalCode' => '',
			'Result' => '',
			'ExcludeResult' => '',
			'NameOnCard' => '',
			'CardNum' => '',
			'CardType' => '',
			'ExcludeCardType' => '',
			'ExcludeVoid' => '',
			'User' => '',
			'invoiceId' => '',
			'SettleFlag' => '',
			'SettleMsg' => '',
			'SettleDt' => '',
			'TransformType' => '',
			'Xsl' => '',
			'ColDelim' => '',
			'RowDelim' => '',
			'IncludeHeader' => '',
			'ExtData' => '',
			'InvNum' => (int)$order->invoice_number + 102,
			'PNRef' => $pnref,
			);
		$response = $this->getResponse($check_url, $params);
		$realresponse = new SimpleXMLElement($response[0]);
		//Test gateway settlement response
		// echo "<pre>";
		//var_dump($realresponse->TrxDetailCard->Settle_Flag_CH);
		//echo "</pre>";
		if ($realresponse->TrxDetailCard->Settle_Flag_CH == 0)
			$canvoid = true;
		else $canvoid = false;




		if (version_compare(_PS_VERSION_, '1.6', '>='))
		{



			$html .= '
			<div class="col-lg-7">


				<div class="panel">
					<div class="panel-heading"><img width="32" src="../modules/'.$this->name.'/logo.gif"> SkyBank Info</div>

					<form action="" method="POST" class="defaultForm form-horizontal">
						<div class="form-group">';
						if (strstr($order_payment[0]->transaction_id, 'Auth') || (isset($canvoid) && $canvoid)) {


							$html .= '
								<div class="col-lg-2" style="width:213px">
									<input type="submit" class="btn btn-default" name="submitCancel" value="'.$this->l('Void').'"'.(strstr($order_payment[0]->transaction_id, 'Auth') ? 'disabled' : '').'/>

									'.(strstr($order_payment[0]->transaction_id, 'Auth') ? '<input type="submit" class="btn btn-default" name="SubmitForce" value="Capture Transaction"/>' : '').'
								</div>';
								$html .= '<p>'; if (strstr($order_payment[0]->transaction_id, 'Auth')){ $html .= 'You must first capture to void or refund</p>';}

						} else {
							$html .= '<label class="control-label col-lg-2" style="width:200px">
							'.$this->l('Amount to refund:').'
							</label>
							<div class="col-lg-3">
								 <input type="text" name="refund_amount" value=""/>
							</div>
							<div class="col-lg-2" style="width:61px">
								<input class="btn btn-default" type="submit" name="submitRefund" value="Submit" />
							</div>
							<div class="col-lg-2" style="width:120x">
								<input type="submit" class="btn btn-default" name="submitFullRefund" value="Full Refund"/>

							</div>';
						}
						$html .= '

						</div>


					</form>
				</div>

			</div>
			';

		} else {



			$html .= '
			<br/>
			<fieldset>


				<legend><img width="32" src="../modules/'.$this->name.'/logo.gif"> SkyBank Info</legend>

					<form action="" method="POST" class="defaultForm form-horizontal">
						<div class="form-group">';
						if (strstr($order_payment[0]->transaction_id, 'Auth') || (isset($canvoid) && $canvoid)) {


							$html .= '
								<div class="col-lg-2" style="width:213px">
									<input type="submit" class="button" name="submitCancel" value="'.$this->l('Void').'"/>
								'.(strstr($order_payment[0]->transaction_id, 'Auth') ? '<input type="submit" class="button" name="SubmitForce" value="Settle Transaction"/>' : '').'
								</div>';

						} else {
							$html .= '<label class="control-label col-lg-2" style="width:200px">
							'.$this->l('Amount to refund:').'
							</label>
							<div class="col-lg-3">
								 <input type="text" name="refund_amount" value=""/>
							</div>
							<div class="col-lg-2" style="width:61px">
								<input class="button" type="submit" name="submitRefund" value="Submit" />
							</div>
							<div class="col-lg-2" style="width:213px">
								<input type="submit" class="button" name="submitFullRefund" value="Full Refund"/>

							</div>';
						}
						$html .= '

						</div>


					</form>


			</fieldset>
			<br/>
			';
		}




		return $html;
	}

	public function hookUpdateOrderStatus($params)
	{
		// if ($params['newOrderStatus']->id ==Configuration::get('PS_OS_CANCELED'))
		// 	$this->removeReOccurring($params['id_order']);

	}
}