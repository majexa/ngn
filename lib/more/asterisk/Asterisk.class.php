<?php

class Asterisk {

  const attemptNoRecall = 3;

  public $attemptNumber = 1;

  /**
   * by hang up running class AgiAction$project
   *
   * @param $context
   * @param $phone
   * @param int $id
   * @param array $data
   */
  function addOutgoingCall($phone, $id, array $data = []) {
    if (empty($data['project'])) $data['project'] = PROJECT_KEY;
    if (empty($data['actionName'])) $data['actionName'] = $data['project'];
    if (!ALLOW_SEND) return;
    $class = AgiAction::getClass($data['actionName']);
    if ($class::recall()) $this->addStartCalling($data['project'], $phone, $id);
    //$t = "Channel: SIP/{$phone}@80.75.130.136:5060";
    $t = "Channel: SIP/sipnet/$phone";
    $s = <<<CALL
$t
CallerID: +74666210228
MaxRetries: 0
WaitTime: 30
Context: common
Extension: s
Priority: 1

CALL;
    $data['id'] = $id;
    foreach ($data as $k => $v) $s .= "Set: $k=$v\n";
    $tmpFile = '/tmp/'.rand(10, 10000);
    LogWriter::str('addCall', $s);
    file_put_contents($tmpFile, $s);
    $file = '/var/spool/asterisk/outgoing/'.time().'-'.rand(100, 999).'.call';
    rename($tmpFile, $file);
  }

  function getRetryTime($attemptNumber) {
    if ($attemptNumber == 1) return 60 * 1;
    //if ($attemptNumber == 1) return 60 * 5;
    elseif ($attemptNumber == 2) {
      if (date('G') > 22 or date('G') < 6) {
        // секунд до 10:00
        if (date('G') >= 0) return mktime(10, 0, 0) - time();
        else return mktime(10, 0, 0, date('n'), date('j') + 1) - time();
      }
      else return 60 * 60; // час
      //else return 60 * 1; // час
    }
    else return -1; // не перезванивать
  }

  protected function addStartCalling($project, $phone, $id) {
    Misc::checkEmpty($id);
    $retryTime = $this->getRetryTime($this->attemptNumber);
    $scFolder = self::scFolder($project);
    if (!file_exists($scFolder)) `sudo mkdir --mode=0777 $scFolder`;
    Config::updateVar("$scFolder/$id.php", [
      'phone' => $phone,
      'startCallingTime' => time(),
      'attemptNumber' => $this->attemptNumber,
      'retryTime' => $retryTime
    ]);
    //`sudo chmod 0777 $scFolder/$id.php`; // для того, что бы перезапись этого файла прошла нормально
    `sudo chown user:user $scFolder/$id.php`;
  }

  static function scFolder($project) {
    return "/usr/share/asterisk/agi-bin/startCalling/$project";
  }

}