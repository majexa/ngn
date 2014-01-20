<?php

class FormSpamBlocker {

  public $initKey = "abcd1234"; // set some string here to make the encoded names and values hard to guess
  public $minutesAfterMidnight = 20; // minutes after midnight to allow a submission of a form generated at the previous day
  public $minTime = 2; // time in seconds needed to have passed, before o form can be submitted, set also by setTimeWindow()
  public $maxTime = 1200; // max time in seconds to submit a form, set also by setTimeWindow()
  public $hasTrap = true; // true: a visually hidden input tag will be generated, set also by setTrap()
  public $trapName = "email"; // name of the visually hidden input tag, set also by setTrap()
  public $trapLabel = "Do not enter anything in this text box otherwise your message will not be sent!"; // label info to warn human users, who do not use CSS, set also by setTrap()
  public $hasSession = true; // enables a session based method to prevent multiple submissions with the same parameters
  public $message = '';

  /**
   * param $bol: true to enable the trap tag, false to disable it [boolean, optional, default=true]
   * param $name: if given, it sets the name of the trap tag [string, optional, default=false]
   * param $label: if given, it sets the label of the trap tag [string, optional, default=false]
   *
   * @param bool $bol
   * @param bool $name
   * @param bool $label
   */
  function setTrap($bol = true, $name = false, $label = false) {
    if ($bol == false) $this->hasTrap = false;
    else {
      $this->hasTrap = true;
      if ($name) $this->trapName = $name;
      if ($label) $this->trapLabel = $label;
    }
  }

  /**
   * @param int time in seconds needed to have passed, before o form can be submitted [numeric, optional, default=2]
   * @param int max time in seconds to submit a form [numeric, optional, default=600]
   */
  function setTimeWindow($min = 2, $max = 600) {
    $this->minTime = $min;
    $this->maxTime = $max;
  }

  /**
   * generates the xhtml string for the required form input tags
   *
   * @return string
   */
  function makeTags() {
    $this->initCode();
    $this->sessionStart($this->codeInit);
    $out = "";
    $out .= $this->setCodeID();
    $out .= $this->userID();
    $out .= $this->dynID();
    if ($this->hasTrap) $out .= $this->trapID();
    return $out;
  }

  /**
   * param $arr: the $_POST or $_GET array sent by a form
   * checks if there are valid parameters in the $arr array
   *
   * @param array $arr
   * @param string $message
   * @return bool
   */
  function checkTags($arr = [], $message = '') {
    $this->message = $message;
    if ($arr[$this->keyName] && $arr[$this->keyName] != "") {
      $this->getCodeID($arr[$this->keyName]);
    }
    else {
      return false;
    }
    if ($this->sessionCheck($arr[$this->keyName]) && $this->checkUserID($arr) && $this->checkDynID($arr) && $this->checkTrap($arr)) return true;
    else return false;
  }

  /**
   * returns an array with the names of the generated form elements
   * [0]: Name of the element that contains the generated key
   * [1]: Name of the element that contains the generated userID
   * [2]: Name of the element that contains the generated dynID
   * [3]: Name of the element that contains the generated visually hidden element
   *
   * @return array
   */
  function getTagNames() {
    $tagNames = [];
    $tagNames[0] = $this->keyName;
    $tagNames[1] = $this->userIDName;
    $tagNames[2] = $this->dynIDName;
    $tagNames[3] = $this->trapName;
    return $tagNames;
  }

  protected $version = "v0.3 (030507)";
  protected $keyName = "ria_key";
  protected $sesName = "ria_ses_name";
  protected $userIDName = "";
  protected $dynIDName = "";
  protected $dynTime = "";

  /**
   * sets a session variable=0, if the public variable $hasSession==true, to prevent multiple submissions
   *
   * @param $sid
   */
  function sessionStart($sid) {
    if ($this->hasSession) {
      session_start();
      $_SESSION[$this->sesName] = 0;
      $_SESSION[$this->keyName] = $this->enc($sid);
    }
  }

  /**
   * checks a session variable, if the public variable $hasSession==true, to prevent multiple submissions
   *
   * @param $sid
   * @return bool
   */
  protected function sessionCheck($sid) {
    if ($this->hasSession) {
      session_start();
      $_SESSION[$this->sesName] = $_SESSION[$this->sesName] + 1;
      $sesNum = $_SESSION[$this->sesName];
      $sesKey = $_SESSION[$this->keyName];
      if ($sesNum == 1 && $sesKey == $this->enc($sid)) return true;
      else {
        $_SESSION[$this->sesName] = $sesNum++;
        return false;
      }
    }
    else return true;
  }

  /**
   * generates the xhtml string for the hidden input tag, that contains some unique userID
   *
   * @return string
   */
  protected function userID() {
    $userID = $this->intUserID();
    $tagName = substr($userID, $this->userIDNamestart, $this->userIDNameLength);
    $tagValue = substr($userID, $this->userIDValuestart, $this->userIDValueLength);
    $out = "<input type=\"hidden\" name=\"".$tagName."\" value=\"".$tagValue."\" />\n";
    $this->userIDName = $tagName;
    return $out;
  }

  /**
   * generates the xhtml string for the hidden input tag with a name, that changes daily
   *
   * @return string
   */
  protected function dynID() {
    $actDay = date("j");
    $actMonth = date("n");
    $actYear = date("Y");
    $actTime = time();
    $today = mktime(0, 0, 0, $actMonth, $actDay, $actYear);
    $tagName = substr($this->enc($today.$this->initKey), $this->dynIDNamestart, $this->dynIDNameLength);
    $tagValue = $this->enc($actTime, "base64");
    $out = "<input type=\"hidden\" name=\"".$tagName."\" value=\"".$tagValue."\" />\n";
    $this->dynIDName = $tagName;
    $this->dynTime = $actTime;
    return $out;
  }

