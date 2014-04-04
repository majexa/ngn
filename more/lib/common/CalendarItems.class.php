<?php

class CalendarItems extends Calendar {

  /**
   * Класс из которого берутся данные для генерации ссылки
   *
   * @var DdItemsPage
   */
  public $items;

  /**
   * Массив с днями, для которых существуют данные
   * Пример структуры массива, где ключ - это день:
   * array(
   *   3 => 1,
   *   15 => 1,
   *   28 => 1
   * )
   *
   * @var array
   */
  public $daysDataExists;

  /**
   * Текущий путь страницы
   *
   * @var strgin
   */
  public $currentPath;

  function __construct($currentPath, DdItemsPage $items) {
    $this->currentPath = $currentPath;
    $this->items = $items;
    $this->setStartDay(1); // Устанавливаем первый день недели - понедельник
  }

  function getMonthView($month, $year) {
    $this->daysDataExists = $this->items->getMonthDaysDataExists($month, $year);
    return parent::getMonthView($month, $year);
  }

  function getDateLink($day, $month, $year) {
    if (in_array($day, $this->daysDataExists)) return $this->currentPath."/d.$day.$month.$year";
  }

}
