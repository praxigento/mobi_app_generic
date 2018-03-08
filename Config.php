<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2;

use Praxigento\Accounting\Config as AccCfg;
use Praxigento\Pv\Config as PvCfg;
use Praxigento\Wallet\Config as WalletCfg;

class Config extends \Praxigento\Core\Config
{
    const CODE_TYPE_CALC_BONUS = \Praxigento\BonusLoyalty\Config::CODE_TYPE_CALC_BONUS;

    const CODE_TYPE_OPER_CHANGE_BALANCE = AccCfg::CODE_TYPE_OPER_CHANGE_BALANCE;
    const CODE_TYPE_OPER_PV_TRANSFER = PvCfg::CODE_TYPE_OPER_PV_TRANSFER;
    const CODE_TYPE_OPER_WALLET_SALE = WalletCfg::CODE_TYPE_OPER_WALLET_SALE;
    const CODE_TYPE_OPER_WALLET_TRANSFER = WalletCfg::CODE_TYPE_OPER_WALLET_TRANSFER;

    const DTPS = \Praxigento\Downline\Config::DTPS;
    const MODULE = 'Praxigento_AppGeneric2';
    const QUAL_LEVEL_GV = 2;
    const QUAL_LEVEL_PSAA = 10;
    const RANK_BY_GV = 'GV_QUALIFIED';
    const RANK_BY_PSAA = 'PSAA_QUALIFIED';
    const RANK_BY_PV = 'PV_QUALIFIED';
}