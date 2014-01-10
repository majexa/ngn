<?php

class TestLongJob extends ProjectTestCase {

  protected $jobIds = ['sample1', 'sample2', 'sample3', 'sample4'];

  function a() {
    // определяем время выполнения итерации работы
    $beforeIterationTime = getMicrotime();
    (new SampleLongJob)->iteration();
    $iterationTime = getMicrotime() - $beforeIterationTime;
    output3("current keys: ".implode(', ', array_keys(LongJobCore::states()->collection->r)));
    $ids = $this->jobIds;
    LongJobCore::states()->destroy(true);
    usleep($iterationTime * 1000000);
    foreach ($ids as $k) $this->assertTrue(LongJobCore::run(new SampleLongJob($k))); //
    usleep(0.1 * 1000000);
    foreach ($ids as $k) $this->assertFalse(LongJobCore::run(new SampleLongJob($k)));
    foreach ($ids as $k) {
      $status = LongJobCore::state($k)->status();
      output2("status after delay $k: $status");
      $this->assertFalse($status == 'starting', 'Status is "starting" after delay, Maybe worker is not running');
      $this->assertTrue($status == 'progress');
    }
    $this->assertTrue(count(LongJobCore::states()->collection) == count($ids));
    LongJobCore::states()->destroy();
    usleep($iterationTime * 1000000);
    $this->assertTrue(count(LongJobCore::states()->collection) == 0);
    foreach ($ids as $k) $this->assertTrue(LongJobCore::state($k)->status() === false, 'job deleted');
  }

  function test() {
    $qwi = new ProjectQueueWorkerInstaller;
    $qwi->install();
    $this->assertTrue(count($this->jobIds) == Config::getSubVar('queue', 'workers'));
    $this->a();
    $this->a();
    $qwi->uninstall();
  }

}