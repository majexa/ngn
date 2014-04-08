<?php

class Ssh2SftpLocal implements Ssh2SftpInterface {

  function putContents($file, $data) {
    file_put_contents($file, $data);
  }

}