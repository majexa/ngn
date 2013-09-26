<?php

class TestLongJob extends ProjectTestCase {

  function test() {
    (new QueueWorker)->install(PROJECT_KEY);

    $this->assertTrue(LongJobCore::run(new SampleLongJob), 'Added successfully');
    $this->assertFalse(LongJobCore::run(new SampleLongJob), 'Can not add twice');
    usleep(1.1 * 1000000);
    $this->assertTrue(count(new LongJobStates) == 1, '1 job total in states');
    $this->assertTrue(LongJobCore::state('ljsample')->status() == 'complete', 'status changed after necessary time');

    LongJobCore::run(new SampleLongJob);
    $this->assertTrue(LongJobCore::state('ljsample')->all()['status'] == 'progress');
    usleep(0.01 * 1000000);
    $this->assertTrue(LongJobCore::state('ljsample')->all()['percentage'] > 0);
    $this->assertTrue(LongJobCore::state('ljsample')->all()['status'] == 'progress');
    $this->assertTrue(LongJobCore::state('ljsample')->status() == 'progress', 'job is in progress');
    $this->assertTrue(count(new LongJobStates) == 1, 'previews state replaced bu current');
    LongJobCore::state('ljsample')->delete();
    $this->assertTrue(LongJobCore::state('ljsample')->status() === false, 'job stopped');
    $this->assertTrue(count(new LongJobStates) == 0, 'job removed from states');

    (new QueueWorker)->uninstall(PROJECT_KEY);
  }

}