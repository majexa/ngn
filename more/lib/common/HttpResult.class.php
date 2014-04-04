<?php

class HttpResult {

  private $text;

  private $body;

  private $headers;

  /**
   * @param   string  Тело HTTP документа вместе с заголовками
   */
  function __construct($text) {
    if (empty($text)) throw new Exception('$text can not be empty');
    $this->text = $text;
  }

  const ERR_EMPTY_BODY = 10;

  static function parseWithHeaders($text, $hasBody = true) {
    $headers = []; // Тексты заголовков, разделенных "\n\n" 
    $text = str_replace("\r\n", "\n", $text);
    $text = ' '.$text;
    $offset = 0;
    $prevPos = 1;
    while (($pos = strpos($text, 'HTTP', $offset))) {
      $offset = $pos + 1;
      if ($pos - 1 == 0 or ($text[$pos - 1] == "\n" and $text[$pos - 2] == "\n")) {
        if ($prevPos != $pos) {
          $headers[] = substr($text, $prevPos, $pos - $prevPos - 2);
        }
        $prevPos = $pos;
      }
    }
    if ($hasBody) {
      $pos = strpos($text, "\n\n", $prevPos); // Позиция начала тела
      if (!($body = substr($text, $pos + 2, 99999999))) throw new Exception('Body is empty', self::ERR_EMPTY_BODY);
    }
    if (($header = substr($text, $prevPos, $pos - $prevPos))) $headers[] = $header;
    if (empty($headers[0])) throw new Exception('Headers is empty. $prevPos='.$prevPos.', $pos-$prevPos='.($pos - $prevPos).' $text: '.getPrr($text));
    foreach ($headers as $k => $h) {
      $h = explode("\n", trim($h));
      $hh = explode(' ', $h[0]);
      $hhh = explode('/', $hh[0]);
      $r[$k]['Protocol'] = $hhh[0];
      if (empty($hhh[1])) throw new Exception('$hhh[1] is empty. $hhh: '.getPrr($hhh));
      $r[$k]['ProtocolVersion'] = $hhh[1];
      $r[$k]['Code'] = $hh[1];
      $r[$k]['CodeName'] = $hh[2];
      for ($i = 1; $i < count($h); $i++) {
        preg_match('/([a-z-]+): (.*)/si', $h[$i], $m);
        $r[$k][$m[1]] = $m[2];
      }
    }
    return $hasBody ? [$r, $body] : $r;
  }

  protected function init() {
    if (!empty($this->headers)) return;
    list($this->headers, $this->body) = $this->parseWithHeaders($this->text);
  }

  function getFinalUrl() {
    $this->init();
    if (count($this->headers) < 0) return false;
    return $this->headers[count($this->headers) - 1]['Location'];
  }

  function getAllHeaders() {
    $this->init();
    return $this->headers;
  }

  function getLastHeaders() {
    $this->init();
    return $this->headers[count($this->headers) - 1];
  }

  function getBody() {
    $this->init();
    return $this->body;
  }

}