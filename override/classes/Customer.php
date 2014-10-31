<?php

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
        static public function checkSkyBankCards($id_customer, $card) {
                $result = Db::getInstance()->ExecuteS('
                	SELECT *
                	FROM `'._DB_PREFIX_.'skybankaim_card`
                	WHERE `id_customer` = '.(int)$id_customer);
                if (!$result)
                        return false;
                return isset($result['customerkey']);
        }
}
