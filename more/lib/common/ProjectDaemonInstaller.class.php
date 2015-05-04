<?php

class ProjectDaemonInstaller extends DaemonInstaller {

  protected $commentFlag = '(project)', $config;

  function __construct($projectName, $daemonName, array $options = []) {
    $this->config = json_decode(`run site $projectName var $daemonName`, JSON_FORCE_OBJECT);
    parent::__construct($projectName, $daemonName, $options);
  }

  protected function defineOptions() {
    return [
      'bin'     => '/usr/bin/php',
      'opts'    => "/home/user/ngn-env/projects/{$this->projectName}/{$this->daemonName}.php",
      'workers' => isset($this->config['workers']) ? $this->config['workers'] : 1
    ];
  }

  function install() {
    if (!empty($this->config['disable'])) return false;
    return parent::install();
  }

  /**
   * Возвращает имена установленных проектных демонов
   */
  static function getInstalled() {
    $r = [];
    foreach (glob('/etc/init.d/*') as $file) {
      if (strstr(file_get_contents($file), '# ngn auto-generated worker (project)')) {
        $r[] = $file;
      }
    }
    return $r;
  }

}