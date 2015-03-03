<?php

class Calendar {

  /**
   * Get the array of strings used to label the days of the week. This array contains seven
   * elements, one for each day of the week. The first entry in this array represents Sunday.
   *
   * @return array
   */
  function getDayNames() {
    return $this->dayNames;
  }

  /**
   * Set the array of strings used to label the days of the week. This array must contain seven
   * elements, one for each day of the week. The first entry in this array represents Sunday.
   *
   * @param $names
   */
  function setDayNames($names) {
    $this->dayNames = $names;
  }

  /**
   * Get the array of strings used to label the months of the year. This array contains twelve
   * elements, one for each month of the year. The first entry in this array represents January.
   *
   * @return array
   */
  function getMonthNames() {
    return $this->monthNames;
  }

  /**
   * Set the array of strings used to label the months of the year. This array must contain twelve
   * elements, one for each month of the year. The first entry in this array represents January.
   *
   * @param $names
   */
  function setMonthNames($names) {
    $this->monthNames = $names;
  }

  /**
   * Gets the start day of the week. This is the day that appears in the first column
   * of the calendar. Sunday = 0.
   *
   * @return int
   */
  function getStartDay() {
    return $this->startDay;
  }

  /**
   * Sets the start day of the week. This is the day that appears in the first column
   * of the calendar. Sunday = 0.
   *
   * @param $day
   */
  function setStartDay($day) {
    $this->startDay = $day;
  }

  /**
   * Gets the start month of the year. This is the month that appears first in the year
   * view. January = 1.
   *
   * @return int
   */
  function getStartMonth() {
    return $this->startMonth;
  }

  /**
   * Sets the start month of the year. This is the month that appears first in the year
   * view. January = 1.
   *
   * @param $month
   */
  function setStartMonth($month) {
    $this->startMonth = $month;
  }


  /**
   * Return the URL to link to in order to display a calendar for a given month/year.
   * You must override this method if you want to activate the "forward" and "back"
   * feature of the calendar.
   *
   * Note: If you return an empty string from this function, no navigation link will
   * be displayed. This is the default behaviour.
   * If the calendar is being displayed in "year" view, $month will be set to zero.
   *
   * @param $month
   * @param $year
   * @return string
   */
  function getCalendarLink($month, $year) {
    return "";
  }

  /**
   * Return the URL to link to  for a given date.
   * You must override this method if you want to activate the date linking feature of the calendar.
   *
   * Note: If you return an empty string from this function, no navigation link will
   * be displayed. This is the default behaviour.
   *
   * @param $day
   * @param $month
   * @param $year
   * @return string
   */
  function getDateLink($day, $month, $year) {
    return "";
  }

  /**
   * Return the HTML for the current month
   *
   * @return mixed
   */
  function getCurrentMonthView() {
    $d = getdate(time());
    return $this->getMonthView($d["mon"], $d["year"]);
  }

  /**
   * Return the HTML for the current year
   *
   * @return string
   */
  function getCurrentYearView() {
    $d = getdate(time());
    return $this->getYearView($d["year"]);
  }

  /**
   * Return the HTML for a specified month
   *
   * @param $month
   * @param $year
   * @return mixed
   */
  function getMonthView($month, $year) {
    return $this->getMonthHTML($month, $year);
  }

  /**
   * Return the HTML for a specified year
   *
   * @param $year
   * @return string
   */
  function getYearView($year) {
    return $this->getYearHTML($year);
  }

  /**
   * Calculate the number of days in a month, taking into account leap years
   *
   * @param $month
   * @param $year
   * @return int
   */
  function getDaysInMonth($month, $year) {
    if ($month < 1 or $month > 12) return 0;
    $d = $this->daysInMonth[$month - 1];
    if ($month == 2) {
      // Check for leap year
      // Forget the 4000 rule, I doubt I'll be around then...
      if ($year % 4 == 0) {
        if ($year % 100 == 0) {
          if ($year % 400 == 0) {
            $d = 29;
          }
        }
        else {
          $d = 29;
        }
      }
    }

    return $d;
  }

  public $ssssTable = '
<div id="calendarHeader">
<a href="$prevMonthLink" class="prev">«</a>
<a href="$monthLink" class="current">$header</a>
<a href="$nextMonthLink" class="next">»</a>
</div>
<table cellspacing="0">
<tr>
  <th><b>$weekday1</b></th>
  <th><b>$weekday2</b></th>
  <th><b>$weekday3</b></th>
  <th><b>$weekday4</b></th>
  <th><b>$weekday5</b></th>
  <th><b>$weekday6</b></th>
  <th><b>$weekday7</b></th>
</tr>
$rows
</table>    
';
  //
  /*
  <table cellspacing="0">
  <tr>
    <th><b>` . $this->dayNames[($this->startDay)%7] . `</b></th>
    <th><b>` . $this->dayNames[($this->startDay+1)%7] . `</b></th>
    <th><b>` . $this->dayNames[($this->startDay+2)%7] . `</b></th>
    <th><b>` . $this->dayNames[($this->startDay+3)%7] . `</b></th>
    <th><b>` . $this->dayNames[($this->startDay+4)%7] . `</b></th>
    <th><b>` . $this->dayNames[($this->startDay+5)%7] . `</b></th>
    <th><b>` . $this->dayNames[($this->startDay+6)%7] . `</b></th>
  </tr>
  */

