<?php

class DaemonInstaller {
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
      'bin' => '/usr/bin/run',
      'opts' => "{$this->projectName}/{$this->daemonName}",
      'workers' => 1
    ];
  }

  function install() {
    $for = '';
    for ($i = 1; $i <= $this->options['workers']; $i++) $for .= " $i";
    $c = '#! /bin/sh

# ngn auto-generated worker

PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
DAEMON='.$this->options['bin'].'
DAEMON_OPTS="'.$this->options['opts'].'"
NAME='.$this->name.'
QUIET="--quiet"

for N in'.$for.'
do
  DESC="'.$this->projectName.' '.$this->daemonName.' daemon ${N}"
  PIDFILE="/var/run/${NAME}-${N}.pid"
  START_OPTS="--start ${QUIET} --background --make-pidfile --pidfile ${PIDFILE} --exec ${DAEMON} ${DAEMON_OPTS}"
  STOP_OPTS="--stop --pidfile ${PIDFILE}"
  test -x $DAEMON || exit 0
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
    print Cli::shell("sudo mv /tmp/$this->name /etc/init.d/$this->name");
    print Cli::shell("sudo chmod +x /etc/init.d/$this->name");
    print Cli::shell("sudo /etc/init.d/$this->name restart");
    (new RcLocal)->add("$this->projectName-$this->daemonName");
    usleep(0.1 * 1000000);
  }

  function uninstall() {
    $ids = str_replace("\n", ' ', `ps aux | grep test/$this->daemonName | grep -v grep | awk '{print $2}'`);
    if ($ids) sys("sudo kill $ids");
    sys("sudo rm /etc/init.d/$this->projectName-$this->daemonName");
    (new RcLocal)->remove("$this->projectName-$this->daemonName");
  }

}