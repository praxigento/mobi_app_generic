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
    /** @var \Praxigento\App\Generic2\Console\Command\Test\Downline\Init\CleanCustomers */
    protected $subClearCust;
    /** @var \Praxigento\App\Generic2\Console\Command\Test\Downline\Init\CreateCustomers */
    protected $subCreateCust;
    /** @var \Praxigento\App\Generic2\Console\Command\Test\Downline\Init\UpdateGroups */
    protected $subUpdateGroups;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $manObj,
        \Praxigento\App\Generic2\Console\Command\Test\Downline\Init\CleanCustomers $subClearCust,
        \Praxigento\App\Generic2\Console\Command\Test\Downline\Init\CreateCustomers $subCreateCust,
        \Praxigento\App\Generic2\Console\Command\Test\Downline\Init\UpdateGroups $subUpdateGroups
    ) {
        parent::__construct(
            $manObj,
            'prxgt:test:downline-init',
            'Initialize customers downline for integration testing.'
        );
        $this->subClearCust = $subClearCust;
        $this->subCreateCust = $subCreateCust;
        $this->subUpdateGroups = $subUpdateGroups;
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $succeed = false;
        $this->subUpdateGroups->do();
        $this->subClearCust->do();
        $this->subCreateCust->do();
        if ($succeed) {
            $output->writeln('<info>Command is completed.<info>');
        } else {
            $output->writeln('<info>Command is failed.<info>');
        }
    }
}