<?php

trait DdParamFilterCtrl {

  static $DATE_Y = 1;

  static $DATE_MY = 2;

  static $DATE_DMY = 3;

  static $DATE_RANGE = 4;

  /**
   * Порядковы номер параметра, отвечающего за ключевое слово фильтра
   *
   * @return integer
   */
  protected function paramFilterN() {
    return 1;
  }

  protected $paramFilterItems;

  /**
   * @return DdDbItemsExtended
   */
  protected function paramFilterItems() {
    if (isset($this->paramFilterItems)) return $this->paramFilterItems;
    return $this->paramFilterItems = $this->_paramFilterItems();
  }

  /**
   * @abstract
   * @return DdDbItemsExtended
   */
  abstract protected function _paramFilterItems();

  /**
   * Инициализирует ф-л трейта
   */
  protected function initFilterByParams() {
    for ($i = $this->paramFilterN(); $i < count($this->req->params); $i++) {
      $m = [];
      if (preg_match('/([a-z]+[0-9]?)\.(.+)/', $this->req->params[$i], $m)) {
        if (strstr($m[2], '.')) {
          list($value, $fieldName) = explode('.', $m[2]);
        } else {
          $value = $m[2];
          $fieldName = null;
        }
        if ($this->addFilterByParam($m[1], $value, $fieldName)) {
          if ($m[1] == 'd') $this->d['filters'][$m[1]] = $value;
          else $this->d['filters'][$m[1]] = explode('.', $value);
        }
      }
    }
  }

  protected function addFilterByParam($param1, $param2, $param3 = null) {
    $method = 'setFilter'.ucfirst($param1);
    if ($param1 == 'd') {
      $this->setFilterDate($param2, $param3);
    }
    elseif ($param1 == 't2' or $param1 == 't') {
      // Четко по тэгу "page/t2.tagGroup.tagId"
      if (is_array($param2)) {
        list($tagName, $tagValue) = $param2;
      } elseif ($param3) {
        $tagName = $param2;
        $tagValue = $param3;
      } else {
        throw new Exception('!');
      }
      $this->setFilterTags($tagValue, $tagName, ($param1 == 't2'));
    }
    elseif ($param1 == 'u') {
      // Четко по пользователю
      $this->setFilterUser($param2);
    }
    elseif ($param1 == 'v') {
      // Четко по значению поля. Пример /asd.asd/v.title.Какой-то заголовок
      //list($fieldName, $value) = explode('.', $v);
      $param3 = $param3 == 'none' ? '' : $param3;
      $param3 = urldecode($param3);
      if (isset(DdCore::$pathTranslation[$param3])) {
        $f = DdCore::$pathTranslation[$param3];
        if (($r = $f($param3))) {
          list($newK, $newV) = $f($param3);
          $this->addFilterByParam($newK, $newV);
        }
      }
      else {
        $this->paramFilterItems()->addF($param2, $param3);
      }
    }
    elseif ($param1 == 'ne') {
      // Not Empty
      $this->paramFilterItems()->addNullFilter($param1, false);
    }
    elseif (method_exists($this, $method)) {
      // Динамический метод
      $this->$method($param2);
    }
    else {
      return false;
    }
    return true;
  }

  /**
   * Возвращает имя поля по которому производится фильтрация по дате
   *
   * @return string
   */
  protected function paramFilterDateField() {
    return 'dateCreate';
  }

  public $year = null, $month = null, $day = null, $dateFieldName = null;
  protected $dateParam, $datePeriod, $dateType;

  protected function setFilterDate($dateParam, $fieldName = null, $redefine = false) {
    if ($redefine and isset($this->dateParam)) return false;
    if (!$fieldName) $fieldName = $this->paramFilterDateField();
    $this->dateFieldName = $fieldName;
    $this->dateParam = $dateParam;
    // Парсим параметры даты
    // Четко по дате
    if (preg_match('/(\d+);(\d+);(\d+)-(\d+);(\d+);(\d+)/', $dateParam, $m)) {
      // Период
      $this->datePeriod['from']['d'] = $m[1];
      $this->datePeriod['from']['m'] = $m[2];
      $this->datePeriod['from']['y'] = $m[3];
      $this->datePeriod['to']['d'] = $m[4];
      $this->datePeriod['to']['m'] = $m[5];
      $this->datePeriod['to']['y'] = $m[6];
      $this->dateType = self::$DATE_RANGE;
    }
    else {
      // Конкретная дата
      $date = explode(';', $dateParam);
      if (count($date) == 3) {
        // Указан конкретный день
        $this->day = (int)$date[0];
        $this->month = (int)$date[1];
        $this->year = (int)$date[2];
        $this->dateType = self::$DATE_DMY;
      }
      elseif (count($date) == 2) {
        // Указан конкретный месяц
        $this->month = (int)$date[0];
        $this->year = (int)$date[1];
        $this->dateType = self::$DATE_MY;
      }
      elseif (count($date) == 1) {
        // Указан конкретный год
        $this->year = (int)$date[0];
        $this->dateType = self::$DATE_Y;
      }
    }
    // Устанавливаем параметры для фильтров
    if ($this->dateType == self::$DATE_RANGE) {
      $this->paramFilterItems()->cond->addRangeFilter($fieldName, $this->datePeriod['from']['y'].'-'.$this->datePeriod['from']['m'].'-'.$this->datePeriod['from']['d'], $this->datePeriod['to']['y'].'-'.$this->datePeriod['to']['m'].'-'.$this->datePeriod['to']['d'].' 23:59:59');
    }
    elseif ($this->dateType == self::$DATE_DMY) {
      $this->paramFilterItems()->cond->addRangeFilter($fieldName, sprintf('%04d-%02d-%02d', $this->year, $this->month, $this->day), sprintf('%04d-%02d-%02d', $this->year, $this->month, $this->day + 1), null, DbCond::strictTo);
    }
    elseif ($this->dateType == self::$DATE_MY) {
      $this->paramFilterItems()->addF($fieldName, $this->month, 'MONTH');
      $this->paramFilterItems()->addF($fieldName, $this->year, 'YEAR');
    }
    elseif ($this->dateType == self::$DATE_Y) {
      $this->paramFilterItems()->addF($fieldName, $this->year, 'YEAR');
    }
    return true;
  }

