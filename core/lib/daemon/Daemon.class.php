<?php

class Daemon {
  use Options;

  protected $projectName, $daemonName, $c;
  public $name;

  function __construct($projectName, $daemonName, array $options = []) {
    $this->projectName = $projectName;
    $this->daemonName = $daemonName;
    $this->name = "{$this->projectName}-{$this->daemonName}";
    $this->setOptions($options);
  }

  protected function defineOptions() {
    return [
      'bin' => '/usr/bin/php',
      'opts' => "/home/user/ngn-env/run/run.php {$this->projectName}/{$this->daemonName}",
      'workers' => 1
    ];
  }

  protected $commentFlag = '';

  function install() {
    $for = '';
    for ($i = 1; $i <= $this->options['workers']; $i++) $for .= " $i";
    $c = '#! /bin/sh

# ngn auto-generated worker '.$this->commentFlag.'

PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
DAEMON='.$this->options['bin'].'
DAEMON_OPTS="'.$this->options['opts'].'"
NAME='.$this->name.'
QUIET="--quiet"

if [ ! -f $DAEMON ]; then
   echo "NOT EXISTS: $DAEMON"
   exit 0
fi

if [ ! -f $DAEMON_OPTS ]; then
   echo "NOT EXISTS: $DAEMON_OPTS"
   exit 0
fi

for N in'.$for.'
do
  DESC="'.$this->projectName.' '.$this->daemonName.' daemon ${N}"
  PIDFILE="/var/run/${NAME}-${N}.pid"
  START_OPTS="--start ${QUIET} --chuid user:user --background --make-pidfile --pidfile ${PIDFILE} --exec ${DAEMON} ${DAEMON_OPTS}"
  STOP_OPTS="--stop --pidfile ${PIDFILE}"
  set -e
  case "$1" in
    start)
      echo -n "Starting $DESC: "
      start-stop-daemon $START_OPTS
      echo "$NAME."
    ;;
    stop)
      echo -n "Stopping $DESC: "
      start-stop-daemon $STOP_OPTS
      echo "$NAME."
    ;;
    check)
      if [ -f $PIDFILE ]
      then
        if ! kill -0 `cat $PIDFILE` > /dev/null 2>&1; then
          echo -n "Starting $DESC: "
          start-stop-daemon $START_OPTS
          echo "$NAME."
        fi
      fi
    ;;
    restart|force-reload)
      if kill -0 `cat $PIDFILE` > /dev/null 2>&1; then
        echo -n "Restarting $DESC: "
        start-stop-daemon $STOP_OPTS
        sleep 1
      else
        echo -n "Starting $DESC: "
      fi
      start-stop-daemon $START_OPTS
      echo "$NAME."
    ;;
    *)
      NM=/etc/init.d/$NAME
      echo "Usage: $NM {start|stop|restart|force-reload|check}" >&2
      exit 1
    ;;
  esac
done

exit 0';
    file_put_contents("/tmp/$this->name", $c);
    output2("Installing {$this->options['workers']} workers of $this->name");
    `sudo mv /tmp/$this->name /etc/init.d/$this->name`;
    `sudo chmod +x /etc/init.d/$this->name`;
    $this->killProcesses();
    $this->restart();
    (new RcLocal)->add("$this->projectName-$this->daemonName");
    return true;
  }

    function restart() {
        print `sudo /etc/init.d/$this->name restart`;
    }

  protected function getProcessIds() {
    $pattern = str_replace('-', '/', $this->name);
    return trim(str_replace("\n", ' ', `ps aux | grep $pattern | grep -v grep | awk '{print $2}'`));
  }

  protected function killProcesses() {
    if ($ids = $this->getProcessIds()) sys("sudo kill $ids", true);
  }

  function exists() {
    return file_exists("/etc/init.d/$this->projectName-$this->daemonName");
  }

  function uninstall($ifExists = true) {
    if ($ifExists and !$this->exists()) return;
    $this->killProcesses();
    sys("sudo rm /etc/init.d/$this->projectName-$this->daemonName");
    (new RcLocal)->remove("$this->projectName-$this->daemonName");
  }

  function checkInstallation() {
    if (!($r = $this->getProcessIds())) throw new Exception('Installation failed for "'.$this->name.'"');
    output("Started processes: $r");
  }

}