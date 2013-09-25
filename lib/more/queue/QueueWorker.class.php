<?php

class QueueWorker {

  function install($project) {
    $workerCount = 1;
    $for = '';
    for ($i = 1; $i <= $workerCount; $i++) $for .= " $i";
    $c = '#! /bin/sh

PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
DAEMON=/usr/bin/php
DAEMON_OPTS=\'/home/user/ngn-env/projects/'.$project.'/queue.php\'
NAME='.$project.'-queue
QUIET="--quiet"

for N in '.$for.'
do
  DESC="'.$project.' queue daemon ${N}"
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
    file_put_contents("/tmp/$project-queue", $c);
    print Cli::shell("sudo mv /tmp/$project-queue /etc/init.d/$project-queue");
    print Cli::shell("sudo chmod +x /etc/init.d/$project-queue");
    print Cli::shell("sudo /etc/init.d/$project-queue restart");
    usleep(0.1 * 1000000);
  }

  function uninstall($project) {
    `sudo /etc/init.d/$project-queue stop`;
    `sudo rm /etc/init.d/$project-queue`;
  }

}