<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Console\Command\Init;

use Praxigento\App\Generic2\Config as Cfg;
use Praxigento\Core\Tool\IPeriod;

/**
 * Initialize bonus parameters for Generic Application.
 */
class Bonus
    extends \Praxigento\App\Generic2\Console\Command\Init\Base
{
    /** @var \Praxigento\BonusBase\Service\IPeriod */
    protected $_callBonusPeriod;
    /** @var \Praxigento\Pv\Service\ISale */
    protected $_callPvSale;
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
            'prxgt:app:init-bonus',
            'Initialize bonus parameters for Generic Application.'
        );
        $this->_manTrans = $manTrans;
        $this->_callBonusPeriod = $callBonusPeriod;
        $this->_callPvSale = $callPvSale;
    }

    protected function _initGenerationPercents()
    {
        try {
            /** @var \Praxigento\BonusBase\Repo\Entity\Type\ICalc $repoCalc */
            $repoCalc = $this->_manObj->get(\Praxigento\BonusBase\Repo\Entity\Type\ICalc::class);
            /** @var \Praxigento\BonusBase\Repo\Entity\IRank $repoRank */
            $repoRank = $this->_manObj->get(\Praxigento\BonusBase\Repo\Entity\IRank::class);
            /** @var \Praxigento\BonusBase\Repo\Entity\Cfg\IGeneration $repo */
            $repo = $this->_manObj->get(\Praxigento\BonusBase\Repo\Entity\Cfg\IGeneration::class);
            /** @var \Praxigento\BonusBase\Data\Entity\Cfg\Generation $data */
            $data = $this->_manObj->create(\Praxigento\BonusBase\Data\Entity\Cfg\Generation::class);
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
            /** @var \Praxigento\BonusBase\Repo\Entity\IRank $repoRank */
            $repoRank = $this->_manObj->get(\Praxigento\BonusBase\Repo\Entity\IRank::class);
            /** @var \Praxigento\BonusLoyalty\Repo\Entity\Cfg\IParam $repo */
            $repo = $this->_manObj->get(\Praxigento\BonusLoyalty\Repo\Entity\Cfg\IParam::class);
            /** @var \Praxigento\BonusLoyalty\Data\Entity\Cfg\Param $data */
            $data = $this->_manObj->create(\Praxigento\BonusLoyalty\Data\Entity\Cfg\Param::class);
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
            /** @var \Praxigento\BonusBase\Repo\Entity\IRank $repo */
            $repo = $this->_manObj->get(\Praxigento\BonusBase\Repo\Entity\IRank::class);
            /** @var \Praxigento\BonusBase\Data\Entity\Rank $data */
            $data = $this->_manObj->create(\Praxigento\BonusBase\Data\Entity\Rank::class);
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

    /**
     * Create type of the bonus calculation.
     */
    protected function _initTypeCalc()
    {
        try {
            /** @var \Praxigento\BonusBase\Repo\Entity\Type\ICalc $repo */
            $repo = $this->_manObj->get(\Praxigento\BonusBase\Repo\Entity\Type\ICalc::class);
            /** @var \Praxigento\BonusBase\Data\Entity\Type\Calc $data */
            $data = $this->_manObj->create(\Praxigento\BonusBase\Data\Entity\Type\Calc::class);
            $data->setCode(Cfg::CODE_TYPE_CALC_BONUS);
            $data->setNote('Bonus for Generic App');
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
            $this->_initTypeCalc();
            $this->_initRanks();
            $this->_initGenerationPercents();
            $this->_initLoyaltyCfg();

            /** @var \Praxigento\BonusBase\Service\Period\Request\GetForPvBasedCalc $req */
            $req = $this->_manObj->create(\Praxigento\BonusBase\Service\Period\Request\GetForPvBasedCalc::class);
            $req->setCalcTypeCode(Cfg::CODE_TYPE_CALC_BONUS);
            $req->setPeriodType(IPeriod::TYPE_DAY);
            $resp = $this->_callBonusPeriod->getForPvBasedCalc($req);


            $this->_manTrans->commit($def);
        } finally {
            // transaction will be rolled back if commit is not done (otherwise - do nothing)
            $this->_manTrans->end($def);
        }
    }

}