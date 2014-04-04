<?php

class ShellSshKey {

  public $key;
  protected $ssh, $server, $user, $homeFolder, $tempFolder;

  function __construct(ShellSshPasswordCmd $cmd, $localUser, $remoteUser) {
    $this->cmd = $cmd;
    $this->ip = '';
    $this->user = $user;
    $this->homeFolder = $user == 'root' ? '/root' : "/home/$user";
    $this->tempFolder = DATA_PATH.'/temp';
    $this->config();
    $this->generate();
    $this->key = $this->getRemoteKey();
  }

  function __toString() {
    return $this->key;
  }

  function sshpass($serverName) {
    return "sshpass -p '{$this->password($serverName)}'";
  }

  protected function config() {
    output("Configuring SSH on server '$this->server'");
    $config = "StrictHostKeyChecking=no\nLogLevel=quiet\nUserKnownHostsFile=/dev/null";
    $this->cmd($this->server, <<<CMD
if grep -q StrictHostKeyChecking=no ~/.ssh/config; then
  echo 'Already installed'
else
  echo '$config' > ~/.ssh/config
fi
CMD
    );
  }

  protected function generate() {
    $suCmd = $this->user != 'root' ? "su $this->user\n" : '';
    $this->cmd(<<<CMD
{$suCmd}if [ ! -f ~/.ssh/id_rsa ]; then
  ssh-keygen -q -f ~/.ssh/id_rsa -t rsa -N ''
fi
CMD
    );
  }

  protected function getRemoteKey() {
    print sys($this->cmd->sshpass($this->server)." scp {$this->ip}:$this->homeFolder/.ssh/id_rsa.pub {$this->tempFolder}/$this->server.pub");
    return file_get_contents("{$this->tempFolder}/$this->server.pub");
  }

}


/*
 *
 *   static function remoteSshCommand($host, $sshUser, $sshPass, $cmd) {
    //sys("expect -c \'spawn ssh '.$sshUser.'@'.$host.' '.$cmd.'; expect password ; send "'.$sshPass.'\n" ; interact\'", false);
  }

  static function uploadSshKey($host, $localUser, $user, $pass) {
    $homeFolder = $localUser == 'root' ? '/root' : "/home/$localUser";
    $c = file_get_contents("$homeFolder/id_rsa.pub");

  }


 *
 */