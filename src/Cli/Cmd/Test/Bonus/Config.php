<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Cli\Cmd\Test\Bonus;


/**
 * Configure bonus calculations for integration testing.
 */
class Config
    extends \Praxigento\Core\App\Cli\Cmd\Base
{

    const RANK_PV_QUAL = 'PV_QUALIFIED';
    const RANK_GV_QUAL = 'GV_QUALIFIED';
    const RANK_PSAA_QUAL = 'PSAA_QUALIFIED';

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj
    ) {
        parent::__construct(
            $manObj,
            'prxgt:test:bonus-config',
            'Configure bonus calculations for integration testing.'
        );
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $succeed = false;
        if ($succeed) {
            $output->writeln('<info>Command is completed.<info>');
        } else {
            $output->writeln('<info>Command is failed.<info>');
        }
    }
}