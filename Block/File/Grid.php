<?php

namespace Mtools\AdminLog\Block\File;

use Mtools\AdminLog\Helper\Data;
use Magento\Framework\View\Element\Template\Context;

class Grid extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Data
     */
    protected $logFileHelper;

    /**
     * @param Context $context
     * @param Data $logFileHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $logFileHelper,
        array $data = []
    )
    {
        $this->logFileHelper = $logFileHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getLogFiles()
    {
        return $this->logFileHelper->buildLogData();
    }

    /**
     * @return string
     */
    public function getLogFile($filename)
    {
        return $this->logFileHelper->getLastLinesOfFile($filename, 100);
    }

    /**
     * @param $fileName
     *
     * @return string
     */
    public function downloadLogFiles($fileName)
    {
        return $this->getUrl('mtoolslogview/file/download', ['file' => $fileName]);
    }

    /**
     * @param $fileName
     *
     * @return string
     */
    public function previewLogFile($fileName)
    {
        return $this->getUrl('mtoolslogview/file/view', ['file' => $fileName]);
    }
}
