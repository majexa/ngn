<?php

class ShellSshKeyUploader extends ShellSshKeyBase {

  function upload() {
    $key = trim(file_get_contents('/home/user/.ssh/id_rsa.pub'));
    touch('/home/user/.ssh/authorized_keys');
    $r = $this->cmd->cmd(<<<CMD
touch -a ~/.ssh/authorized_keys
if grep -q '$key' ~/.ssh/authorized_keys; then
  echo 'Key exists'
else
  echo "$key" >> ~/.ssh/authorized_keys
fi
CMD
    , false);
    output($this->cmd->title().': key '.(strstr($r, 'exists') ? 'exists' : 'uploaded'));
  }

}