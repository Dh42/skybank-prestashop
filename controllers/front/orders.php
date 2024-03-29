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

class SkyBankAimOrdersModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public $display_column_left = false;

	public function __construct()
	{
		parent::__construct();
		$this->context = Context::getContext();
		include_once($this->module->getLocalPath().'AutoShipOrders.php');
	}
	public function setMedia()
	{
		parent::setMedia();
		$this->addCSS(array(
			_THEME_CSS_DIR_.'history.css',
			_THEME_CSS_DIR_.'addresses.css'
			));
		$this->addJS(array(
			_THEME_JS_DIR_.'history.js',
			_THEME_JS_DIR_.'tools.js' // retro compat themes 1.5
			));
		$this->addJqueryPlugin('footable');
		$this->addJqueryPlugin('footable-sort');
		$this->addJqueryPlugin('scrollTo');
	}

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();
		$this->assign();
	}

	/**
	 * Assign wishlist template
	 */
	public function assign()
	{
		$errors = array();

		if ($this->context->customer->isLogged())
		{
			$add = Tools::getIsset('add');
			$add = (empty($add) === false ? 1 : 0);
			$delete = Tools::getIsset('deleted');
			$delete = (empty($delete) === false ? 1 : 0);
			$id_wishlist = Tools::getValue('id_wishlist');
			if (Tools::isSubmit('submitWishlist'))
			{
				if (Configuration::get('PS_TOKEN_ACTIVATED') == 1 && strcmp(Tools::getToken(), Tools::getValue('token')))
					$errors[] = $this->module->l('Invalid token', 'mywishlist');
				if (!count($errors))
				{
					$name = Tools::getValue('name');
					if (empty($name))
						$errors[] = $this->module->l('You must specify a name.', 'mywishlist');
					if (AutoShipOrders::isExistsByNameForUser($name))
						$errors[] = $this->module->l('This name is already used by another list.', 'mywishlist');

					if (!count($errors))
					{
						$wishlist = new AutoShipOrders();
						$wishlist->id_shop = $this->context->shop->id;
						$wishlist->id_shop_group = $this->context->shop->id_shop_group;
						$wishlist->name = $name;
						$wishlist->id_customer = (int)$this->context->customer->id;
						list($us, $s) = explode(' ', microtime());
						srand($s * $us);
						$wishlist->token = Tools::strtoupper(Tools::substr(sha1(uniqid(rand(), true)._COOKIE_KEY_.$this->context->customer->id), 0, 16));
						$wishlist->add();
						Mail::Send(
							$this->context->language->id,
							'wishlink',
							Mail::l('Your wishlist\'s link', $this->context->language->id),
							array(
							'{wishlist}' => $wishlist->name,
							'{message}' => $this->context->link->getModuleLink('blockwishlist', 'view', array('token' => $wishlist->token))
							),
							$this->context->customer->email,
							$this->context->customer->firstname.' '.$this->context->customer->lastname,
							null,
							(string)Configuration::get('PS_SHOP_NAME'),
							null,
							null,
							$this->module->getLocalPath().'mails/');

						Tools::redirect($this->context->link->getModuleLink('blockwishlist', 'mywishlist'));
					}
				}
			}
			else if ($add)
				AutoShipOrders::addCardToWishlist($this->context->customer->id, Tools::getValue('id_wishlist'), $this->context->language->id);
			elseif ($delete && empty($id_wishlist) === false)
			{
				$wishlist = new AutoShipOrders((int)$id_wishlist);
				if (Validate::isLoadedObject($wishlist))
					$wishlist->delete();
				else
					$errors[] = $this->module->l('Cannot delete this wishlist', 'mywishlist');
			}
			$this->context->smarty->assign('orders', AutoShipOrders::getOrders($this->context->customer->id));
			//$this->context->smarty->assign('nbProducts', AutoShipOrders::getInfosByIdCustomer($this->context->customer->id));
		}
		else
			Tools::redirect('index.php?controller=authentication&back='.urlencode($this->context->link->getModuleLink('blockwishlist', 'mywishlist')));

		$this->context->smarty->assign(array(
			'id_customer' => (int)$this->context->customer->id,
			'errors' => $errors,
			'form_link' => $errors,
		));

		$this->setTemplate('autoship-orders.tpl');
	}
}