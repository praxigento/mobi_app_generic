<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Cli\Test\Downline\Init;

use Praxigento\App\Generic2\Config as Cfg;

/**
 * Create downline tree;
 */
class CreateDownline
{
    const A_ENTRIES = \Praxigento\App\Generic2\Cli\Test\Downline\Init\CreateCustomers::A_ENTRIES;
    const A_MAP_BY_MLM_ID = \Praxigento\App\Generic2\Cli\Test\Downline\Init\CreateCustomers::A_MAP_BY_MLM_ID;
    /** @var \Praxigento\Downline\Service\ISnap */
    protected $callDwnlSnap;
    /** @var  \Praxigento\Core\Api\Helper\Format */
    protected $hlpFormat;
    /** @var \Psr\Log\LoggerInterface */
    protected $logger;
    /** @var \Praxigento\Downline\Repo\Dao\Change */
    protected $daoDwnlChange;
    /** @var \Praxigento\Downline\Repo\Dao\Customer */
    protected $daoDwnlCust;
    /** @var \Magento\Framework\App\ResourceConnection */
    protected $resource;

    public function __construct(
        \Praxigento\Core\Api\App\Logger\Main $logger,
        \Magento\Framework\App\ResourceConnection $resource,
        \Praxigento\Core\Api\Helper\Format $hlpFormat,
        \Praxigento\Downline\Repo\Dao\Change $daoDwnlChange,
        \Praxigento\Downline\Repo\Dao\Customer $daoDwnlCust,
        \Praxigento\Downline\Service\ISnap $callDwnlSnap
    ) {
        $this->logger = $logger;
        $this->resource = $resource;
        $this->hlpFormat = $hlpFormat;
        $this->daoDwnlChange = $daoDwnlChange;
        $this->daoDwnlCust = $daoDwnlCust;
        $this->callDwnlSnap = $callDwnlSnap;
    }

    public function do($data)
    {
        $tree = $data[self::A_ENTRIES];
        $mapByMlmId = $data[self::A_MAP_BY_MLM_ID];
        /* expand minimal tree: populate tree with depth & path */
        $reqExpand = new \Praxigento\Downline\Service\Snap\Request\ExpandMinimal();
        $reqExpand->setTree($tree);
        $reqExpand->setKeyCustomerId(\Praxigento\App\Generic2\Cli\Test\Downline\Init\ReadCsv\Downline::A_CUST_ID);
        $reqExpand->setKeyParentId(\Praxigento\App\Generic2\Cli\Test\Downline\Init\ReadCsv\Downline::A_PARENT_ID);
        $respExpand = $this->callDwnlSnap->expandMinimal($reqExpand);
        $expandedTree = $respExpand->getSnapData();
        /* order tree by depth */
        uasort($expandedTree, function ($a, $b) {
            $result = $a[\Praxigento\Downline\Repo\Data\Snap::A_DEPTH] - $b[\Praxigento\Downline\Repo\Data\Snap::A_DEPTH];
            return $result;
        });
        /* save customer data into repo */
        $dtChanged = \DateTime::createFromFormat('Ymd', '20170101');
        $timeStarted = $dtChanged->getTimestamp();
        foreach ($expandedTree as $item) {
            $custMlmId = $item[\Praxigento\Downline\Repo\Data\Snap::A_CUSTOMER_ID];
            $parentMlmId = $item[\Praxigento\Downline\Repo\Data\Snap::A_PARENT_ID];
            $depth = $item[\Praxigento\Downline\Repo\Data\Snap::A_DEPTH];
            $path = $item[\Praxigento\Downline\Repo\Data\Snap::A_PATH];
            $country = 'ES';
            /* get Mage IDs for MLM IDs */
            $cusMageId = $mapByMlmId[$custMlmId];
            $parentMageId = $mapByMlmId[$parentMlmId];
            $parts = explode(Cfg::DTPS, $path);
            $partsMapped = array_map(function ($mlmId) use ($mapByMlmId) {
                $result = isset($mapByMlmId[$mlmId]) ? $mapByMlmId[$mlmId] : '';
                return $result;
            }, $parts);
            $pathIds = implode(Cfg::DTPS, $partsMapped);
            $timeStarted += 600;
            $dtChanged->setTimestamp($timeStarted);
            $dateChanged = $this->hlpFormat->dateTimeForDb($dtChanged);
            /* add record to downline tree */
            $eCust = new \Praxigento\Downline\Repo\Data\Customer();
            $eCust->setCustomerId($cusMageId);
            $eCust->setParentId($parentMageId);
            $eCust->setDepth($depth);
            $eCust->setPath($pathIds);
            $eCust->setMlmId($custMlmId);
            $eCust->setReferralCode($custMlmId);
            $eCust->setCountryCode($country);
            $this->daoDwnlCust->create($eCust);
            /* add record to change log */
            $eChange = new \Praxigento\Downline\Repo\Data\Change();
            $eChange->setCustomerId($cusMageId);
            $eChange->setParentId($parentMageId);
            $eChange->setDateChanged($dateChanged);
            $this->daoDwnlChange->create($eChange);
        }
    }
}