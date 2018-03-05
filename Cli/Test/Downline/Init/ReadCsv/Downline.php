<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */
namespace Praxigento\App\Generic2\Cli\Test\Downline\Init\ReadCsv;

/**
 * Read CSV with downline tree data exported from bonus module and return .
 */
class Downline
{
    /** Add traits */
    use \Praxigento\App\Generic2\Cli\Test\Traits\TReadCsv {
        readCsvFile as protected;
    }

    const A_CUST_ID = \Praxigento\App\Generic2\Cli\Test\Downline\Init::A_CUST_MLM_ID;
    const A_EMAIL = \Praxigento\App\Generic2\Cli\Test\Downline\Init::A_EMAIL;
    const A_PARENT_ID = \Praxigento\App\Generic2\Cli\Test\Downline\Init::A_PARENT_MLM_ID;

    public function do()
    {
        $csv = $this->readCsvFile($path = __DIR__ . '/../../../data/downline.csv', 0);
        $result = [];
        foreach ($csv as $item) {
            /* extract read data */
            $custMlmId = $item[0];
            $parentMlmId = $item[1];
            $scheme = $item[2]; // TODO: use it or remove it
            /* compose working data */
            $email = "$custMlmId-migrate@praxigento.com";
            if (strlen($parentMlmId) == 0) {
                /* this is root node */
                $parentMlmId = $custMlmId;
            }
            $result[$custMlmId] = [
                self::A_CUST_ID => $custMlmId,
                self::A_PARENT_ID => $parentMlmId,
                self::A_EMAIL => $email
            ];
        }
        return $result;
    }
}