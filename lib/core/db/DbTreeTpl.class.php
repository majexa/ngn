<?php

class DbTreeTpl {
  
  protected $nodes;
  
  protected $nodeTpl;
  
  protected $nodesBeginTpl;
  
  protected $nodesEndTpl;
  
  protected $leafTpl;
  
  protected $depth = 1;
  
  protected $extData;
  
  protected $depthLimit = 9;
  
  protected $separator;
  
  function setNodes($nodes) {
    $this->nodes = $nodes;
  }
  
  function setNodeTpl($tpl) {
    $this->nodeTpl = $tpl;
  }
  
  function setSeparator($s) {
    $this->separator = $s;
  }
  
  function setNodesBeginTpl($tpl) {
    $this->nodesBeginTpl = $tpl;
  }
  
  function setNodesEndTpl($tpl) {
    $this->nodesEndTpl = $tpl;
  }
  
  function setLeafTpl($tpl) {
    $this->leafTpl = $tpl;
  }
  
  function setDepthLimit($limit) {
    $this->depthLimit = $limit;
  }
  
  function setTpl($tpl) {
    $this->setLeafTpl($tpl);
    $this->setNodeTpl($tpl);
  }
  
  function setExtData($data) {
    $this->extData = $data;
  }
  
  function html() {
    if (!isset($this->nodes)) throw new Exception('$this->nodes not defined');
    $html = '';
    $html .= $this->htmlTpl([], 'nodesBegin');
    $html .= $this->htmlNodes($this->nodes);
    $html .= $this->htmlTpl([], 'nodesEnd');
    return $html;
  }
  
  private function htmlNodes(&$nodes) {
    $html = '';
    $count = count($nodes);
    $n = 0;
    foreach ($nodes as &$node) {
      if ($n == 0) $node['first'] = true;
      if ($n == $count-1) $node['last'] = true;
      $node['depth'] = $this->depth;
      if (count($node['childNodes']) or !empty($node['childNodesExists'])) {
        $html .= $this->htmlTpl($node, 'node');
        if ($this->depth < $this->depthLimit) {
          $this->depth++;
          $html .= $this->htmlTpl($node, 'nodesBegin');
          $html .= $this->htmlNodes($node['childNodes']);
          $html .= $this->htmlTpl($node, 'nodesEnd');
          $this->depth--;
        }
      } else {
        $html .= $this->htmlTpl($node, 'leaf');
      }
      if (!empty($node['last'])) $html .= $this->separator;
      $n++;
    }
    return $html;
  }
  
  protected function prepareTpl(&$node) {}
  protected function prepareNode(&$node) {}
  
  private function htmlTpl($node, $tpl) {
    $_tpl = $tpl.'Tpl';
    if (isset($this->extData)) $node += $this->extData;
    if ($tpl == 'node' or $tpl == 'leaf') $this->prepareNode($node);
    return St::dddd($this->$_tpl, $node);
  }
  
}
