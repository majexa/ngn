<?php

class Pagination extends Options2 {

  public $options = [
    'n' => 10,
    'sep' => ' ',
    'maxPages' => 9,
    'dddd' => '`<a href="`.$link.`"><span>`.$title.`</span></a>`',
    'ddddSelected' => '`<b><span>`.$title.`</span></b>`',
    'forceShowTableStatus' => false,
    'req' => null,
    'type' => '',
    'desc' => false,
    'page' => null,
    'selfPage' => null
  ];
  
  /**
   * @var Req
   */
  protected $req;
  
  protected function init() {
    $this->req = $this->options['req'] ?: O::get('Req');
  }
  
  function get($table, $cond = '', $selectCond = '') {
    $cond = is_object($cond) ? $cond->all() : $cond;
    if ($cond or $this->options['forceShowTableStatus']) {
      $cnt = db()->selectCell("SELECT COUNT(*) AS count$selectCond FROM $table".$cond);
    } else {
      $r = db()->selectRow("SHOW TABLE STATUS LIKE '$table'");
      $cnt = $r['Rows'];
    }
    $res = $this->data($cnt);
    $r = [$res[0], $res[1].','.($res[2]), $res[3], $res[4]];
    return $r;
  }
  
  function get2($cnt) {
    $res = $this->data($cnt);
    return [$res[0], $res[1].','.$res[2], $res[3], $res[4]];
  }
  
  function data($all) {
    if ($this->req->pg) {
      $page = isset($this->req->pg[$this->options['type']]) ? $this->req->pg[$this->options['type']] : 1;
    } else {
      $page = 1;
    }
    if ($page <= 0) $page = 1; // Если №страницы меньше или равен 0, считаем, что это первая страница
    if ($this->options['n'] == 0) $pagesN = 0;
    else {
      if ($all) $pagesN = ceil($all / $this->options['n']);
      else $pagesN = 1;
    }
    if ($page > $pagesN) $page = $pagesN; // Если №страницы больше возможного кол-ва страниц
    if ($this->options['desc']) $page = $pagesN - $page + 1;
    $html = '';
    if (!$this->options['page']) unset($this->req->r["page".$this->options['type']]);
    $links = [];
    if ($pagesN != 0 and $pagesN != 1) {
      $links = [];
      $descN = 0;
      $self = $this->req->pg ? Tt()->getPath(count($this->req->params)-1) : Path()->getPath();
      for ($i = 0; $i < $pagesN; $i++) {
        $pageNumber = $i + 1;
        $descN--;
        if ($i <= $page - round($this->options['maxPages'] / 2)-1 or
            $i >= $page + round($this->options['maxPages'] / 2)-1) continue;
        $qstr2 = $self.'/pg'.$this->options['type'].$pageNumber;
        $d = [
          'title' => $pageNumber,
          'link' => $this->options['selfPage'].$qstr2
        ];
        if (($i+1) == $page) $links[] = St::dddd($this->options['ddddSelected'], $d);
        else $links[] = St::dddd($this->options['dddd'], $d);
      }
    }
    if (count($links) > 0) $html = implode($this->options['sep'], $links);
    if ($this->options['n'] == 0) {
      $limit = '';
    } else {
      $offset = ($page-1) * $this->options['n'];
      $limit = $this->options['n'];
    }
    return [$html, $offset, $limit, $all, count($links)];
  }

}
