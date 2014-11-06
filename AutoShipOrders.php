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
*  @author SkyBank Financial <contact@skybankfinancial.com>
*  @copyright  2014 SkyBank Financial
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of SkyBank Financial
*/
if (!defined('_PS_VERSION_'))
        exit;

class AutoShipOrders extends ObjectModel
{
	public static function getCCInfoKey($id_customer)
	{
                return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
                SELECT c.`ccinfokey`
                FROM `'._DB_PREFIX_.'skybankaim_card` c
                WHERE c.`id_customer` = '.(int)$id_customer);
	}
	public static function getAllOrders()
	{
                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT so.*, o.`id_customer`
                FROM `'._DB_PREFIX_.'orders` o
                JOIN `'._DB_PREFIX_.'skybankaim_autoship_order` so ON (so.`id_order` = o.`id_order`)
                WHERE so.`active` = 1 AND
		DATE_ADD(last_run_date,INTERVAL frequency DAY) <= CURRENT_DATE 
                ORDER BY o.`date_add` DESC');
		return $res;		
	}
	public static function getOrders($id_customer, Context $context = null)
        {
                if (!$context)
                        $context = Context::getContext();

                $res = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT o.*, so.`order_name`, so.`frequency`, (SELECT SUM(od.`product_quantity`) FROM `'._DB_PREFIX_.'order_detail` od WHERE od.`id_order` = o.`id_order`) nb_products
                FROM `'._DB_PREFIX_.'orders` o JOIN `'._DB_PREFIX_.'skybankaim_autoship_order` so ON (so.`id_order` = o.`id_order`)
                WHERE so.`active` = 1 AND o.`id_customer` = '.(int)$id_customer.'
                GROUP BY o.`id_order`
                ORDER BY o.`date_add` DESC');
                if (!$res)
                        return array();

		foreach ($res as $key => $val)
                {
                        $res2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                                SELECT os.`id_order_state`, osl.`name` AS order_state, os.`invoice`, os.`color` as order_state_color
                                FROM `'._DB_PREFIX_.'order_history` oh
                                LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = oh.`id_order_state`)
                                INNER JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$context->language->id.')
                        WHERE oh.`id_order` = '.(int)($val['id_order']).'
                                ORDER BY oh.`date_add` DESC, oh.`id_order_history` DESC
                        LIMIT 1');

                        if ($res2)
                                $res[$key] = array_merge($res[$key], $res2[0]);

                }
                return $res;
        }
     
}
