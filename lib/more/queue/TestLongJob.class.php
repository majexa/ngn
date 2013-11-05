<?php

class TestLongJob extends ProjectTestCase {

  protected $jobIds = ['sample1', 'sample2', 'sample3', 'sample4'];

  function a() {
    // определяем время выполнения итерации работы
    $a = getMicrotime();
    (new SampleLongJob)->iteration();
    $iterationTime = getMicrotime() - $a;
    output3("current keys: ".implode(', ', array_keys(LongJobCore::states()->collection->r)));
    $a = $this->jobIds;
    LongJobCore::states()->destroy(true);
    usleep($iterationTime * 1000000);

    foreach ($a as $k) $this->assertTrue(LongJobCore::run(new SampleLongJob($k)));
    usleep(0.1 * 1000000);
    foreach ($a as $k) $this->assertFalse(LongJobCore::run(new SampleLongJob($k)));
    foreach ($a as $k) {
      $status = LongJobCore::state($k)->status();
      output2("status after delay $k: $status");
      $this->assertFalse($status == 'starting', 'Status is "starting" after delay, Maybe worker is not running');
      $this->assertTrue($status == 'progress');
    }
    $this->assertTrue(count(LongJobCore::states()->collection) == count($a));
    LongJobCore::states()->destroy();
    usleep($iterationTime * 1000000);
    $this->assertTrue(count(LongJobCore::states()->collection) == 0);
    foreach ($a as $k) $this->assertTrue(LongJobCore::state($k)->status() === false, 'job deleted');
  }

  function test() {
    $qwi = new QueueWorkerInstaller(PROJECT_KEY, count($this->jobIds));
    $qwi->install();
    $this->a();
    $this->a();
    $qwi->uninstall();
  }

}