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
    public const MTOOLS_CRON_ARGUMENT = 'cronjob';

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
    public function __construct(
        CronConfigInterface $cronConfig,
        ObjectManagerInterface $objectManager
    ) {
        $this->cronConfig = $cronConfig;
        $this->objectManager = $objectManager;

        parent::__construct();
    }

    /**
     * Configure job
     */
    protected function configure()
    {
        $argument = new InputArgument(
            self::MTOOLS_CRON_ARGUMENT,
            InputArgument::OPTIONAL,
            'Allow search by partial cron_job'
        );
        $this->setName('cron:job:list')
            ->setDescription('List cron jobs')
            ->setDefinition([$argument]);

        parent::configure();
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $findMatch = false;
        $cronGroups = $this->cronConfig->getJobs();
        $findCronJob = $input->getArgument(self::MTOOLS_CRON_ARGUMENT);

        $jobList = [];
        foreach ($cronGroups as $group) {
            foreach ($group as $cronJob) {
                if (!empty($cronJob['name'])) {
                    $jobList[] = $cronJob['name'];
                }
            }
        }
        $output->writeln('<info>Cron Job List:</info>');
        foreach ($this->searchJobList($jobList, $findCronJob, $findMatch) as $cronJobName) {
            $output->writeln($cronJobName);
        }

        if (!$findMatch && !empty($findCronJob)) {
            $output->writeln('<error>Search did not match any result.</error>');
        }

        // Return an integer value indicating the status of the command
        return Command::SUCCESS; // or return 0; for success
    }

    /**
     * @param $jobList
     * @param $findCronJob
     * @param $findMatch
     *
     * @return array
     */
    protected function searchJobList($jobList, $findCronJob, &$findMatch)
    {
        if (!empty($findCronJob)) {
            $jobFilteredList = [];
            foreach ($jobList as $cronJobName) {
                if (strripos($cronJobName, $findCronJob) !== false) {
                    $jobFilteredList[] = $cronJobName;
                    $findMatch = true;
                }
            }
            $jobList = $jobFilteredList;
        }
        return $jobList;
    }
}
