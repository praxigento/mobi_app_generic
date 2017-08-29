<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Cli\Cmd\Init;

use Praxigento\App\Generic2\Config as Cfg;
use Praxigento\Core\Tool\IPeriod;

/**
 * Initialize bonus parameters for Generic Application.
 *
 * @deprecated Use classes in Praxigento\App\Generic2\Cli\Cmd\Test\...
 */
class Bonus
    extends \Praxigento\Core\Cli\Cmd\Base
{
    /** @var  \Praxigento\Core\Transaction\Database\IManager */
    protected $_manTrans;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Transaction\Database\IManager $manTrans,
        \Praxigento\BonusBase\Service\IPeriod $callBonusPeriod,
        \Praxigento\Pv\Service\ISale $callPvSale
    ) {
        parent::__construct(
            $manObj,
            'prxgt:app:init:bonus',
            'Initialize bonus parameters for Generic Application.'
        );
        $this->_manTrans = $manTrans;
    }

    protected function _calcBonus()
    {
        /** @var \Praxigento\BonusLoyalty\Service\Calc\Request\Bonus $req */
        $req = new \Praxigento\BonusLoyalty\Service\Calc\Request\Bonus();
        /** @var \Praxigento\BonusLoyalty\Service\ICalc $call */
        $call = $this->manObj->get(\Praxigento\BonusLoyalty\Service\ICalc::class);
        $resp = $call->bonus($req);
        $calcId = $resp->getCalcId();
        return $calcId;
    }

    protected function _calcPeriod()
    {
        /** @var \Praxigento\BonusBase\Service\IPeriod $call */
        $call = $this->manObj->get(\Praxigento\BonusBase\Service\IPeriod::class);
        /** @var \Praxigento\BonusBase\Service\Period\Request\GetForPvBasedCalc $req */
        $req = new \Praxigento\BonusBase\Service\Period\Request\GetForPvBasedCalc();
        $req->setCalcTypeCode(Cfg::CODE_TYPE_CALC_BONUS);
        $req->setPeriodType(IPeriod::TYPE_DAY);
        $resp = $call->getForPvBasedCalc($req);
        $period = $resp->getPeriodData();
        $result = $period->getDstampEnd();
        return $result;
    }

    protected function _calcQualification()
    {
        /** @var \Praxigento\BonusLoyalty\Service\ICalc $call */
        $call = $this->manObj->get(\Praxigento\BonusLoyalty\Service\ICalc::class);
        /** @var \Praxigento\BonusLoyalty\Service\Calc\Request\Qualification $req */
        $req = new \Praxigento\BonusLoyalty\Service\Calc\Request\Qualification();
        $req->setGvMaxLevels(Cfg::QUAL_LEVEL_GV);
        $req->setPsaaLevel(Cfg::QUAL_LEVEL_PSAA);
        $resp = $call->qualification($req);
        $calcId = $resp->getCalcId();
        return $calcId;
    }

    protected function _calcTreeCompression()
    {
        /** @var \Praxigento\BonusLoyalty\Service\ICalc $call */
        $call = $this->manObj->get(\Praxigento\BonusLoyalty\Service\ICalc::class);
        $req = new \Praxigento\BonusLoyalty\Service\Calc\Request\Compress();
        $resp = $call->compress($req);
        $calcId = $resp->getCalcId();
        return $calcId;
    }

    protected function _calcTreeSnapshots($periodTo)
    {
        /** @var \Praxigento\Downline\Service\ISnap $call */
        $call = $this->manObj->get(\Praxigento\Downline\Service\ISnap::class);
        /** @var \Praxigento\Downline\Service\Snap\Request\Calc $req */
        $req = new \Praxigento\Downline\Service\Snap\Request\Calc();
        $req->setDatestampTo($periodTo);
        $call->calc($req);
    }

    protected function _initGenerationPercents()
    {
        try {
            /** @var \Praxigento\BonusBase\Repo\Entity\Type\Calc $repoCalc */
            $repoCalc = $this->manObj->get(\Praxigento\BonusBase\Repo\Entity\Type\Calc::class);
            /** @var \Praxigento\BonusBase\Repo\Entity\Rank $repoRank */
            $repoRank = $this->manObj->get(\Praxigento\BonusBase\Repo\Entity\Rank::class);
            /** @var \Praxigento\BonusBase\Repo\Entity\Cfg\Generation $repo */
            $repo = $this->manObj->get(\Praxigento\BonusBase\Repo\Entity\Cfg\Generation::class);
            /** @var \Praxigento\BonusBase\Repo\Entity\Data\Cfg\Generation $data */
            $data = new \Praxigento\BonusBase\Repo\Entity\Data\Cfg\Generation();
            // get calculation type ID
            $calcTypeId = $repoCalc->getIdByCode(Cfg::CODE_TYPE_CALC_BONUS);
            $data->setCalcTypeId($calcTypeId);
            //
            // PV rank
            //
            $id = $repoRank->getIdByCode(Cfg::RANK_BY_PV);
            $data->setRankId($id);
            //
            $data->setGeneration(1);
            $data->setPercent(0.2);
            $repo->create($data);
            //
            $data->setGeneration(2);
            $data->setPercent(0.15);
            $repo->create($data);
            //
            // GV rank
            //
            $id = $repoRank->getIdByCode(Cfg::RANK_BY_GV);
            $data->setRankId($id);
            //
            $data->setGeneration(1);
            $data->setPercent(0.2);
            $repo->create($data);
            //
            $data->setGeneration(2);
            $data->setPercent(0.15);
            $repo->create($data);
            //
            $data->setGeneration(3);
            $data->setPercent(0.1);
            $repo->create($data);
            //
            // PSAA rank
            //
            $id = $repoRank->getIdByCode(Cfg::RANK_BY_PSAA);
            $data->setRankId($id);
            //
            $data->setGeneration(1);
            $data->setPercent(0.2);
            $repo->create($data);
            //
            $data->setGeneration(2);
            $data->setPercent(0.15);
            $repo->create($data);
            //
            $data->setGeneration(3);
            $data->setPercent(0.1);
            $repo->create($data);
            //
            $data->setGeneration(4);
            $data->setPercent(0.05);
            $repo->create($data);
        } catch (\Exception $e) {
            // do nothing if data is already created
        }
    }

    protected function _initLoyaltyCfg()
    {
        try {
            /** @var \Praxigento\BonusBase\Repo\Entity\Rank $repoRank */
            $repoRank = $this->manObj->get(\Praxigento\BonusBase\Repo\Entity\Rank::class);
            /** @var \Praxigento\BonusLoyalty\Repo\Entity\Cfg\Param $repo */
            $repo = $this->manObj->get(\Praxigento\BonusLoyalty\Repo\Entity\Cfg\Param::class);
            /** @var \Praxigento\BonusLoyalty\Repo\Entity\Data\Cfg\Param $data */
            $data = new \Praxigento\BonusLoyalty\Repo\Entity\Data\Cfg\Param();
            //
            $id = $repoRank->getIdByCode(Cfg::RANK_BY_PV);
            $data->setRankId($id);
            $data->setPv(5);
            $repo->create($data);
            //
            $id = $repoRank->getIdByCode(Cfg::RANK_BY_GV);
            $data->setRankId($id);
            $data->setPv(5);
            $data->setGv(10);
            $repo->create($data);
            //
            $id = $repoRank->getIdByCode(Cfg::RANK_BY_PSAA);
            $data->setRankId($id);
            $data->setPv(5);
            $data->setGv(10);
            $data->setPsaa(2);
            $repo->create($data);
        } catch (\Exception $e) {
            // do nothing if data is already created
        }
    }

    /**
     * Create ranks for distributors.
     */
    protected function _initRanks()
    {
        try {
            /** @var \Praxigento\BonusBase\Repo\Entity\Rank $repo */
            $repo = $this->manObj->get(\Praxigento\BonusBase\Repo\Entity\Rank::class);
            /** @var \Praxigento\BonusBase\Repo\Entity\Data\Rank $data */
            $data = new \Praxigento\BonusBase\Repo\Entity\Data\Rank();
            // PV
            $data->setCode(Cfg::RANK_BY_PV);
            $data->setNote('Qualified by PV only.');
            $repo->create($data);
            // PV & GV
            $data->setCode(Cfg::RANK_BY_GV);
            $data->setNote('Qualified by PV & GV.');
            $repo->create($data);
            // PV, GV & PSAA
            $data->setCode(Cfg::RANK_BY_PSAA);
            $data->setNote('Qualified by PV, GV & PSAA.');
            $repo->create($data);
        } catch (\Exception $e) {
            // do nothing if data is already created
        }
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $def = $this->_manTrans->begin();
        try {
            /* init data */
            $this->_initRanks();
            $this->_initGenerationPercents();
            $this->_initLoyaltyCfg();

            /* calc bonus */
            $periodTo = $this->_calcPeriod();
            $this->_calcTreeSnapshots($periodTo);
            $this->_calcTreeCompression();
            $this->_calcQualification();
            $this->_calcBonus();

            $this->_manTrans->commit($def);
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->_manTrans->end($def);
        }
    }
}