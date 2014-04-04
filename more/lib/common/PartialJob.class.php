<?php

abstract class PartialJob {
  
  protected $jobs;
  protected $jobsTotal;
  protected $stepsTotal;
  protected $stepsRemains;
  public $jobsInStep = 10;
  public $unknownTotalCount = false;
  protected $jobsData;
  
  function __construct() {
    $this->initJobs();
    if ($this->unknownTotalCount) return;
    if (!isset($this->jobsTotal)) $this->jobsTotal = count($this->jobs);
    Misc::checkEmpty($this->jobsTotal);
    $this->stepsTotal = ceil($this->jobsTotal/$this->jobsInStep);
  }
  
  protected function init() {}
  
  function setJobsData(array $data) {
    $this->jobsData = $data;
    return $this;
  }
  
  abstract protected function initJobs();
  
  abstract protected function makeJob($n);
  
  function complete() {}
  
  /**
   * Возвращает идентификатор этого объекта
   */
  function getId() {
    return get_class($this);
  }
  
  function makeStep($step) {
    $r = $this->stepData($step);
    if ($this->stepsRemains <= 0) $this->complete();
    return $r;
  }
  
  function stepData($step) {
    $step = (int)$step;
    if ($this->unknownTotalCount)
      return [
        'step' => $step,
        'nextStep' => $step+1
      ];
    // ------------------------------------------------------
    // Если шаг больше, максимально возможного
    if ($step > $this->stepsTotal) {
      throw new Exception(
        'Шаг '.$step.' больше максимально возможного '.$this->stepsTotal,
        1040
      );
    }
    $r = null;
    for ($i = $step * $this->jobsInStep; $i < ($step+1) * $this->jobsInStep; $i++) {
      if (!isset($this->jobs[$i])) break;
      $r = $this->makeJob($i);
    }
    // если $this->jobsInStep = 1, $i = $step
    $stepsTotal = ceil($this->jobsTotal / $this->jobsInStep);
    $this->stepsRemains = $stepsTotal - ($step+1);
    return [
      'jobsTotal' => $this->jobsTotal,
      'stepsTotal' => $stepsTotal,
      'jobsRemains' => $this->jobsTotal - (($step + 1) * $this->jobsInStep), //$this->getJobsRemains($i),
      'stepsRemains' => $this->stepsRemains,
      'step' => $step,
      'nextStep' => $this->stepsRemains > 0 ? $step+1 : 0,
      'lastJobResult' => $r
    ];
  }
  
  /**
   * Возвращает число оставшихся заданий
   *
   * @param   integer   Текущий номер задания
   * @return  integer
   */
  protected function getJobsRemains($n) {
    return $this->jobsTotal - $n;
  }

}