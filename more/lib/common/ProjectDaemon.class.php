<?php

class ProjectDaemon extends Daemon {

  protected $commentFlag = '(project)', $config;

  function __construct($projectName, $daemonName, array $options = []) {
    $this->config = json_decode(`run site $projectName var varName=$daemonName`, JSON_FORCE_OBJECT);
    parent::__construct($projectName, $daemonName, $options);
  }

  protected function defineOptions() {
    return [
      'bin'     => '/usr/bin/php',
      'opts'    => NGN_ENV_PATH."/projects/{$this->projectName}/{$this->daemonName}.php",
      'workers' => isset($this->config['workers']) ? $this->config['workers'] : 1
    ];
  }

  function install() {
    if (!empty($this->config['disable'])) return false;
    return parent::install();
  }

  /**
   * Возвращает имена установленных проектных демонов или демонов конкретного проекта
   *
   * @param null $projectName
   * @return array
   */
  static function getInstalled($projectName = null) {
    $r = [];
    $projectNameMask = $projectName ? $projectName.'-' : '';
    foreach (glob('/etc/init.d/'.$projectNameMask.'*') as $file) {
      if (strstr(file_get_contents($file), '# ngn auto-generated worker (project)')) {
        $r[] = str_replace('/etc/init.d/'.$projectNameMask, '', $file);
      }
    }
    return $r;
  }

}