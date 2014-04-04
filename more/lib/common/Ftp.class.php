<?php

class Ftp {

  public $server;

  public $user;

  public $password;

  public $id;

  public $r;

  private $logger;

  private $errorHandler;
  
  public $tempFolder;

  function connect() {
    $this->log('Connecting to "'.$this->server.'"');
    $this->id = ftp_connect($this->server);
    if (!$this->id) {
      $this->log('Could not connect to ftp host "'.$this->server.'"');
      return false;
    }
    $this->r = ftp_login($this->id, $this->user, $this->password);
    if (! $this->r) {
      $this->log(
        'Could not login to host "'.$this->server.'" for user "' .
           $this->user .
           ', pass "' .
           $this->password .
           '"');
      return false;
    }
    $this->log(
      'Connected to "' . $this->server . '", for user "' . $this->user . '"');
    ftp_pasv($this->id, true); // Переводим в пассивный режим
    return true;
  }

  function disconnect() {
    ftp_close($this->id);
  }

  function setLogger($logger) {
    $this->logger = $logger;
  }

  private function log($t) {
    output($t);
  }

  function setErrorHandler($handler) {
    $this->errorHandler = $handler;
  }

  private function error($t) {
    if (!is_callable($this->errorHandler))
      return;
    call_user_func($this->errorHandler, $t);
  }

  /**
   * Enter description here...
   *
   * @param unknown_type $source
   * @param unknown_type Каталог куда загружать
   */
  function _upload($source, $destination, $perm = 0777) {
    if (!$this->id) throw new Exception('Ftp not connected');
    $destination = Misc::trimSlashes($destination);
    $destination = ($destination ? $destination.'/' : '') . basename($source);
    $this->log('Start upload file "'.$source.'" to "'.$destination.'"');
    if (!file_exists($source))
      throw new Exception('File "' . $source . '" does not exists');
    //if ($this->exists($destination)) $this->delete($destination);
    $upload = ftp_put($this->id, $destination, $source, FTP_BINARY);
    if (!$upload) {
      throw new Exception('FTP upload "' . $source . '" to "ftp://' . $this->server . '/' . $destination .
            '" has failed!');
    } else {
      $this->log('Uploaded "' . $source . '" to "ftp://' . $this->server . '/' . $destination . '"');
    }
    $this->chmod($destination, $perm);
  }
  
  function upload($source, $destination, $perm = 0777) {
    if (is_dir($source)) {
      foreach (glob($source.'/*') as $path) {
        if (is_dir($path)) {
          $this->upload($path, $destination, $perm);
        } else {
          $this->_upload($path, $destination, $perm);
        }
      }
    } else {
      $this->_upload($source, $destination, $perm);
    }
  }
  
  private function isUnix() {
    $s = ftp_raw($this->id, 'SYST');
    if (strstr($s[0], 'emulated by FileZilla')) return false;
    return true;    
  }
  
  private function canChmod() {
    return $this->isUnix();
  }
  
  function chmod($path, $perm = 0777) {
    if (!$this->canChmod()) return;
    //if ($perm == 493) die2(':(');
    if (ftp_chmod($this->id, $perm, $path)!== false)
      $this->log('Chmod '.$perm.' "'.$path.'" successfully');
    else 
      $this->log('could not chmod "'.$path.'"');
  }

  function chmodR($path) {
    $this->chmod($path);
    if (!$list = $this->lst($path)) return;
    foreach ($list as $v) $this->chmodR($v);
  }

  function isDir($path) {
    return (ftp_size($this->id, $path) == - 1);
  }
  
  function exists($path) {
    if (!($lst = $this->lst(dirname($path)))) return false;
    foreach ($lst as $v) {
      if ($path == $v)
        return true;
    }
    return false;
  }

  function delete($path, $deleteSelf = true) {
    $path = Misc::clearLastSlash($path);
    if (! $this->isDir($path)) {
      //$this->log('delete file '.$path);
      ftp_delete($this->id, $path);
      return;
    }
    //$this->log("$path is directory");
    // Dir
    if (($list = $this->lst($path)) !== false) {
      foreach ($list as $v) {
        $this->delete($v, true); // поведение на remote FTP
      }
    }
    if ($deleteSelf)
      ftp_rmdir($this->id, $path);
  }

  function clear($path) {
    $this->log("FTP clear '$path'");
    $this->delete($path, false);
  }

  private $httpWebRoot;
  
  public $exitOnUploadError = false;

