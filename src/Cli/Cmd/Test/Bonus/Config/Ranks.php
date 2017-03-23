<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Cli\Cmd\Test\Bonus\Config;


class Ranks
{
    const RANK_PV_QUAL = \Praxigento\App\Generic2\Cli\Cmd\Test\Bonus\Config::RANK_PV_QUAL;
    const RANK_GV_QUAL = \Praxigento\App\Generic2\Cli\Cmd\Test\Bonus\Config::RANK_GV_QUAL;
    const RANK_PSAA_QUAL = \Praxigento\App\Generic2\Cli\Cmd\Test\Bonus\Config::RANK_PSAA_QUAL;

    protected $repoRank;

    public function __construct(
        \Praxigento\BonusBase\Repo\Entity\IRank $repoRank
    ) {
        $this->repoRank = $repoRank;
    }

    public function addRanks()
    {
        $this->repoRank->getIdByCode(self::RANK_PV_QUAL);
    }
}