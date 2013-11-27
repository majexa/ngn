<?php

class TinyFileManager extends TinyUploadManager {
  
  /**
   * Перемещает файл в соответствующую папку и возвращает ссылку на него
   *
   * @param   array   Пример:
   *                  array(
   *                    'name' => 'asdasd.txt',
   *                    'tmp_name' => '873yq2f.tmp',
   *                    'type' => 'text/plain'
   *                  )
   * 
   * @return  array   Пример:
   *                  array(
   *                    'url' => './u/ed/asd/file.txt',
   *                    'filesize' => '124123'
   *                  )
   * 
   */
  function process($v) {
    $exp = File::getExtension($v['tmp_name']);
    Dir::make($this->folderPath);
    if (!$fileName = Misc::transit($v['name'], true))
      File::getUnicName($this->folderPath, $exp);
    copy($v['tmp_name'], $this->folderPath.'/'.$fileName);
    $r = [
      'url' => './'.$this->folder.'/'.$fileName,
      'filesize' => filesize($v['tmp_name'])
    ];
    unlink($v['tmp_name']);
    return $r;
  }
  
}