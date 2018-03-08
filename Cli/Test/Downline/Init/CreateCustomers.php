<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Cli\Test\Downline\Init;

use Praxigento\App\Generic2\Config as Cfg;

/**
 * Create entries in "customer_entity" and return $map[entity_id]=mlm_id;
 */
class CreateCustomers
{
    const A_CUST_MLM_ID = \Praxigento\App\Generic2\Cli\Test\Downline\Init\ReadCsv\Downline::A_CUST_ID;
    const A_EMAIL = \Praxigento\App\Generic2\Cli\Test\Downline\Init\ReadCsv\Downline::A_EMAIL;
    const A_ENTRIES = 'entries';
    const A_MAP_BY_MAGE_ID = 'mapByMageId';
    const A_MAP_BY_MLM_ID = 'mapByMlmId';
    /** @var string 'UserPassword12 */
    protected $DEFAULT_PASSWORD_HASH = '387cf1ea04874290e8e3c92836e1c4b630c5abea110d8766bddb4b3a6224ea04:QVIfkMF7kfwRkkC3HdqJ84K1XANG38LF:1';
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;
    /** @var \Praxigento\App\Generic2\Cli\Test\Downline\Init\ReadCsv\Downline */
    protected $subReadCsv;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\App\Generic2\Cli\Test\Downline\Init\ReadCsv\Downline $subReadCsv
    ) {
        $this->logger = $logger;
        $this->resource = $resource;
        $this->subReadCsv = $subReadCsv;
    }

    public function do()
    {
        $tbl = $this->resource->getTableName(Cfg::ENTITY_MAGE_CUSTOMER);
        $conn = $this->resource->getConnection();
        $entries = $this->subReadCsv->do();
        $mapMageIdByMlmId = [];
        $mapMlmIdByMageId = [];
        $websiteId = Cfg::DEF_WEBSITE_ID_BASE;
        foreach ($entries as $entry) {
            $mlmId = $entry[self::A_CUST_MLM_ID];
            $email = $entry[self::A_EMAIL];
            $nameFirst = 'John';
            $nameLast = 'Doe';
            $groupId = 1;
            $conn->insert(
                $tbl,
                [
                    Cfg::E_CUSTOMER_A_WEBSITE_ID => $websiteId,
                    Cfg::E_CUSTOMER_A_EMAIL => $email,
                    Cfg::E_CUSTOMER_A_FIRSTNAME => $nameFirst,
                    Cfg::E_CUSTOMER_A_LASTNAME => $nameLast,
                    Cfg::E_CUSTOMER_A_GROUP_ID => $groupId,
                    Cfg::E_CUSTOMER_A_PASS_HASH => $this->DEFAULT_PASSWORD_HASH
                ]
            );
            $id = $conn->lastInsertId($tbl);
            $mapMageIdByMlmId[$mlmId] = $id;
            $mapMlmIdByMageId[$id] = $mlmId;
            $this->logger->debug("New Magento customer #$mlmId is added with ID=$id ($email).");
        }
        $total = count($entries);
        $this->logger->debug("Total $total customer were added to Magento.");
        $result = [
            self::A_ENTRIES => $entries,
            self::A_MAP_BY_MLM_ID => $mapMageIdByMlmId,
            self::A_MAP_BY_MAGE_ID => $mapMlmIdByMageId
        ];
        return $result;
    }
}