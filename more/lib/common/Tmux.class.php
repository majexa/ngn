<?php

/**
 * tmux — это менеджер терминалов, к которому удобно подключаться и отключаться, не теряя при этом процессы и историю.
 * Класс Tmux помогает запускать несколько комманд в одном окне, не разбираясь формате команд tmux'а.
 * Extample:
 * (new Tmux(['php cmd.php 123']))->run()
 *
 * https://gist.github.com/henrik/1967800
 * http://ricochen.wordpress.com/2011/11/14/tmux-techniques-by-example/
 */
class Tmux {

  protected $sessionName;

  function __construct($sessionName = 'asd') {
    $this->sessionName = $sessionName;
    // useful `tmux attach || tmux new; tmux list-session 2>&1 | grep -q "^$this->sessionName:" || tmux new-session -s $this->sessionName -d -n task1; tmux select-window -t $this->sessionName:task1; tmux send-keys "htop" C-m`;
  }

  protected $notSplitPanes = [];

  function notSplit($paneN) {
    $this->notSplitPanes[] = $paneN;
    return $this;
  }

  function run($cmd, $n = 9) {
    $this->kill();
    $this->cmd("-2 new-session -d -s $this->sessionName");
    //tmux list-session 2>&1 | grep -q "^$this->sessionName:" ||
    $this->cmd("new-window -t $this->sessionName:1 -n '$this->sessionName'");
    $rows = $cols = ceil(sqrt($n));
    for ($i = 0; $i < $cols - 1; $i++) {
      $this->cmd("select-pane -t $i");
      $p = round(100 / ($cols - $i)) * ($cols - $i - 1);
      $this->cmd("split-window -h -p $p");
    }
    $n = 0;
    while (1) {
      for ($j = 0; $j < $rows - 1; $j++) {
        $paneN = $j + $n * 3;
        $this->cmd("select-pane -t $paneN");
        $p = round(100 / ($rows - $j)) * ($rows - $j - 1);
        if (!in_array($paneN, $this->notSplitPanes)) $this->cmd("split-window -v -p $p");
      }
      $n++;
      if ($n == $cols) break;
    }
    $totalPanes = $cols * $rows;
    for ($i = 0; $i < $totalPanes; $i++) {
      if (($_cmd = $this->prepareCmd($cmd, $i)) === false) continue;
      $this->cmd("select-pane -t $i");
      $this->cmd("send-keys \"$_cmd\" C-m");
      //sleep(2);
    }
    $this->cmd("select-window -t $this->sessionName:1");
    $this->cmd("-2 attach-session -t $this->sessionName");
  }

  protected function prepareCmd($cmd, $paneN) {
    if (!is_array($cmd)) return $cmd;
    return isset($cmd[$paneN]) ? $cmd[$paneN] : false;
  }

  function cmd($cmd) {
    // output2($cmd);
    `tmux $cmd`;
  }

  function kill() {
    print `tmux kill-session -t $this->sessionName`;
  }

}