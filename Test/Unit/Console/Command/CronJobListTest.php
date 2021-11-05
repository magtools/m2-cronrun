<?php

namespace Mtools\Cronrun\Test\Unit\Console\Command;

use PHPUnit\Framework\TestCase;
use Mtools\CronRun\Console\Command\CronJobList;

class CronJobListTest extends TestCase
{

    protected $cronConfig;
    protected $objectManager;

    protected $testObject;
    protected $testReflection;

    protected function setUp():void
    {
        $this->testObject = new CronJobList(
            $this->getMockedDependency('cronConfig', 'Magento\Cron\Model\ConfigInterface'),
            $this->getMockedDependency('objectManager', 'Magento\Framework\ObjectManagerInterface'),
        );

        $this->testReflection = new \ReflectionClass(CronJobList::class);
    }

    /**
     * @param $propertyName
     * @param $className
     */
    protected function getMockedDependency($propertyName, $className)
    {
        if (empty($this->{$propertyName})) {
            $this->{$propertyName} = $this->getMockBuilder($className)
                ->disableOriginalConstructor()
                ->getMock();
        }
        return $this->{$propertyName};
    }

    /**
     * @test
     * @dataProvider dataProvider
     * @throws \ReflectionException
     */
    public function testSearchJobList($jobList,$findCronJob,$expected)
    {
        $method = $this->testReflection->getMethod('searchJobList');
        $method->setAccessible(true);

        $findMatch = false;
        $result = $method->invokeArgs($this->testObject,[$jobList,$findCronJob,&$findMatch]);
        $this->assertEquals($result,$expected);
    }

    /**
     * @return array[]
     */
    public function dataProvider()
    {
        return [
            'Empty cronjob list' => [
                [], '', []
            ],
            'Cronjob search without filter' => [
                ['cron_job_1','cron_job_2'], '', ['cron_job_1','cron_job_2']
            ],
            'Cronjob search with filter' => [
                ['cron_job_1','cron_job_2','cron_job_3'], 'job_2', ['cron_job_2']
            ],
        ];
    }
}
