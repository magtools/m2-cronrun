<?php

namespace Mtools\CronRun\Console\Command;

use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Framework\ObjectManagerInterface;
use Magento\Cron\Model\ConfigInterface as CronConfigInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class CronJobRun extends Command
{
    /**
     * @const
     */
    const MTOOLS_CRON_ARGUMENT = 'cronjob';

    /**
     * @var CronConfigInterface
     */
    protected $cronConfig;

    /**
     * @var Area
     */
    protected $area;

    /**
     * @var State
     */
    protected $state;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param Area                   $area
     * @param State                  $state
     * @param CronConfigInterface    $cronConfig
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(
        Area $area,
        State $state,
        CronConfigInterface $cronConfig,
        ObjectManagerInterface $objectManager
    )
    {
        $this->area = $area;
        $this->state = $state;
        $this->cronConfig = $cronConfig;
        $this->objectManager = $objectManager;

        parent::__construct();
    }

    /**
     * @param void
     */
    protected function configure()
    {
        $argument = new InputArgument(self::MTOOLS_CRON_ARGUMENT, InputArgument::REQUIRED, 'The cron_job name');
        $this->setName('cron:job:run')
            ->setDescription('Run cron by cron_job')
            ->setDefinition([$argument]);
        parent::configure();
    }


    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cronArgument = $input->getArgument(self::MTOOLS_CRON_ARGUMENT);
        $cronConfig = $this->validateJob($cronArgument);
        if (!$cronConfig) {
            throw new \Exception('CronJob does not exists');
        }
        $this->runCronJob($cronConfig);
        $output->writeln('<info>' . 'CronJob was executed.' . '</info>');
    }

    /**
     * @param string $cronjob
     *
     * @return string $cronConfig
     */
    protected function validateJob($cronjob)
    {
        $cronGroups = $this->cronConfig->getJobs();
        foreach ($cronGroups as $jobGroup) {
            if (array_key_exists($cronjob, $jobGroup)) {
                return $jobGroup[$cronjob];
            }
        }
    }

    /**
     * @param string $cronConfig
     *
     * @return void
     * @throws \Exception
     */
    protected function runCronJob($cronConfig)
    {
        $this->state->setAreaCode($this->area::AREA_CRONTAB);

        if (!isset($cronConfig['instance'], $cronConfig['method'])) {
            throw new \Exception('No callbacks found');
        }

        $cronObject = $this->objectManager->create($cronConfig['instance']);
        $cronRun = $cronConfig['method'];
        $callback = [$cronObject, $cronConfig['method']];

        try {
            if (!is_callable($callback)) {
                throw new \Exception(
                    __('Cron: %s::%s can\'t run', $cronConfig['instance'], $cronConfig['method'])
                );
            }
            $cronObject->$cronRun();
        } catch (\Exception $e) {
            throw $e;
        }
    }
}