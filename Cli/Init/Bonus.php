<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Cli\Init;

use Praxigento\App\Generic2\Config as Cfg;

/**
 * Initialize bonus parameters for Generic Application.
 *
 * @deprecated Use classes in Praxigento\App\Generic2\Cli\Test\...
 */
class Bonus
    extends \Praxigento\Core\App\Cli\Cmd\Base
{
    /** @var  \Praxigento\Core\Api\App\Repo\Transaction\Manager */
    protected $_manTrans;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\Core\Api\App\Repo\Transaction\Manager $manTrans,
        \Praxigento\BonusBase\Service\IPeriod $callBonusPeriod
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
        $req->setPeriodType(HPeriod::TYPE_DAY);
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
            /** @var \Praxigento\BonusBase\Repo\Dao\Type\Calc $daoCalc */
            $daoCalc = $this->manObj->get(\Praxigento\BonusBase\Repo\Dao\Type\Calc::class);
            /** @var \Praxigento\BonusBase\Repo\Dao\Rank $daoRank */
            $daoRank = $this->manObj->get(\Praxigento\BonusBase\Repo\Dao\Rank::class);
            /** @var \Praxigento\BonusBase\Repo\Dao\Cfg\Generation $dao */
            $dao = $this->manObj->get(\Praxigento\BonusBase\Repo\Dao\Cfg\Generation::class);
            /** @var \Praxigento\BonusBase\Repo\Data\Cfg\Generation $data */
            $data = new \Praxigento\BonusBase\Repo\Data\Cfg\Generation();
            // get calculation type ID
            $calcTypeId = $daoCalc->getIdByCode(Cfg::CODE_TYPE_CALC_BONUS);
            $data->setCalcTypeId($calcTypeId);
            //
            // PV rank
            //
            $id = $daoRank->getIdByCode(Cfg::RANK_BY_PV);
            $data->setRankId($id);
            //
            $data->setGeneration(1);
            $data->setPercent(0.2);
            $dao->create($data);
            //
            $data->setGeneration(2);
            $data->setPercent(0.15);
            $dao->create($data);
            //
            // GV rank
            //
            $id = $daoRank->getIdByCode(Cfg::RANK_BY_GV);
            $data->setRankId($id);
            //
            $data->setGeneration(1);
            $data->setPercent(0.2);
            $dao->create($data);
            //
            $data->setGeneration(2);
            $data->setPercent(0.15);
            $dao->create($data);
            //
            $data->setGeneration(3);
            $data->setPercent(0.1);
            $dao->create($data);
            //
            // PSAA rank
            //
            $id = $daoRank->getIdByCode(Cfg::RANK_BY_PSAA);
            $data->setRankId($id);
            //
            $data->setGeneration(1);
            $data->setPercent(0.2);
            $dao->create($data);
            //
            $data->setGeneration(2);
            $data->setPercent(0.15);
            $dao->create($data);
            //
            $data->setGeneration(3);
            $data->setPercent(0.1);
            $dao->create($data);
            //
            $data->setGeneration(4);
            $data->setPercent(0.05);
            $dao->create($data);
        } catch (\Exception $e) {
            // do nothing if data is already created
        }
    }

    protected function _initLoyaltyCfg()
    {
        try {
            /** @var \Praxigento\BonusBase\Repo\Dao\Rank $daoRank */
            $daoRank = $this->manObj->get(\Praxigento\BonusBase\Repo\Dao\Rank::class);
            /** @var \Praxigento\BonusLoyalty\Repo\Dao\Cfg\Param $dao */
            $dao = $this->manObj->get(\Praxigento\BonusLoyalty\Repo\Dao\Cfg\Param::class);
            /** @var \Praxigento\BonusLoyalty\Repo\Data\Cfg\Param $data */
            $data = new \Praxigento\BonusLoyalty\Repo\Data\Cfg\Param();
            //
            $id = $daoRank->getIdByCode(Cfg::RANK_BY_PV);
            $data->setRankId($id);
            $data->setPv(5);
            $dao->create($data);
            //
            $id = $daoRank->getIdByCode(Cfg::RANK_BY_GV);
            $data->setRankId($id);
            $data->setPv(5);
            $data->setGv(10);
            $dao->create($data);
            //
            $id = $daoRank->getIdByCode(Cfg::RANK_BY_PSAA);
            $data->setRankId($id);
            $data->setPv(5);
            $data->setGv(10);
            $data->setPsaa(2);
            $dao->create($data);
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
            /** @var \Praxigento\BonusBase\Repo\Dao\Rank $dao */
            $dao = $this->manObj->get(\Praxigento\BonusBase\Repo\Dao\Rank::class);
            /** @var \Praxigento\BonusBase\Repo\Data\Rank $data */
            $data = new \Praxigento\BonusBase\Repo\Data\Rank();
            // PV
            $data->setCode(Cfg::RANK_BY_PV);
            $data->setNote('Qualified by PV only.');
            $dao->create($data);
            // PV & GV
            $data->setCode(Cfg::RANK_BY_GV);
            $data->setNote('Qualified by PV & GV.');
            $dao->create($data);
            // PV, GV & PSAA
            $data->setCode(Cfg::RANK_BY_PSAA);
            $data->setNote('Qualified by PV, GV & PSAA.');
            $dao->create($data);
        } catch (\Exception $e) {
            // do nothing if data is already created
        }
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $this->checkAreaCode();
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