  /**
   * Определяет является ли заданная FTP-директории вебрутом
   *
   * @param   string  Путь к FTP-директории
   * @return  bool
   */
  private function detectWebRootDir($dir = '') {
    if (!file_exists('test.txt'))
      throw new Exception("File 'test.txt' does not exists");
    if ($dir and $dir[0] == '/')
      throw new Exception("first symbol is slash: '{$dir[0]}'");
    $remoteFile = ($dir ? $dir.'/' : '').'test.txt';
    $this->log("Try to put: $remoteFile");
    if (!ftp_put($this->id, $remoteFile, 'test.txt', FTP_ASCII)) {
      if ($this->exitOnUploadError)
        $this->error("Upload '$remoteFile' error");
      $this->log("Error putting: $remoteFile. Path does not exists or check permissions");
      return false;
    }
    $this->chmod($remoteFile);
    if (Url::exists($this->httpWebRoot.'/test.txt')) {
      ftp_delete($this->id, $remoteFile);
      return $dir;
    }
    $this->log("'$remoteFile' not opening by url '{$this->httpWebRoot}/test.txt'");
    //ftp_delete($this->id, $remoteFile);
    return false;
  }
  
  function lst($path) {
    if (!$this->isDir($path))
      return false;
    if (!$list = ftp_nlist($this->id, $path))
      return false;
    $list2 = [];
    foreach ($list as $v) {
      $vv = basename($v);
      if ($vv == '.' or $vv == '..')
        continue;
      if ($path and !strstr($v, '/'))
         $list2[] = $path.'/'.$v;
      else $list2[] = $v;
    }
    sort($list2);
    return $list2;
  }

  /**
   * Рекурсивно просматривает список сабдиректорий и возвращает путь до директории 
   * с вебрутом, если таковая имеется, либо FALSE, если таковой нет
   *
   * @param   Путь к FTP-директории, в которой нужно производить поиск
   * @return  mixed   Путь к FTP-директории с веб рутом или FALSE FALSE, если таковой нет
   */
  private function detectWebRootDirs($path = '', $recursive = false) {
    if (!$list = $this->lst($path)) return false;
    $listDirs = [];
    foreach ($list as $v) {
      if (!$this->isDir($v))
        continue;
      $listDirs[] = $v;
      $webroot = $this->detectWebRootDir($v);
      if ($webroot !== false) {
        return $webroot;
      }
    }
    foreach ($listDirs as $path) {
      $webroot = $this->detectWebRootDirs($path);
      if ($webroot !== false) {
        $this->log("*** detected at $path: $webroot");
        return $webroot;
      }
    }
    return false;
  }

  private $webroots;
  
  /**
   * @todo Сделать проверку параметров $httpWebRoot, $ftpWebRoot
   *
   */
  function detectWebRoot($httpWebRoot, $ftpWebRoot = '') {
    if (!$httpWebRoot) throw new Exception('$httpWebRoot is empty');
    $httpWebRoot = 'http://'.$httpWebRoot;
    $this->log("Detecting webroot for URL '$httpWebRoot' from FTP dir '$ftpWebRoot'");
    $this->httpWebRoot = $httpWebRoot;
    file_put_contents('test.txt', 'test');
    $webRoot = $this->detectWebRootDir($ftpWebRoot);
    if ($webRoot !== false) {
      $this->log("Webroot detected: '$ftpWebRoot'");
      return $webRoot;
    }
    $webRoot = $this->detectWebRootDirs($ftpWebRoot);
    if ($webRoot !== false) {
      $this->log("Webroot detected: '$webRoot'");
      return $webRoot;
    }
    return false;
  }
  
  function rename($oldFile, $newFile) {
    if (ftp_rename($this->id, $oldFile, $newFile)) {
      $this->log("Successfully renamed '$oldFile' to '$newFile'");
    } else {
      $this->log("There was a problem while renaming $oldFile to $newFile");
    }
  }
  
  function download($localFile, $remoteFile) {
    ftp_get($this->id, $localFile, $remoteFile, FTP_BINARY);
  }
  
  function mkdir($path) {
    if ($this->exists($path)) {
      $this->log("'$path' already exists");
      return;
    }
    if (ftp_mkdir($this->id, $path)) {
      $this->log("Successfully make dir '$path'");
    } else {
      $this->log("There was a problem while creating dir '$path'");
    }
  }
  
  function getContents($path) {
    if (!isset($this->tempFolder))
      throw new Exception('$this->tempFolder not defined');
    if (!file_exists($this->tempFolder))
      throw new Exception('$this->tempFolder "'.$this->tempFolder.'" not exists');
    // ------------------------------
    $localPath = $this->tempFolder.'/'.basename($path);
    if (!$this->exists($path))
      throw new Exception('File "'.$path.'" does not exists');
    ftp_get($this->id, $localPath, $path, FTP_BINARY);
    return file_get_contents($localPath);
  }
  
  function putContents($path, $c) {
    if (!isset($this->tempFolder))
      throw new Exception('$this->tempFolder not defined');
    if (!file_exists($this->tempFolder))
      throw new Exception('$this->tempFolder "'.$this->tempFolder.'" not exists');
    // ------------------------------
    $tempFile = $this->tempFolder.'/'.basename($path);
    file_put_contents($tempFile, $c);
    ftp_put($this->id, $path, $tempFile, FTP_BINARY);
    $this->log("Put content to file '$path'");
  }

}

