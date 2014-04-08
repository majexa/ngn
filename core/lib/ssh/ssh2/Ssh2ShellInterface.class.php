<?php

interface Ssh2ShellInterface {

  function exec($cmd);

  function shell(array $cmds);

}