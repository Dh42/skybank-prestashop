<?php
include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/AutoShipOrders.php');
include(dirname(__FILE__). '/skybankaim.php');

$skybankaim = new SkyBankAIM();
	// $_GET['token'] = '74b0283c782f6e4416df4755fc233447';
if (isset($_GET['token']))
{


        $secureKey = md5(_COOKIE_KEY_.Configuration::get('PS_SHOP_NAME'));
        if (!empty($secureKey) AND $secureKey == $_GET['token']){
		$orders = AutoShipOrders::getAllOrders();

		$orderStatus = Configuration::get('SKYBANK_AIM_CARD_OS');
		foreach($orders as $order){
echo 'Autoship Order #'.$order['id_order'].'<br>' ;
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
            if ($skybankaim->currentOrder){
				Db::getInstance()->insert('skybankaim_re_order', array('id_order'   => (int)$skybankaim->currentOrder)); 	
	 			Db::getInstance()->update('skybankaim_autoship_order', array('last_run_date' => date('Y-m-d')), 'id_order = '.(int)$order['id_order']);	
			}
		}
	}
}
