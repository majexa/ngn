<?php

/**
 * Объекты класса AgiAction вызываются не из контекста сайта, поэтому констант сайта таких, как путь к конфигурации или настройки базы данных не существует. Поэтому связь с сайтом осуществляется через демон очереди и добавление заданий в эту очередь
 */
abstract class AgiAction {
  use Options;

  /**
   * @var Agi
   */
  protected $agi;

  protected function defineOptions() {
    return [
      'introSound' => "ivr/{$this->name}/intro",
      'okSound'    => "ivr/{$this->name}/ok"
    ];
  }

  protected $name;

  function __construct(AgiBase $agi, array $options = []) {
    $this->agi = $agi;
    $r = str_replace('AgiAction', '', get_class($this));
    if (!$r) $this->name = 'common';
    else $this->name = lcfirst($r);
    $this->setOptions($options);
    $this->agi->conlog(getPrr($this->agi->request));
    if ($this->agi->request['agi_extension'] != 'h') $this->pickup();
    if (static::recall()) {
      $scFile = Asterisk::scFolder($this->name).'/'.$this->agi->getVar('id').'.php';
      File::delete($scFile);
    }
  }

  protected function pickup() {
  }

  abstract function action();

  static function recall() {
    return true;
  }

  static function getClass($project) {
    $class = 'AgiAction'.ucfirst($project);
    return class_exists($class) ? $class : 'AgiAction';
  }

  function hangup() {
  }

}