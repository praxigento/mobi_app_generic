<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2;

class Config extends \Praxigento\Core\Config
{
    const CODE_TYPE_CALC_BONUS = \Praxigento\BonusLoyalty\Config::CODE_TYPE_CALC_BONUS;
    const DTPS = \Praxigento\Downline\Config::DTPS;
    const MODULE = 'Praxigento_AppGeneric2';
    const QUAL_LEVEL_GV = 2;
    const QUAL_LEVEL_PSAA = 10;

    const RANK_BY_GV = 'GV_QUALIFIED';
    const RANK_BY_PSAA = 'PSAA_QUALIFIED';
    const RANK_BY_PV = 'PV_QUALIFIED';
}