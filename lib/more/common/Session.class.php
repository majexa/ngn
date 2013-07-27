<?php

if (!defined('SESSION_EXPIRES')) define('SESSION_EXPIRES', 604800);

class Session {
  
  /**
   * Через сколько секунд после старта сессии она истекает
   *
   * @var integer
   */
  static $expires = SESSION_EXPIRES;
  
  static $started = false;

  /**
   * Open the session
   * @return bool
   */
  static function open() {
  }

  /**
   * Close the session
   * @return bool
   */
  static function close() {
  }

  /**
   * Read the session
   * @param int session id
   * @return string string of the sessoin
   */
  static function read($id) {
    return db()->selectCell("SELECT data FROM sessions WHERE id=?", $id);
  }

  /**
   * Write the session
   * @param int session id
   * @param string data of the session
   */
  static function write($id, $data) {
    if (!$data) return;
    db()->query("REPLACE INTO sessions SET id=?, expires=?, data=?", $id, time() + self::$expires, $data);
    if (!($id = db()->selectCell("SELECT id FROM sessions WHERE id=?", $id))) {
      db()->query("INSERT INTO sessions SET id=?, expires=?, data=?", $id, time() + self::$expires, $data);
    } else {
      db()->query("UPDATE sessions SET expires=?, data=? WHERE id=?", time() + self::$expires, $data, $id);
    }
  }

  /**
   * Destoroy the session
   * @param int session id
   * @return bool
   */
  static function destroy($id) {
    return db()->query("DELETE FROM sessions WHERE id=?", $id);
  }

  
  static function delete() {
    self::destroy($_COOKIE[ini_get('session.name')]);
  }
  
  /**
   * Garbage Collector
   * @param int life time (sec.)
   * @return bool
   * @see session.gc_divisor      100
   * @see session.gc_maxlifetime 1440
   * @see session.gc_probability    1
   * @usage execution rate 1/100
   *        (session.gc_probability/session.gc_divisor)
   */
  static function gc($max) {
    return db()->query("DELETE FROM sessions WHERE expires < ?", time());
  }
  
  static function init() {
    if (self::$started) return;
    //ini_set('session.cookie_domain', SITE_DOMAIN);
    ini_set('session.gc_maxlifetime', self::$expires);
    ini_set('session.cookie_lifetime', self::$expires);

    /**
     * Если будем определять INI-переменную 'session.cookie_domain', то для доменов
     * 1-го уровня (пример: domainname) куки не ставятся (почему-то).
     * Так что делаем теперь проверку какого уровня домен и будем назначать в зависимости 
     * от этого выставлять куки-домен или нет.
     * Проверим наше убеждение из первого предложения.
     * ================================================
     * 
     * Сделать возможность авторизации по 4-м типам:
     * 1) Сайт, находящийся на домене 2-го уровня, а так же на доменах, 3-го
     *    уровнят
     * 2) Сайт, находящийся только на домене 2-го уровня и домене "www" 3-го уровня
     * 3) Сайт, находящийся только на домене 3-го уровня
     * 4) Сайт, находящийся на домене произвольного типа и имеющий зеркала 
     *    произвольного типа
     *
     */
    ini_set('session.save_handler', 'user');
    session_set_save_handler(
      ['Session', 'open'],
      ['Session', 'close'],
      ['Session', 'read'],
      ['Session', 'write'],
      ['Session', 'destroy'],
      ['Session', 'gc']
    );
    if (!session_id()) session_start();
    self::$started = true;
  }
  
}