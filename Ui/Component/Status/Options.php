<?php

namespace Mtools\CronRun\Ui\Component\Status;

use Magento\Cron\Model\Schedule;

class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    /**
     * @return array|array[]
     */
    public function toOptionArray()
    {
        return [
                ['value' => Schedule::STATUS_ERROR,   'label' => Schedule::STATUS_ERROR],
                ['value' => Schedule::STATUS_MISSED,  'label' => Schedule::STATUS_MISSED],
                ['value' => Schedule::STATUS_PENDING, 'label' => Schedule::STATUS_PENDING],
                ['value' => Schedule::STATUS_RUNNING, 'label' => Schedule::STATUS_RUNNING],
                ['value' => Schedule::STATUS_SUCCESS, 'label' => Schedule::STATUS_SUCCESS]
        ];
    }
}
