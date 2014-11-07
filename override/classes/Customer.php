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

class Customer extends CustomerCore
{
	public static function getSkyBankCustomerkey($id_customer) {
		$result = Db::getInstance()->getRow('
			SELECT `customerkey`
			FROM `'._DB_PREFIX_.'skybankaim_customer`
			WHERE `id_customer` = '.(int)$id_customer);
		if (!$result)
			return false;
		return $result['customerkey'];
	}
	public static function getSkyBankCards($id_customer)
	{
		return Db::getInstance()->ExecuteS('
			SELECT *
			FROM `'._DB_PREFIX_.'skybankaim_card`
			WHERE `id_customer` = '.(int)$id_customer);
	}
	public static function checkSkyBankCards($id_customer, $card)
	{ // Fabio: not sure about removing 2nd param here
		$result = Db::getInstance()->ExecuteS('
			SELECT *
			FROM `'._DB_PREFIX_.'skybankaim_card`
			WHERE `id_customer` = '.(int)$id_customer);
		if (!$result)
			return false;
		return isset($result['customerkey']);
	}
}
