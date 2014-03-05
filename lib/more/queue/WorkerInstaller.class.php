<?php

class WorkerInstaller {

  protected $projectName, $demon, $workersCount, $c;

  function __construct($projectName, $demon, $workersCount) {
    $this->projectName = $projectName;
    $this->demon = $demon;
    $this->workersCount = $workersCount;
    $this->c = file_get_contents('/etc/rc.local');
  }

  function install() {
    $for = '';
    $file = $this->demon;
    $project = $this->projectName;
    for ($i = 1; $i <= $this->workersCount; $i++) $for .= " $i";
    $c = '#! /bin/sh

PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
DAEMON=/usr/bin/php
DAEMON_OPTS=\'/home/user/ngn-env/projects/'.$project.'/'.$file.'.php\'
NAME='.$project.'-'.$file.'
QUIET="--quiet"

for N in '.$for.'
do
  DESC="'.$project.' '.$file.' daemon ${N}"
  PIDFILE="/var/run/${NAME}${N}.pid"
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
    file_put_contents("/tmp/$project-$file", $c);
    print Cli::shell("php ~/ngn-env/pm/pm.php localProject updateIndex $this->projectName");
    print Cli::shell("sudo mv /tmp/$project-$file /etc/init.d/$project-$file");
    print Cli::shell("sudo chmod +x /etc/init.d/$project-$file");
    print Cli::shell("sudo /etc/init.d/$project-$file restart");
    $this->addToRc($this->projectName, $this->demon);
    usleep(0.1 * 1000000);
  }

  function uninstall() {
    $ids = str_replace("\n", ' ', `ps aux | grep test/$this->demon | grep -v grep | awk '{print $2}'`);
    if ($ids) sys("sudo kill $ids");
    sys("sudo rm /etc/init.d/$this->projectName-$this->demon");
  }

  protected function rcLocalWrite($c) {
    $tmpFile = "/tmp/$this->projectName-$this->demon";
    file_put_contents($tmpFile, $c);
    `sudo mv $tmpFile /etc/rc.local`;
  }

  function addToRc($projectName, $demon) {
    $cmd = "su user -c 'sudo /etc/init.d/$projectName-$demon start'";
    $begin = "# ngn auto-generated workers begin\nsleep 15";
    $end = '# ngn auto-generated workers end';
    if ($this->rcLocalIsVirgin()) {
      $a = "\n\n$begin\n$cmd\n$end\n\n";
      $c = preg_replace('/^(.*this script does nothing.)(\s+)(.*)$/ms', '$1'.$a.'$3', $this->c);
      $this->rcLocalWrite($c);
    }
    else {
      if (!preg_match("/$begin(.*)$end/ms", $this->c, $m)) throw new Exception('something wrong');
      if (strstr($m[1], $cmd)) {
        output("Worker '$projectName-$demon' already in rc.local");
      }
      else {
        $this->rcLocalWrite(preg_replace("/($begin)(.*)($end)/ms", '$1$2'."$cmd\n".'$3', $this->c));
      }
    }
  }

  function rcLocalIsVirgin() {
    preg_match('/^.*this script does nothing.\s+(.*)$/ms', $this->c, $m);
    return !$this->hasNgnWorkers($m[1]);
  }

  function hasNgnWorkers($s) {
    return strstr($s, '# ngn auto-generated workers');
  }

}