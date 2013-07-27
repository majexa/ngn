<? $d['calendar']['pagePath'] = $d['params'] ? $this->getPath() : $d['page']['path'] ?>

<div id="ddCalendar">
  <? $this->tpl('common/calendarInner', $d['calendar']) ?>
</div>
