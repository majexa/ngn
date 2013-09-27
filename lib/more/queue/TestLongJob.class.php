<?php

class TestLongJob extends ProjectTestCase {

  protected $jobIds = ['sample1', 'sample2', 'sample3', 'sample4'];

  /*
  function a() {
    output3("current keys: ".implode(', ', array_keys(LongJobCore::states()->collection->r)));
    LongJobCore::run(new SampleLongJob('sample'));
    usleep(0.1 * 1000000);
    $status = LongJobCore::state('sample')->status();
    output2("status after delay: $status");
    $this->assertFalse($status == 'starting', 'Status is "starting" after delay, Maybe worker is not running');
    LongJobCore::states()->destroy();
    usleep(0.1 * 1000000);
    $this->assertTrue(LongJobCore::state('sample')->status() === false, 'job deleted');
  }
  function b() {
    $a = $this->jobIds;
    //LongJobCore::states()->destroy();
    foreach ($a as $k) LongJobCore::state($k)->delete(true);
    usleep(0.1 * 1000000);
    foreach ($a as $k) $this->assertTrue(LongJobCore::run(new SampleLongJob($k)), "$k added successfully");
    usleep(0.1 * 1000000);
    foreach ($a as $k) $this->assertFalse(LongJobCore::run(new SampleLongJob($k)), "$k added successfully");
    $this->assertTrue(count(LongJobCore::states()->collection) == count($a));
    LongJobCore::states()->destroy();
    $this->assertTrue(count(LongJobCore::states()->collection) == 0);
    usleep(0.1 * 1000000);
    $this->assertTrue(count(LongJobCore::states()->collection) == 0);
    foreach ($a as $k) $this->assertFalse(LongJobCore::state($k)->status());
    foreach ($a as $k) $this->assertTrue(LongJobCore::run(new SampleLongJob($k)), "$k added successfully");
    usleep(0.2 * 1000000);
    foreach ($a as $k) print LongJobCore::state($k)->status().', ';
    print "\n";
    //$this->assertTrue(LongJobCore::state($k)->status() == 'progress', "$k status = progress");
    return;
    LongJobCore::states()->destroy();
    usleep(0.1 * 1000000); // подождать пока удалиться
    foreach ($a as $k) $this->assertTrue(LongJobCore::state($k)->status() === false, "$k status = false");
  }
  */

  function c() {
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
    $this->c();
    $this->c();
    $qwi->uninstall();
  }

}