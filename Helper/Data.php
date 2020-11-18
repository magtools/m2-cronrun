<?php

namespace Mtools\AdminLog\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends AbstractHelper
{
    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @param Context       $context
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Context $context,
        DirectoryList $directoryList
    ) {
        $this->directoryList = $directoryList;
        parent::__construct($context);
    }

    /**
     * @return string
     */
    public function getLogPath()
    {
        return $this->directoryList->getPath('log');
    }

    /**
     * @var string
     * @return array
     */
    protected function getLogFiles($path)
    {
        $list = scandir($path);
        array_splice($list, 0, 2);

        $result = [];
        foreach ($list as $index => $file) {
            if (is_dir($path.DIRECTORY_SEPARATOR.$file)) {
                foreach ($this->getLogFiles($path.DIRECTORY_SEPARATOR.$file) as $childFile) {
                    $result[] = $file.DIRECTORY_SEPARATOR.$childFile;
                }
            } else {
                $result[] = $file;
            }
        }

        return $result;
    }

    /**
     * @param $bytes
     * @param int $precision
     * @return string
     */
    protected function filesizeToReadableString($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * @return array
     */
    public function buildLogData()
    {
        $maxNumOfLogs = 30;
        $logFileData = [];
        $path = $this->getLogPath().DIRECTORY_SEPARATOR;

        //build log data into array
        foreach ($this->getLogFiles($this->getLogPath()) as $file) {
            $logFileData[$file]['name'] = $file;
            $logFileData[$file]['filesize'] = $this->filesizeToReadableString((filesize($path . $file)));
            $logFileData[$file]['modTime'] = filemtime($path . $file);
            $logFileData[$file]['modTimeLong'] = date("F d Y H:i:s.", filemtime($path . $file));
        }

        //sort array by modified time
        usort($logFileData, function ($item1, $item2) {
            return $item2['modTime'] <=> $item1['modTime'];
        });

        //limit the amount of log data $maxNumOfLogs
        $logFileData = array_slice($logFileData, 0, $maxNumOfLogs);

        return $logFileData;
    }

    /**
     * @param $fileName
     * @param $numOfLines
     *
     * @return string
     */
    public function getLastLinesOfFile($fileName, $numOfLines = 100)
    {
        $path = $this->getLogPath();
        $fullPath = $path . DS . $fileName;

        return $this->getTailCustom($fullPath, $numOfLines);
    }

    /**
     * @param      $filepath
     * @param int  $lines
     * @param bool $adaptive
     *
     * @return false|string
     */
    protected function getTailCustom($filepath, $lines = 1, $adaptive = true) {

        // Open file
        $f = @fopen($filepath, "rb");
        if ($f === false) return false;

        // Sets buffer size, according to the number of lines to retrieve.
        // This gives a performance boost when reading a few lines from the file.
        if (!$adaptive) $buffer = 4096;
        else $buffer = ($lines < 2 ? 64 : ($lines < 10 ? 512 : 4096));

        // Jump to last character
        fseek($f, -1, SEEK_END);

        // Read it and adjust line number if necessary
        // (Otherwise the result would be wrong if file doesn't end with a blank line)
        if (fread($f, 1) != "\n") $lines -= 1;

        // Start reading
        $output = '';
        $chunk = '';

        // While we would like more
        while (ftell($f) > 0 && $lines >= 0) {

            // Figure out how far back we should jump
            $seek = min(ftell($f), $buffer);

            // Do the jump (backwards, relative to where we are)
            fseek($f, -$seek, SEEK_CUR);

            // Read a chunk and prepend it to our output
            $output = ($chunk = fread($f, $seek)) . $output;

            // Jump back to where we started reading
            fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);

            // Decrease our line counter
            $lines -= substr_count($chunk, "\n");

        }

        // While we have too many lines
        // (Because of buffer size we might have read too many)
        while ($lines++ < 0) {

            // Find first newline and remove all text before that
            $output = substr($output, strpos($output, "\n") + 1);

        }

        // Close file and return
        fclose($f);
        return trim($output);

    }
}
