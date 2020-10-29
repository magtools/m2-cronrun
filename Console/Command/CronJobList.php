<?php

namespace Mtools\CronRun\Console\Command;

use Magento\Framework\ObjectManagerInterface;
use Magento\Cron\Model\ConfigInterface as CronConfigInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class CronJobList extends Command
{
    /**
     * @const
     */
    const MTOOLS_CRON_ARGUMENT = 'cronjob';

    /**
     * @var array
     */
    protected $cronJobList;

    /**
     * @var CronConfigInterface
     */
    protected $cronConfig;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param CronConfigInterface    $cronConfig
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct (
        CronConfigInterface $cronConfig,
        ObjectManagerInterface $objectManager
    ) {
        $this->cronConfig = $cronConfig;
        $this->objectManager = $objectManager;

        parent::__construct();
    }

    protected function configure()
    {
        $argument = new InputArgument(self::MTOOLS_CRON_ARGUMENT, InputArgument::OPTIONAL, 'Allow search by partial cron_job');
        $this->setName('cron:job:list')
            ->setDescription('List cron jobs')
            ->setDefinition([$argument]);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $cronGroups = $this->cronConfig->getJobs();
        $findCronJob = $input->getArgument(self::MTOOLS_CRON_ARGUMENT);
        $findMatch = false;
        $step = false;

        $output->writeln('<info>Cron Job List:</info>');
        foreach ($cronGroups as $group) {
            foreach ($group as $cronJob) {
                if (empty($cronJob['name'])) {
                    continue;
                }
                if (empty($findCronJob)) {
                    $output->writeln($cronJob['name']);
                } else if ( strripos($cronJob['name'], $findCronJob) !== false ) {
                    $output->writeln($cronJob['name']);
                    $findMatch = true;
                }
            }
        }

        if (!$findMatch && !empty($findCronJob)) {
            $output->writeln( '<error>Search did not match any result.</error>');
        }
    }
}
