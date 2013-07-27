<?php

class CronManager {

  static $periods = [
    '5min', '10min', 'hourly'
  ];

  protected $jobs;

  function __construct() {
    $this->initJobs();
  }

  protected function initJobs() {
    foreach (ClassCore::getDescendants('CronJobAbstract') as $v) {
      /* @var $job CronJobAbstract */
      $job = new $v['class'];
      if (!$job->enabled) continue;
      if (!isset($job->period)) throw new Exception("{$v['class']} period does not set");
      $this->jobs[$job->period][$v['name']] = $job;
    }
  }

  protected function getJobs($period) {
    if (!isset($this->jobs[$period])) return false;
    return $this->jobs[$period];
  }

  /**
   * @param   string    5minute/daily/hourly
   */
  function run($period) {
    if (!in_array($period, self::$periods)) throw new Exception("Period '$period' does not exists");
    if (!($jobs = $this->getJobs($period))) return false;
    $n = 0;
    $c = '';
    foreach ($jobs as $name => $job) {
      /* @var $job CronJobAbstract */
      $c .= $job->run();
      $jobNames[] = $name;
      $n++;
    }
    LogWriter::v('cron', 'jobs done: '.implode(', ', $jobNames));
    LogWriter::html('cron', $c, ['period' => $period]);
  }

}