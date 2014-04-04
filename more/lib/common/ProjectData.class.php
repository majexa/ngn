<?php

class ProjectData extends ProjectState {

  static protected function file($key) {
    return DATA_PATH.'/data/'.$key.'.php';
  }

}