<?php

namespace Mtools\CronRun\Controller\Adminhtml\Schedule;

use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\Page as FrameworkPage;
use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

class Grid extends \Magento\Backend\App\Action
{
    /**
     * @const acl
     */
    const ADMIN_RESOURCE = 'Mtools_CronRun::cron_scheduled';

    /**
     * @var Page
     */
    protected $resultPage;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param Page $resultPage
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        Page $resultPage,
        PageFactory $resultPageFactory
    ) {
        $this->resultPage = $resultPage;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }

    /**
     * @return Page|FrameworkPage
     */
    public function getResultPage()
    {
        if ($this->resultPage === null) {
            $this->resultPage = $this->resultPageFactory->create();
        }
        return $this->resultPage;
    }

    /**
     * @return $this
     */
    protected function setPageData()
    {
        $resultPage = $this->getResultPage();
        $resultPage->getConfig()->getTitle()->prepend((__('CronJobs Schedule')));
        return $this;
    }
}