  protected function paramFilterBirthDateField() {
    throw new Exception('Filter use date. You must realize "paramFilterDateField" method');
  }

  protected function setFilterAge($d) {
    $dateField = $this->paramFilterBirthDateField();
    static $init;
    if ($init) throw new Exception('Already initialized');
    $init = true;
    list($a, $b) = explode('-', $d);
    $a = (int)$a;
    $b = (int)$b;
    if ($b) {
      $this->setPageTitle("Возраст от $a до $b");
      $this->paramFilterItems()->cond->addRangeFilter("DATEDIFF(CURRENT_DATE, {$this->paramFilterItems()->table}.{$dateField})/365", $a, $b + 1, ['table' => false]);
    }
    else {
      throw new Exception('not realized');
      //$this->setPageTitle("Возраст от $a до $b");
      //$this->oManager->items->addF('age', $a);
    }
  }

  public $tagFilters = [];

  protected function setFilterTags($tagValue, $tagField, $byId = true) {
    /*
    // Парсим параметры тэгов
    if (is_array($tagsParam)) {
      // Тэги указаны вместе с типами
      throw new Exception('действие не реализовано');
      // ..........+..........
    } else {
      // Тэги указаны без типов. Выборка не учитывает типа
      // ..........+..........
      $ids = array();
      if (strstr($tagsParam, ',')) {
        // Условие выборки "или"
        $tagNames = explode(',', $tagsParam);
        $oTags = DdTags::get($this->strName, $tagField);
        if ($oTags->group->tree)
          throw new Exception("Getting tags by name supportes only flat tags. '$tagField' is tree type tag.");
        foreach ($tagNames as $name) {
          foreach ($oTags->getByName($name) as $tag) {
            $this->tagsSelected[] = $tag;
          }
          foreach (DdTagsItems::getIdsByName($this->strName, $tagField, $name) as $id)
            if (!in_array($id, $ids)) $ids[] = $id;
        }
        if ($ids) $this->oManager->items->addF('id', $ids);
      } elseif (strstr($tagsParam, '+')) {
        // Условие выборки "и"
        $tagNames = explode('+', $tagsParam);
        foreach ($tagNames as $name)
          $ids[] = DdTags::getItemIds($name, $tagField,
            $this->page['id']);
        for ($i = 1; $i < count($ids); $i++)
          $intersectIds = array_intersect($ids[$i - 1], $ids[$i]);
        if ($intersectIds)
          $this->oManager->items->addF('id', $intersectIds);
        // Определяем заголовок
        foreach ($tagNames as $name) {
          if (($tag = DdTags::getTag($name, $tagField, $this->page['id']))) {
            $titles[] = $tag['title'];
            $this->tagsSelected[] = $tag;
          }
        }
        if ($titles)
          $this->setPageTitle(implode(' + ', $titles));
        $this->d['tagsSplitter'] = '+';
      } else {
        // ------------------------------------
        // Фильтр по одному тэгу
        // ------------------------------------
        DdTagsItems::$getNonActive = isset($this->priv['edit']);
        $oTags = DdTags::get($this->strName, $tagField);
        if (is_numeric($tagsParam)) {
          if ($oTags->group->tree) {
            $tag = DdTags::getById($tagsParam);
          } else {
            $tag = DdTags::getById($tagsParam);
          }
          if (!empty($tag)) {
            $this->tagsSelected[] = $tag;
            $itemIds = DdTagsItems::getIdsByTagId($this->strName, $tagField, $tagsParam);
          }
        } else {
          if ($oTags->group->tree)
            throw new Exception("Getting tags by name supportes only flat tags. '$tagField' is tree type tag.");
          if (($tag = $oTags->getByName($tagsParam))) {
            $this->tagsSelected[] = $tag;
            $itemIds = DdTagsItems::getIdsByName($this->strName, $tagField, $tagsParam);
          }
        }
        if (empty($itemIds)) {
          // Если нет тэгов, делаем значение фильтра таким, что бы выборка была нулевая
          $itemIds = -1;
        }
        $this->oManager->items->addF('id', $itemIds);
      }
    }
    */
    // die2(3);
    $this->tagFilters[$tagField] = $tagValue;
    if (!$tagValue) {
      $this->paramFilterItems()->addF('id', 0);
      return;
    }
    try {
      $this->paramFilterItems()->addTagFilter($tagField, is_array($tagValue) ? $tagValue : explode(',', $tagValue), $byId);
    } catch (Exception $e) {
      if ($e->getCode() == 10) {
        throw new Error404($e->getMessage());
      }
    }
  }

  public $curUser = false;
  public $curUserId = false;

  /**
   * @param integer $userId ID пользователя
   * @throws EmptyException
   */
  function setFilterUser($userId) {
    $this->curUser = DbModelCore::get('users', $userId);
    $this->curUserId = $userId;
    $this->paramFilterItems()->addF('userId', $userId);
  }

}