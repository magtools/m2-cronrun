<?php

namespace Mtools\CronRun\Ui\Component\Listing\Column;

use Magento\Cron\Model\Schedule;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class ColorStatus extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @param UiComponentFactory $uiComponentFactory
     * @param ContextInterface   $context
     * @param array              $components
     * @param array              $data
     */
    public function __construct(
        UiComponentFactory $uiComponentFactory,
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
    }

    /**
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $severity = "minor pending"; //Schedule::STATUS_PENDING
                $severity = ($item['status'] == Schedule::STATUS_RUNNING) ? 'notice running' : $severity;
                $severity = ($item['status'] == Schedule::STATUS_SUCCESS) ? 'notice success' : $severity;
                $severity = ($item['status'] == Schedule::STATUS_MISSED) ? 'minor missed' : $severity;
                $severity = ($item['status'] == Schedule::STATUS_ERROR) ? 'major' : $severity;

                $item['status'] = '<span class="grid-severity-'. $severity .'">'.  $item['status'] .'</span>';
            }
        }

        return $dataSource;
    }
}
