<?php

/**
 * Control Panel Items Output
 */
class Cpio extends ItemsOutput {

  function el($value, $fieldName, $itemId) {
    return $this->_html([
      'id'    => $itemId,
      'type'  => $this->fields[$fieldName]['type'],
      'title' => $this->fields[$fieldName]['title'],
      'name'  => $fieldName,
      'v'     => $value,
      'o'     => $this
    ]);
  }

  protected function getFields() {
    return PmCore::getFields();
  }

  protected function elsItem(array $item) {
  }

  protected function html(array $data) {
    $data['ddddItemLink'] = St::dddd($data['ddddItemLink'], $data);
    $ddddByType = array_merge($this->ddddByType, self::$_ddddByType);
    $ddddByName = array_merge($this->ddddByName, self::$_ddddByName);
    if (isset(self::$funcByName[$data['name']])) {
      $func = self::$funcByName[$data['name']];
      try {
        $r = ($this->debug ? 'funcByName:'.$data['name'].'=' : ''). // debug
          $func($data);
      } catch (Exception $e) {
        throw new Exception('funcByName name="'.$data['name'].'" error: '.$e->getMessage());
      }
      return $r;
    }
    elseif (isset($ddddByName[$data['name']])) {
      try {
        $r = ($this->debug ? 'ddddByName:'.$data['name'].'=' : ''). // debug
          St::dddd($ddddByName[$data['name']], $data);
      } catch (Exception $e) {
        throw new Exception('ddddByName name="'.$data['name'].'" error: '.$e->getMessage());
      }
      return $r;
    }
    elseif (isset($this->d[$data['type']])) {
    }
    elseif (isset($this->tplPathByName[$data['name']])) {
      return ($this->debug ? 'tplPathByName:name:'.$data['name'] : ''). // debug
      Tt()->getTpl($this->tplPathByName[$data['name']], $data);
    }
    elseif (isset($this->tplPathByType[$data['type']])) {
      return ($this->debug ? 'tplPathByType:type:'.$this->tplPathByType[$data['type']].'=' : ''). // debug
      Tt()->getTpl($this->tplPathByType[$data['type']], $data);
    }
    elseif (isset($this->ssssByType[$data['type']])) {
      try {
        $r = ($this->debug ? 'ssssByType:'.$data['type'].'=' : ''). // debug
          St::ssss($this->ssssByType[$data['type']], $data);
      } catch (Exception $e) {
        throw new Exception('ssssByType type="'.$data['type'].', name="'.$data['name'].'", current class='.get_class($this).'". error: '.$e->getMessage());
      }
      return $r;
    }
    elseif (isset($ddddByType[$data['type']])) {
      try {
        $r = ($this->debug ? 'ddddByType:'.$data['type'].'=' : ''). // debug
          St::dddd($ddddByType[$data['type']], $data);
      } catch (Exception $e) {
        throw new Exception('ddddByType type="'.$data['type'].', name="'.$data['name'].'" current class='.get_class($this).'". error: '.$e->getMessage());
      }
      return $r;
    }
    else {
      return ($this->debug ? 'ddddDefault (type='.$data['type'].'): ' : ''). // debug
      St::dddd($this->ddddDefault, $data);
    }
  }

}
