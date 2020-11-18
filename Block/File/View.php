<?php

namespace Mtools\AdminLog\Block\File;

use Mtools\AdminLog\Helper\Data;
use Mtools\AdminLog\Helper\ReadLogFileTrait;
use Magento\Framework\View\Element\Template\Context;

class View extends \Magento\Framework\View\Element\Template
{
    use ReadLogFileTrait {
        fetch as fetchLogFileBlocks;
    }

    /**
     * @var Data
     */
    protected $logFileHelper;

    /**
     * View constructor.
     *
     * @param Context $context
     * @param Data    $logFileHelper
     * @param array   $data
     */
    public function __construct(
        Context $context,
        Data $logFileHelper,
        array $data = []
    ) {
        $this->logFileHelper = $logFileHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getLogFile()
    {
        return $this->logFileHelper->getLastLinesOfFile($this->getFileName(), 100);
    }

    /**
     * @return array
     */
    public function getLogFileBlocks(): array
    {
        return $this->fetchLogFileBlocks($this->logFile(), $this->getLimit(), $this->getStart());
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return (int) $this->getRequest()->getParam('limit', 100) ?: 100;
    }

    /**
     * @return int
     */
    public function getStart(): int
    {
        return (int) $this->getRequest()->getParam('start', 0);
    }

    /**
     * @return mixed
     */
    public function getFileName()
    {
        return $this->getRequest()->getParam('file');
    }

    /**
     * @param int $limit
     * @return string
     */
    public function getLimitUrl(int $limit): string
    {
        return $this->getUrl('*/*/*', [
            '_current' => true,
            'limit'    => $limit,
            'file'     => $this->getFileName(),
        ]);
    }

    /**
     * @param int $start
     * @return string
     */
    public function getStartUrl(int $start): string
    {
        return $this->getUrl('*/*/*', [
            '_current'     => true,
            'start'        => $start,
            'file'         => $this->getFileName(),
        ]);
    }

    /**
     * Get back URL
     *
     * @return string
     */
    public function getBackUrl(): string
    {
        return $this->getUrl('*/*/grid', ['_current' => true]);
    }

    /**
     * @param int $max
     * @return array
     */
    public function getStarts($max = 10)
    {
        $start = $this->getStart() - $this->getLimit() * 2;
        $start = $start > 0 ? $start : 0;
        if ($start > $this->getLimit() * 3) {
            $step = ceil($start / 4);
            $step -= $step % $this->getLimit();

            return array_merge(
                range(0, $start - $this->getLimit(), $step),
                range($start, $this->getLimit() * ($max - 1) + $start, $this->getLimit())
            );
        }

        return range(0, $this->getLimit() * ($max - 1) + $start, $this->getLimit());
    }

    /**
     * @return array
     */
    public function getLimits()
    {
        return [100, 500, 1000];
    }

    /**
     * @return string
     */
    private function logFile(): string
    {
        return $this->logFileHelper->getLogPath().DIRECTORY_SEPARATOR.$this->getFileName();
    }
}
