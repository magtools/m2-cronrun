<?php

namespace Mtools\AdminLog\Controller\Adminhtml\File;

use Zend_Filter_BaseName;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Controller\Adminhtml\System;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\Filesystem\DirectoryList;

class Download extends System
{

    /**
     * @var Context $context
     * @var FileFactory $fileFactory
     */
    protected $fileFactory;

    public function __construct(
        Context $context,
        FileFactory $fileFactory
    ) {
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $filePath = $this->getFilePathWithFile($this->getRequest()->getParam('file'));

        $filter   = new Zend_Filter_BaseName();
        $fileName = $filter->filter($filePath);
        try {
            return $this->fileFactory->create(
                $fileName,
                [
                    'type'  => 'filename',
                    'value' => $filePath
                ]
            );
        } catch (\Exception $e) {
            throw new NotFoundException(__($e->getMessage()));
        }
    }

    /**
     * @param $filename
     * @return string
     */
    protected function getFilePathWithFile($fileName)
    {
        $path = DirectoryList::getDefaultConfig()[DirectoryList::LOG];

        return reset($path) . DS . $fileName;
    }

    /**
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Mtools_AdminLog::logfiles_download');
    }
}
