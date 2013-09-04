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

  /**
   * @abstract
   * @return DdItems
   */
  abstract protected function paramFilterItems();

  /**
   * Инициализирует ф-л трейта
   */
  protected function initFilterByParams() {
    for ($i = $this->paramFilterN(); $i < count($this->req->params); $i++) {
      $m = [];
      if (preg_match('/([a-z]+[0-9]?)\.(.+)/', $this->req->params[$i], $m)) {
        if ($this->addFilterByParam($m[1], $m[2])) {
          if ($m[1] == 'd') $this->d['filters'][$m[1]] = $m[2];
          else $this->d['filters'][$m[1]] = explode('.', $m[2]);
        }
      }
    }
  }

  protected function addFilterByParam($k, $v) {
    $method = 'setFilter'.ucfirst($k);
    if ($k == 'd') {
      $this->setFilterDate($v);
    }
    elseif ($k == 't2' or $k == 't') {
      // Четко по тэгу "page/t2.tagGroup.tagId"
      list($tagName, $tagValue) = is_array($v) ? $v : explode('.', $v);
      $this->setFilterTags($tagValue, $tagName, ($k == 't2'));
    }
    elseif ($k == 'u') {
      // Четко по пользователю
      $this->setFilterUser($v);
    }
    elseif ($k == 'v') {
      // Четко по значению поля. Пример /asd.asd/v.title.Какой-то заголовок
      list($fieldName, $value) = explode('.', $v);
      $value = $value == 'none' ? '' : $value;
      $value = urldecode($value);
      if (isset(DdCore::$pathTranslation[$fieldName])) {
        $f = DdCore::$pathTranslation[$fieldName];
        if (($r = $f($value))) {
          list($newK, $newV) = $f($value);
          $this->addFilterByParam($newK, $newV);
        }
      }
      else {
        $this->paramFilterItems()->addF($fieldName, $value);
      }
    }
    elseif ($k == 'ne') {
      // Not Empty
      $this->paramFilterItems()->addNullFilter($k, false);
    }
    elseif (method_exists($this, $method)) {
      // Динамический метод
      $this->$method($v);
    }
    else {
      return false;
    }
    return true;
  }

  protected function paramFilterDateField() {
    return 'dateCreate';
  }

  public $year, $month, $day;
  protected $dateParam, $datePeriod, $dateType;

  protected function setFilterDate($dateParam) {
    $dateField = $this->paramFilterDateField();
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
        $this->day = $date[0];
        $this->month = $date[1];
        $this->year = $date[2];
        $this->dateType = self::$DATE_DMY;
      }
      elseif (count($date) == 2) {
        // Указан конкретный месяц
        $this->month = $date[0];
        $this->year = $date[1];
        $this->dateType = self::$DATE_MY;
      }
      elseif (count($date) == 1) {
        // Указан конкретный год
        $this->year = $date[0];
        $this->dateType = self::$DATE_Y;
      }
    }
    // Устанавливаем параметры для фильтров
    if ($this->dateType == self::$DATE_RANGE) {
      $this->paramFilterItems()->cond->addRangeFilter($dateField, $this->datePeriod['from']['y'].'-'.$this->datePeriod['from']['m'].'-'.$this->datePeriod['from']['d'], $this->datePeriod['to']['y'].'-'.$this->datePeriod['to']['m'].'-'.$this->datePeriod['to']['d'].' 23:59:59');
    }
    elseif ($this->dateType == self::$DATE_DMY) {
      // Заголовок типа "Литературные новости (11 августа 2009)"
      $m = Config::getVar('ruMonths2');
      //if (!empty($this->d['pageTitle'])) $this->setPageTitle($this->d['pageTitle'].' ('.$this->day.' '.mb_strtolower($m[(int)$this->month], CHARSET).' '.$this->year.')');
      $this->paramFilterItems()->cond->addRangeFilter($dateField, sprintf('%04d-%02d-%02d', $this->year, $this->month, $this->day), sprintf('%04d-%02d-%02d', $this->year, $this->month, $this->day + 1), null, DbCond::strictTo);
    }
    elseif ($this->dateType == self::$DATE_MY) {
      // Заголовок типа "Литературные новости (Август 2009)"
      //$m = Config::getVar('ruMonths');
      //if (!empty($this->d['pageTitle'])) $this->setPageTitle($this->d['pageTitle'].' ('.$m[(int)$this->month].' '.$this->year.')');
      $this->paramFilterItems()->addF($dateField, (int)$this->month, 'MONTH');
      $this->paramFilterItems()->addF($dateField, (int)$this->year, 'YEAR');
    }
    elseif ($this->dateType == self::$DATE_Y) {
      $this->paramFilterItems()->addF($dateField, (int)$this->year, 'YEAR');
    }
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
    $this->paramFilterItems()->addTagFilter($tagField, is_array($tagValue) ? $tagValue : explode(',', $tagValue), $byId);
  }

  protected $curUser;

  /**
   * @param   integer   ID пользователя
   */
  function setFilterUser($userId) {
    $this->curUser = Misc::checkEmpty(DbModelCore::get('users', $userId));
    $this->paramFilterItems()->addF('userId', $this->curUser['id']);
    /*
     * $this->d['itemsUser']
    if (empty($this->d['itemsUser'])) {
      $this->error404('Пользователь не найден');
      return;
    }
    if ($this->page->getS('ownerMode') == 'author') {
      $name = UsersCore::name($this->d['itemsUser']);
      $this->setPageTitle($this->d['pageTitle'].' — '.$name);
      $this->setPathData($this->tt->getPath(2), $name);
    }
    */
    /*
    $this->d['submenu'] = UserMenu::get(
      $this->d['itemsUser'],
      $this->d['page']['id'],
      $this->action
    );
    */
  }

}