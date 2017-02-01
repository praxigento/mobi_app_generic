<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Test\Downline\Init;

/**
 * Create entries in "customer_entity" and return $map[entity_id]=mlm_id;
 */
class CreateCustomers
{
    const A_MLM_ID = \Praxigento\App\Generic2\Console\Command\Test\Downline\Init::A_CUST_MLM_ID;

    public function do()
    {
        $tbl = $this->_resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER);
        for ($i = 1; $i <= $total; $i++) {
            $email = "customer_$i@test.com";
            $this->_conn->insert(
                $tbl,
                [Cfg::E_CUSTOMER_A_EMAIL => $email]
            );
            $id = $this->_conn->lastInsertId($tbl);
            $this->_mapCustomerMageIdByIndex[$i] = $id;
            $this->_mapCustomerIndexByMageId[$id] = $i;
            $this->_logger->debug("New Magento customer #$i is added with ID=$id ($email).");
        }
        $this->_logger->debug("Total $total customer were added to Magento.");
    }
}