  /**
   * generates the xhtml string for the hidden input tag, that contains the key do decrypt the code passed
   *
   * @return string
   */
  protected function setCodeID() {
    $out = "<input type=\"hidden\" name=\"".$this->keyName."\" value=\"".$this->codeInit."\" />\n";
    return $out;
  }

  /**
   * generates the xhtml string for the trag text input tag, that is hidden using CSS
   * if CSS is disabled, a human user will be warned no to enter anything in this box
   * It is a good idea to change the style="display:none" to class="somename"
   * and set in your external CSS .somename {display:none;} to confuse spambots even more
   *
   * @return string
   */
  protected function trapID() {
    $out = "<span style=\"display:none;visibility:hidden;\">\n";
    $out .= "<label for=\"".$this->trapName."\">".$this->trapLabel."</label>\n";
    $out .= "<input type=\"text\" name=\"".$this->trapName."\" id=\"".$this->trapName."\" value=\"\" />\n";
    $out .= "</span>\n";
    return $out;
  }

  /**
   * generates the unique userID
   *
   * @return string
   */
  protected function intUserID() {
    $actSystem = $_SERVER['HTTP_USER_AGENT'];
    $actIP = $_SERVER['REMOTE_ADDR'];
    $userID = $this->enc($actSystem.$actIP.$this->initKey);
    return $userID;
  }

  /**
   * encoding
   *
   * @param $var
   * @param bool $method
   * @return string
   */
  protected function enc($var, $method = false) {
    if ($method == "base64") return base64_encode($var);
    else return md5($var);
  }

  /**
   * generates the required parameters to encrypt the generated hidden names and values
   */
  protected function initCode() {
    $r1 = rand(10, 124);
    $r2 = rand(4, 12);
    $r3 = rand(17, 89);
    $r4 = rand(199, 489);
    $r5 = rand(1, 42);
    $r6 = rand(312, 999);
    $userIDNameStart = rand(0, 31);
    $userIDNameLength = (32 - $userIDNameStart);
    $userIDValueStart = rand(0, 31);
    $userIDValueLength = (32 - $userIDValueStart);
    $dynIDNameStart = rand(0, 31);
    $dynIDNameLength = (32 - $dynIDNameStart);
    $this->userIDNamestart = $userIDNameStart;
    $this->userIDNameLength = $userIDNameLength;
    $this->userIDValuestart = $userIDValueStart;
    $this->userIDValueLength = $userIDValueLength;
    $this->dynIDNamestart = $dynIDNameStart;
    $this->dynIDNameLength = $dynIDNameLength;
    $this->codeInit = $r1.".".$userIDNameStart.".".$r2.".".$userIDNameLength.".".$r3.".".$userIDValueStart.".".$r4.".".$userIDValueLength.".".$r5.".".$dynIDNameStart.".".$r6.".".$dynIDNameLength;
  }

  /**
   * sets the required perameters for the code decryption
   *
   * @param $key
   */
  protected function getCodeID($key) {
    $keys = explode(".", $key);
    $this->userIDNamestart = $keys[1];
    $this->userIDNameLength = $keys[3];
    $this->userIDValuestart = $keys[5];
    $this->userIDValueLength = $keys[7];
    $this->dynIDNamestart = $keys[9];
    $this->dynIDNameLength = $keys[11];
  }

  /**
   * checks if there is a valid userID in an array specified
   *
   * @param array $arr
   * @return bool
   */
  function checkUserID($arr = []) {
    $found = false;
    $userID = $this->intUserID();
    $tagName = substr($userID, $this->userIDNamestart, $this->userIDNameLength);
    $tagValue = substr($userID, $this->userIDValuestart, $this->userIDValueLength);
    foreach ($arr as $name => $value) {
      if ($tagName == $name && $tagValue == $value) {
        $found = true;
        $this->userIDName = $name;
      }
    }
    return $found;
  }

  /**
   * checks if there is a valid dynID in an array specified
   *
   * @param array $arr
   * @return bool
   */
  function checkDynID($arr = []) {
    $actDay = date("j");
    $actMonth = date("n");
    $actYear = date("Y");
    $now = time();
    $today = mktime(0, 0, 0, $actMonth, $actDay, $actYear);
    $yesterday = mktime(0, 0, 0, $actMonth, $actDay - 1, $actYear);
    $indelay = $now - $today - ($this->minutesAfterMidnight * 60);
    $checktoday = substr($this->enc($today.$this->initKey), $this->dynIDNamestart, $this->dynIDNameLength);
    $checkyesterday = substr($this->enc($yesterday.$this->initKey), $this->dynIDNamestart, $this->dynIDNameLength);
    foreach ($arr as $name => $value) {
      if ($name == $checktoday OR ($name == $checkyesterday && $indelay <= 0)) {
        $val = base64_decode($value);
        $this->dynTime = $val;
        if ($this->checkSubmisionTime($val)) {
          $found = true;
          $this->dynIDName = $name;
        }
      }
    }
    return $found;
  }

  /**
   * checks if the form was submitted within the time period, set by minTime and maxTime variables
   *
   * @param $var
   * @return bool
   */
  protected function checkSubmisionTime($var) {
    $now = time();
    $elapsed = $now - $var;
    if (($elapsed < $this->minTime) OR ($elapsed > $this->maxTime)) return false;
    else return true;
  }

  /**
   * checks if a parameter, hidden by CSS, has some value
   *
   * @param array $arr
   * @return bool
   */
  protected function checkTrap($arr = []) {
    $noTrap = true;
    foreach ($arr as $name => $value) {
      if ($name == $this->trapName && $value != "") {
        $noTrap = false;
      }
    }
    return $noTrap;
  }
}
