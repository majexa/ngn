<?php

class DdCalendar extends Calendar {

  /**
   * Текущий путь страницы
   *
   * @var string
   */
  public $basePath;

  /**
   * Дни, с существующими данными
   *
   * @var array
   */
  protected $daysDataExists;

  protected $strName;

  function __construct($strName, $basePath) {
    $this->strName = $strName;
    $this->basePath = $basePath;
    $this->setStartDay(1);
  }

  function getDaysDataExists($month, $year) {
    return db()->selectCol(<<<SQL
SELECT
  DISTINCT DAY(eventDate)
FROM dd_i_{$this->strName}
WHERE
  MONTH(eventDate)=$month AND
  YEAR(eventDate)=$year
SQL
    );
  }

  function getMonthView($month, $year) {
    $this->daysDataExists = $this->getDaysDataExists($month, $year);
    return parent::getMonthView($month, $year);
  }

  function getDateLink($day, $month, $year) {
    if (in_array($day, $this->daysDataExists)) return $this->basePath."/d.$day;$month;$year";
    return '';
  }

}
