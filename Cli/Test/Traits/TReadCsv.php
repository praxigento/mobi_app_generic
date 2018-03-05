<?php
/**
 * User: Alex Gusev <alex@flancer64.com>
 */

namespace Praxigento\App\Generic2\Cli\Test\Traits;


trait TReadCsv
{
    /**
     * Read CSV file and put content into simple array or array with keys (if $idColumnIdx is not null).
     *
     * @param $path path to the CSV file (relative or absolute).
     * @param null $keyIndex index of the ID column.
     * @param bool $skipFirstRow 'true' skip first row (header)
     * @return array
     */
    function readCsvFile($path, $keyIndex = null, $skipFirstRow = true)
    {
        $result = [];
        $file = fopen($path, 'r');
        /* skip first row with header */
        if ($skipFirstRow) fgetcsv($file);
        $useKey = !is_null($keyIndex);
        while ($row = fgetcsv($file)) {
            if ((count($row) > 0) && !is_null($row[0])) {
                if ($useKey) {
                    $key = $row[$keyIndex];
                    $result[$key] = $row;
                } else {
                    $result[] = $row;
                }

            }
        }
        return $result;
    }
}