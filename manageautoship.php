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

/* SSL Management */
$useSSL = true;

require_once(dirname(__FILE__).'/../../config/config.inc.php');
require_once(dirname(__FILE__).'/../../init.php');
require_once(dirname(__FILE__).'/skybankaim.php');
$context = Context::getContext();
if ($context->customer->isLogged())
{
        $type = (string)Tools::getValue('type');
	switch($type)
	{
		case 'frequency':
        		$id_order = (int)Tools::getValue('id_order');
        		$frequency = (int)Tools::getValue('frequency');
			if(Db::getInstance()->update('skybankaim_autoship_order', array('frequency' => $frequency), 'id_order = '.(int)$id_order))
				echo(1);
		break;
		case 'order':
        		$id_order = (int)Tools::getValue('id_order');
			if(Db::getInstance()->delete('skybankaim_autoship_order','id_order = '.(int)$id_order))
				die(1);
		break ;
	}
}
