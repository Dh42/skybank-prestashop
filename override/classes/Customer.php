<?php
/**
* Copyright (C) SkyBank Financial - All Rights Reserved
* Unauthorized redistribution of this file or any of the code here in is strickly prohibited
* Proprietary and confidential
* Written by SkyBank Financial 2014
*/
class Customer extends CustomerCore
{
        static public function getSkyBankCustomerkey($id_customer) {
                $result = Db::getInstance()->getRow('
                	SELECT `customerkey`
                	FROM `'._DB_PREFIX_.'skybankaim_customer`
                	WHERE `id_customer` = '.(int)$id_customer);
                if (!$result)
                        return false;
                return $result['customerkey'];
        }
        static public function getSkyBankCards($id_customer) {
                return Db::getInstance()->ExecuteS('
                	SELECT *
                	FROM `'._DB_PREFIX_.'skybankaim_card`
                	WHERE `id_customer` = '.(int)$id_customer);
        }
        static public function checkSkyBankCards($id_customer, $card) { // Fabio: not sure about removing 2nd param here
                $result = Db::getInstance()->ExecuteS('
                	SELECT *
                	FROM `'._DB_PREFIX_.'skybankaim_card`
                	WHERE `id_customer` = '.(int)$id_customer);
                if (!$result)
                        return false;
                return isset($result['customerkey']);
        }
}