  public $ddddRow = '`<tr>`.$cells.`</tr>`';
  public $ddddCell = '`<td>`.($empty ? `&nbsp;` : ($link ? `<a href="`.$link.`"`.($class ? ` class="`.$class.`"` : ``).`>`.$day.`</a>` : `<span>`.$day.`</span>`)).`</td>`';

  /**
   * Generate the HTML for a given month
   *
   * @param $m
   * @param $y
   * @param int $showYear
   * @return mixed
   * @throws Exception
   */
  function getMonthHTML($m, $y, $showYear = 1) {
    $a = $this->adjustDate($m, $y);
    $month = $a[0];
    $year = $a[1];
    $daysInMonth = $this->getDaysInMonth($month, $year);
    $date = getdate(mktime(12, 0, 0, $month, 1, $year));
    $first = $date['wday'];
    $monthName = $this->monthNames[$month - 1];
    $prev = $this->adjustDate($month - 1, $year);
    $next = $this->adjustDate($month + 1, $year);
    if ($showYear == 1) {
      $prevMonth = $this->getCalendarLink($prev[0], $prev[1]);
      $nextMonth = $this->getCalendarLink($next[0], $next[1]);
    }
    else {
      $prevMonth = '';
      $nextMonth = '';
    }
    $header = $monthName.(($showYear > 0) ? ' '.$year : '');
    /*
    $s .= "<table cellspacing=\"0\" class=\"monthHeader\">\n";
    $s .= "<tr>\n";
    //$s .= "<th>" . (($prevMonth == "") ? "&nbsp;" : "<a href=\"$prevMonth\">&lt;&lt;</a>")  . "</th>\n";
    $s .= "<th>$header</th>\n";
    //$s .= "<th>" . (($nextMonth == "") ? "&nbsp;" : "<a href=\"$nextMonth\">&gt;&gt;</a>")  . "</th>\n";
    $s .= "</tr>\n";
    $s .= "</table>\n";

    $s .= "<table cellspacing=\"0\">\n";
    $s .= "<tr>\n";
    $s .= "<th><b>" . $this->dayNames[($this->startDay)%7] . "</b></th>\n";
    $s .= "<th><b>" . $this->dayNames[($this->startDay+1)%7] . "</b></th>\n";
    $s .= "<th><b>" . $this->dayNames[($this->startDay+2)%7] . "</b></th>\n";
    $s .= "<th><b>" . $this->dayNames[($this->startDay+3)%7] . "</b></th>\n";
    $s .= "<th><b>" . $this->dayNames[($this->startDay+4)%7] . "</b></th>\n";
    $s .= "<th><b>" . $this->dayNames[($this->startDay+5)%7] . "</b></th>\n";
    $s .= "<th><b>" . $this->dayNames[($this->startDay+6)%7] . "</b></th>\n";
    $s .= "</tr>\n";
    */
    // We need to work out what date to start at so that the first appears in the correct column
    $d = $this->startDay + 1 - $first;
    while ($d > 1) $d -= 7;
    // Make sure we know when today is, so that we can use a different CSS style
    $today = getdate(time());
    $rows = '';
    while ($d <= $daysInMonth) {
      $cells = '';
      for ($i = 0; $i < 7; $i++) {
        $isToday = ($year == $today['year'] and $month == $today['mon'] and $d == $today['mday']);
        $selected = ($year == $this->selectedYear and $month == $this->selectedMonth and $d == $this->selectedDay);
        if ($d > 0 and $d <= $daysInMonth) {
          $link = $this->getDateLink($d, $month, $year);
          $empty = false;
        }
        else {
          $link = '';
          $empty = true;
        }
        $class = '';
        if ($selected) $class .= ' selected';
        if ($isToday) $class .= ' today';
        $cells .= St::dddd($this->ddddCell, [
          'class'    => trim($class),
          'today'    => $isToday,
          'selected' => $selected,
          'day'      => $d,
          'link'     => $link,
          'empty'    => $empty
        ]);
        $d++;
      }
      $rows .= St::dddd($this->ddddRow, ['cells' => $cells]);
    }
    $d = [
      'm'         => $m,
      'y'         => $y,
      'prevMonth' => $prevMonth,
      'nextMonth' => $nextMonth,
      'header'    => $header,
      'rows'      => $rows
    ];
    for ($i = 0; $i <= 7; $i++) $d['weekday'.($i + 1)] = $this->dayNames[($this->startDay + $i) % 7];
    $d['prevMonthLink'] = '';// $this->basePath.'/d.'.$m.';'.$y;
    $d['nextMonthLink'] = '';//$this->basePath.'/d.'.$m.';'.$y;
    return St::ssss($this->ssssTable, $d);
  }


