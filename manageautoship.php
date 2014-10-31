<?php

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
