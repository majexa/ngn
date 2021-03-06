<?php

class Curl {
  use DebugOutput;

  public $getHeaders = false; // headers will be added to output 
  public $getContent = true; // contens will be added to output 
  public $followRedirects = true; // should the class go to another URL, if the current is "HTTP/1.1 302 Moved Temporarily" 
  public $encoding = 'utf-8';
  public $decodeResult = true;
  public $defaultInputEncoding = 'windows-1251';
  private $fCookieFile;
  public $fSocket;

  function __construct() {
    $this->fCookieFile = tempnam("/tmp", "g_");
    $this->init();
  }

  protected function init() {
    $this->fSocket = curl_init();
    $this->loadDefaults();
  }

  protected function loadDefaults() {
    $this->setopt(CURLOPT_RETURNTRANSFER, true);
    $this->setopt(CURLOPT_FOLLOWLOCATION, $this->followRedirects);
    $this->setopt(CURLOPT_REFERER, "http://google.com");
    $this->setopt(CURLOPT_VERBOSE, false);
    $this->setopt(CURLOPT_SSL_VERIFYPEER, false);
    $this->setopt(CURLOPT_SSL_VERIFYHOST, false);
    $this->setopt(CURLOPT_HEADER, $this->getHeaders);
    $this->setopt(CURLOPT_NOBODY, !$this->getContent);
    $this->setopt(CURLOPT_COOKIEJAR, $this->fCookieFile);
    $this->setopt(CURLOPT_COOKIEFILE, $this->fCookieFile);
    $this->setopt(CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.0; WOW64) AppleWebKit/535.2 (KHTML, like Gecko) Chrome/15.0.874.120 Safari/535.2");
  }

  function setopt($opt, $value) {
    return curl_setopt($this->fSocket, $opt, $value);
  }

  function destroy() {
    return curl_close($this->fSocket);
  }

  function head($url) {
    if (!$this->fSocket) return false;
    /*
    $this->getHeaders = true; 
    $this->getContent = false; 
    $this->loadDefaults();
    $this->setopt(CURLOPT_REFERER, Misc::getHostUrl($url)); 
    $this->setopt(CURLOPT_POST, 0); 
    $this->setopt(CURLOPT_CUSTOMREQUEST,'HEAD');
    $this->setopt(CURLOPT_URL, $url);
    */
    $this->setopt(CURLOPT_URL, $url);
    //$urlP = parse_url($url);
    //$this->setopt(CURLOPT_REFERER, 'http://'.['host']);
    $this->setopt(CURLOPT_RETURNTRANSFER, 1);
    $this->setopt(CURLOPT_HEADER, 1);
    $this->setopt(CURLOPT_NOBODY, 1);
    $this->setopt(CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($this->fSocket);
    //$this->destroy(); 
    return $result;
  }

  function exists($url) {
    Misc::checkEmpty($url);
    $t = $this->head($url);
    if ($t === false) throw new Exception("strange");
    $headers = HttpResult::parseWithHeaders($t, false);
    return $headers[0]['Code'] == '200';
  }

  function get($url, $withHeaders = false) {
    if (!$this->fSocket) return false;
    $this->setopt(CURLOPT_RETURNTRANSFER, true);
    $this->setopt(CURLOPT_NOBODY, 0);
    $this->setopt(CURLOPT_POST, 0);
    $this->setopt(CURLOPT_URL, $url);
    if ($withHeaders) $this->setopt(CURLOPT_HEADER, 1);
    $result = $this->exec();
    $result = $this->decodeResult ? $this->convert($result) : $result;
    if ($withHeaders) {
      $pos = strpos($result, "\r\n\r\n");
      $header = substr($result, 0, $pos);
      $body = substr($result, $pos + 4, strlen($result));
      return [$header, $body];
    }
    return $result;
  }

  function copy($url, $file) {
    $this->setopt(CURLOPT_URL, $url);
    $this->setopt(CURLOPT_BINARYTRANSFER, 1);
    $this->setopt(CURLOPT_TIMEOUT, 320);
    file_put_contents($file, $this->exec());
  }

  function post($url, $postData, $arr_headers = []) {
    if (!$this->fSocket) return false;
    $this->setopt(CURLOPT_HEADER, 0);
    $this->setopt(CURLOPT_POST, 1);
    if (!empty($postData)) {
      $postData = $this->compilePostData($postData);
      $this->setopt(CURLOPT_POSTFIELDS, $postData);
    }
    if (!empty($arr_headers)) $this->setopt(CURLOPT_HTTPHEADER, $arr_headers);
    $this->setopt(CURLOPT_URL, $url);
    $result = curl_exec($this->fSocket);
    return $result;
  }

  function exec() {
    return curl_exec($this->fSocket);
  }

  /**
   * @param string $url URL
   * @return HttpResult
   * @throws Exception
   */
  function getObj($url) {
    $this->init();
    if (!$this->fSocket) return false;
    $this->setopt(CURLOPT_URL, $url);
    $this->setopt(CURLOPT_REFERER, Misc::getHostUrl($url));
    $this->setopt(CURLOPT_RETURNTRANSFER, 1);
    $this->setopt(CURLOPT_HEADER, 1);
    $this->setopt(CURLOPT_NOBODY, 0);
    $this->setopt(CURLOPT_FOLLOWLOCATION, 1);
    $result = curl_exec($this->fSocket);
    $this->destroy();
    if (!$result) throw new Exception("No result by url '$url'");
    return new HttpResult($this->convert($result));
  }

  function check200($url) {
    return $this->getObj($url)->getAllHeaders()[0]['Code'] == 200 ? true : false;
  }

  function check200AndThrow($url) {
    if (!$this->check200($url)) {
      throw new Exception("Unavailable url '$url'");
    }
  }

  protected function convert($text) {
    return $text;
    // return $this->detectUTF8($this->getParsed($text, '<title>', '</title>')) ? $text : iconv($this->defaultInputEncoding, $this->encoding.'//IGNORE', $text);
  }

  function info($url, $opt) {
    $this->setopt(CURLOPT_URL, $url);
    $this->exec();
    return curl_getinfo($this->fSocket, $opt);
  }

  protected function compilePostData($postData) {
    return http_build_query($postData);
  }

  function getParsed($result, $bef, $aft = '') {
    $len = strlen($bef);
    $posBef = strpos($result, $bef);
    if ($posBef === false) return '';
    $posBef += $len;
    if (empty($aft)) {
      // try to search up to the end of line 
      $posAft = strpos($result, "\n", $posBef);
      if ($posAft === false) $posAft = strpos($result, "\r\n", $posBef);
    }
    else
      $posAft = strpos($result, $aft, $posBef);

    if ($posAft !== false) $rez = substr($result, $posBef, $posAft - $posBef);
    else
      $rez = substr($result, $posBef);
    return $rez;
  }

  function detectUTF8($string) {
    return preg_match('%(?:
        [\xC2-\xDF][\x80-\xBF]             # non-overlong 2-byte
        |\xE0[\xA0-\xBF][\x80-\xBF]        # excluding overlongs
        |[\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
        |\xED[\x80-\x9F][\x80-\xBF]        # excluding surrogates
        |\xF0[\x90-\xBF][\x80-\xBF]{2}     # planes 1-3
        |[\xF1-\xF3][\x80-\xBF]{3}         # planes 4-15
        |\xF4[\x80-\x8F][\x80-\xBF]{2}     # plane 16
        )+%xs', $string);
  }

  function getCode($url) {
    $this->setopt(CURLOPT_URL, $url);
    $this->exec();
    return curl_getinfo($this->fSocket, CURLINFO_HTTP_CODE);
  }

}