  /*
      Generate the HTML for a given year
  */
  function getYearHTML($year) {
    $s = '';
    $prev = $this->getCalendarLink(0, $year - 1);
    $next = $this->getCalendarLink(0, $year + 1);

    $s .= "<table>\n";
    $s .= "<tr>";
    $s .= "<td>".(($prev == "") ? "&nbsp;" : "<a href=\"$prev\">&lt;&lt;</a>")."</td>\n";
    $s .= "<th class=\"calendarHeader\" valign=\"top\" align=\"center\">".(($this->startMonth > 1) ? $year." - ".($year + 1) : $year)."</th>\n";
    $s .= "<td>".(($next == "") ? "&nbsp;" : "<a href=\"$next\">&gt;&gt;</a>")."</td>\n";
    $s .= "</tr>\n";
    $s .= "<tr>";
    $s .= "<td>".$this->getMonthHTML(0 + $this->startMonth, $year, 0)."</td>\n";
    $s .= "<td>".$this->getMonthHTML(1 + $this->startMonth, $year, 0)."</td>\n";
    $s .= "<td>".$this->getMonthHTML(2 + $this->startMonth, $year, 0)."</td>\n";
    $s .= "</tr>\n";
    $s .= "<tr>\n";
    $s .= "<td>".$this->getMonthHTML(3 + $this->startMonth, $year, 0)."</td>\n";
    $s .= "<td>".$this->getMonthHTML(4 + $this->startMonth, $year, 0)."</td>\n";
    $s .= "<td>".$this->getMonthHTML(5 + $this->startMonth, $year, 0)."</td>\n";
    $s .= "</tr>\n";
    $s .= "<tr>\n";
    $s .= "<td>".$this->getMonthHTML(6 + $this->startMonth, $year, 0)."</td>\n";
    $s .= "<td>".$this->getMonthHTML(7 + $this->startMonth, $year, 0)."</td>\n";
    $s .= "<td>".$this->getMonthHTML(8 + $this->startMonth, $year, 0)."</td>\n";
    $s .= "</tr>\n";
    $s .= "<tr>\n";
    $s .= "<td>".$this->getMonthHTML(9 + $this->startMonth, $year, 0)."</td>\n";
    $s .= "<td>".$this->getMonthHTML(10 + $this->startMonth, $year, 0)."</td>\n";
    $s .= "<td>".$this->getMonthHTML(11 + $this->startMonth, $year, 0)."</td>\n";
    $s .= "</tr>\n";
    $s .= "</table>\n";

    return $s;
  }

  /**
   * Adjust dates to allow months > 12 and < 0. Just adjust the years appropriately.
   * e.g. Month 14 of the year 2001 is actually month 2 of year 2002.
   *
   * @param $month
   * @param $year
   * @return array
   */
  function adjustDate($month, $year) {
    $a = [];
    $a[0] = $month;
    $a[1] = $year;
    while ($a[0] > 12) {
      $a[0] -= 12;
      $a[1]++;
    }
    while ($a[0] <= 0) {
      $a[0] += 12;
      $a[1]--;
    }
    return $a;
  }

  /**
   * The start day of the week. This is the day that appears in the first column
   * of the calendar. Sunday = 0.
   *
   * @var int
   */
  public $startDay = 0;

  /**
   * The start month of the year. This is the month that appears in the first slot
   * of the calendar in the year view. January = 1.
   *
   * @var int
   */
  public $startMonth = 1;

  /*
      The labels to display for the days of the week. The first entry in this array
      represents Sunday.
  */
  //public $dayNames = array("S", "M", "T", "W", "T", "F", "S");
  public $dayNames = ["Вс", "Пн", "Вт", "Ср", "Чт", "Пт", "Сб"];

  //public $monthNames = array("January", "February", "March", "April", "May", "June",
  //                        "July", "August", "September", "October", "November", "December");

  public $monthNames = [
    "Январь",
    "Февраль",
    "Март",
    "Апрель",
    "Май",
    "Июнь",
    "Июль",
    "Август",
    "Сентябрь",
    "Октябрь",
    "Ноябрь",
    "Декабрь"
  ];

  /**
   * The number of days in each month. You're unlikely to want to change this...
   * The first entry in this array represents January.
   *
   * @var array
   */
  public $daysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];

  public $selectedDay;
  public $selectedMonth;
  public $selectedYear;

  public function setSelectedToday() {

  }

}