<?php

class DaemonInstaller {
  use Options;

  protected $projectName, $daemonName, $c;
  public $name;

  function __construct($projectName, $daemonName, array $options = []) {
    $this->setOptions($options);
    $this->projectName = $projectName;
    $this->daemonName = $daemonName;
    $this->c = file_get_contents('/etc/rc.local');
    $this->name = "{$this->projectName}-{$this->daemonName}";
  }

  /**
   * @return mixed Количесво воркеров демона
   */
  protected function workersCount() {
    return 1;
  }

  protected function bin() {
    return isset($this->options['bin']) ? $this->options['bin'] : '/usr/bin/php';
  }

  protected function opts() {
    return isset($this->options['opts']) ? $this->options['opts'] : "/home/user/ngn-env/projects/{$this->projectName}/{$this->daemonName}.php";
  }

  function install() {
    $for = '';
    for ($i = 1; $i <= $this->workersCount(); $i++) $for .= " $i";
    $c = '#! /bin/sh

# ngn auto-generated worker

PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
DAEMON='.$this->bin().'
DAEMON_OPTS="'.$this->opts().'"
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
<<<<<<< HEAD:core/lib/common/DaemonInstaller.class.php
    file_put_contents("/tmp/$this->name", $c);
    print Cli::shell("sudo mv /tmp/$this->name /etc/init.d/$this->name");
    print Cli::shell("sudo chmod +x /etc/init.d/$this->name");
    print Cli::shell("sudo /etc/init.d/$this->name restart");
=======
    file_put_contents("/tmp/$divised", $c);
    print Cli::shell("sudo mv /tmp/$divised /etc/init.d/$divised");
    print Cli::shell("sudo chmod +x /etc/init.d/$divised");
    print Cli::shell("sudo /etc/init.d/$divised restart");
>>>>>>> 82de3053f79156a475216e9d5603bbcba9d5562f:more/lib/common/DaemonInstaller.class.php
    //$this->addToRc();
    usleep(0.1 * 1000000);
  }

  function uninstall() {
    $ids = str_replace("\n", ' ', `ps aux | grep test/$this->daemonName | grep -v grep | awk '{print $2}'`);
    if ($ids) sys("sudo kill $ids");
    sys("sudo rm /etc/init.d/$this->projectName-$this->daemonName");
  }

  protected function rcLocalWrite($c) {
    $tmpFile = "/tmp/$this->projectName-$this->daemonName";
    file_put_contents($tmpFile, $c);
    `sudo mv $tmpFile /etc/rc.local`;
  }

  protected function addToRc() {
    $cmd = "su user -c 'sudo /etc/init.d/{$this->projectName}-{$this->daemonName} start'";
    $begin = "# ngn auto-generated workers begin\nsleep 15";
    $end = '# ngn auto-generated workers end';
    if ($this->rcLocalIsVirgin()) {
      $a = "\n\n$begin\n$cmd\n$end\n\n";
      $c = preg_replace('/^(.*this opts does nothing.)(\s+)(.*)$/ms', '$1'.$a.'$3', $this->c);
      $this->rcLocalWrite($c);
    }
    else {
      if (!preg_match("/$begin(.*)$end/ms", $this->c, $m)) throw new Exception('something wrong');
      if (strstr($m[1], $cmd)) {
        output("Worker '{$this->projectName}-{$this->daemonName}' already in rc.local");
      }
      else {
        $this->rcLocalWrite(preg_replace("/($begin)(.*)($end)/ms", '$1$2'."$cmd\n".'$3', $this->c));
      }
    }
  }

  protected function rcLocalIsVirgin() {
    if (!preg_match('/^.*this opts does nothing.\s+(.*)$/ms', $this->c, $m)) return false;
    return !self::hasNgnWorkers($m[1]);
  }

  static function hasNgnWorkers($s) {
    return strstr($s, '# ngn auto-generated worker');
  }

  //static function cleanup($prefix == null) {

  //}

}