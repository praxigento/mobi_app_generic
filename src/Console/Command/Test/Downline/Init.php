<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Console\Command\Test\Downline;


/**
 * Initialize customers downline for integration testing.
 */
class Init
    extends \Praxigento\Core\Console\Command\Base
{
    const A_CUST_MLM_ID = 'cust_mlm_id';
    const A_DATE_ENROLLED = 'date_enrolled';
    const A_EMAIL = 'email';
    const A_PARENT_MLM_ID = 'parent_mlm_id';
    /** @var \Praxigento\App\Generic2\Console\Command\Test\Downline\Init\UpdateGroups */
    protected $subUpdateGroups;
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\App\Generic2\Console\Command\Test\Downline\Init\UpdateGroups $subUpdateGroups
    ) {
        parent::__construct(
            $manObj,
            'prxgt:test:downline-init',
            'Initialize customers downline for integration testing.'
        );
        $this->subUpdateGroups = $subUpdateGroups;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $succeed = false;
        $this->subUpdateGroups->do();
        if ($succeed) {
            $output->writeln('<info>Command is completed.<info>');
        } else {
            $output->writeln('<info>Command is failed.<info>');
        }
    }
}