<?php

class SendEmail {

  /**
   * @var PHPMailer
   */
  public $mailer;

  public $method;

  public $addHostToLinks = true;

  function __construct() {
    $this->mailer = new PHPMailer();
    $this->mailer->SetLanguage('ru');
    $this->mailer->SetFrom(Config::getVarVar('mail', 'fromEmail'), Config::getVarVar('mail', 'fromName'));
    $this->mailer->CharSet = CHARSET;
    $this->mailer->Mailer = Config::getVarVar('mail', 'method');
    $this->mailer->Encoding = 'base64'; //"8bit", "7bit", "binary", "base64", and "quoted-printable".
    if ($this->mailer->Mailer == 'smtp') {
      $smtp = Config::getVar('smtp');
      $this->mailer->Host = $smtp['server'];
      if (!empty($smtp['port'])) $this->mailer->Port = $smtp['port'];
      if (!empty($smtp['auth'])) {
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $smtp['user'];
        $this->mailer->Password = $smtp['pass'];
      }
    }
    $this->mailer->SMTPDebug = false;
  }

  protected function emailToRecipient($email) {
    return [
      'email' => $email,
      'name'  => substr($email, 0, strpos($email, '@'))
    ];
  }

  /**
   * @param   mixed   string|array
   * @param   string  Тема письма
   * @param   string  Текст письма
   * @param   bool    HTML/Plain text
   * @return  bool
   */
  function send($emails, $subject, $message, $html = true) {
    output("Try sending email to $emails");
    Misc::checkEmpty($emails, '$emails');
    if (defined('ALLOW_SEND') and ALLOW_SEND === false) {
      $this->log($emails, $subject, $message);
      return true;
    }
    if ($html and $this->addHostToLinks) {
      $message = str_replace("<a href=", "\n<a href=", $message);
      $message = preg_replace('/(href=["\'])(?!https?:\/\/|mailto:)\.*\/*(.*)(["\'])/imu', '$1'.SITE_WWW.'/$2$3', $message);
      $message = preg_replace('/(src=["\'])(?!https?:\/\/)\.*\/*(.*)(["\'])/imu', '$1'.SITE_WWW.'/$2$3', $message);
    }
    if (!is_array($emails)) {
      if (strstr($emails, ',')) {
        foreach (explode(',', $emails) as $v) {
          $recipients[] = $this->emailToRecipient(trim($v));
        }
      }
      else {
        $recipients[0] = $this->emailToRecipient($emails);
      }
    }
    else {
      foreach ($emails as &$email) if (!is_array($email)) $email = $this->emailToRecipient($email);
      $recipients = $emails;
    }
    if (!$recipients) throw new Exception('$recipients not defined');
    $e = $this->mailer;
    $e->Subject = $subject;
    $e->ClearAllRecipients();
    for ($i = 0; $i < count($recipients); $i++) $e->AddAddress($recipients[$i]['email'], $recipients[$i]['name']);
    if (!$html) {
      $e->IsHTML(false);
      $e->Body = $message;
    }
    else {
      $e->MsgHTML($message);
    }
    LogWriter::str('email', "$emails: $subject");
    output("Sending email to $emails");
    return $e->Send();
  }

}
