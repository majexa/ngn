<?php

/**
 * Page Actions
 * Контроллер вывода страницы.
 * Что делает:
 * 1. init() - вызывается до любых действий, осуществляемых в контроллере
 * 2. action() - осуществляет действия в зависимости от значения $this->req->r['action'] или параметров,
 *    заробранных Req'ом
 * 3. defaultAction() - вызывается если $this->isDefaultLayout = true и подготавливает данные вывода шаблона
 *
 */
abstract class CtrlCommon {
use Options;

  /**
   * Массив с данными для шаблона
   *
   * @var array
   */
  public $d = [];

  /**
   * Выводить ли страницу или действие происходит без вывода
   *
   * @var bool
   */
  public $hasOutput;

  /**
   * Определяет существование запрашиваемого экшена
   *
   * @var bool
   */
  public $isAction = false;

  /**
   * Запскать ли функию подготовки данных для страницы без экшенов
   *
   * @var string
   */
  public $isDefaultAction = true;

  /**
   * Текущий экшн страницы (берётся из $this->req->r['action'])
   *
   * @var string
   */
  public $action;

  /**
   * Экшн, который вызывается по умолчанию
   *
   * @var string
   */
  protected $defaultAction;

  /**
   * Префикс экшена (ajax, json, ...)
   *
   * Так к слову: Экшеном является строка состоящая из префикса и имени,
   * соединенных подчеркиванием, а не только одно имя без префикса
   *
   * @var string
   */
  public $actionPrefix;

  /**
   * Имя экшена без префикса
   *
   * @var string
   */
  public $actionBase;

  /**
   * Экшены с этими префиксами будут отключать вывод главного шаблона
   */
  public $noLayoutPrefixes = [
    'ajax', 'json', 'rss', 'xml'
  ];

  /**
   * Флаг определяет, является ли текущий запрос JSON-запросом
   *
   * @var bool
   */
  public $isJson;

  protected $isAjax;

  public $error404 = false;

  /**
   * Данные для формирования JSON-формата
   *
   * @var mixed
   */
  public $json;

  /**
   * ID пользователя
   *
   * @var integer
   */
  public $authUserId;

  public $allowRedirect = true;

  public $paramActionN;

  public $actionDisabled = false;

  protected $afterActionDisabled = false;

  public $allowRequestAction = true;

  public $ajaxSuccess;

  public $ajaxOutput;

  /**
   * Пример:
   * array(
   *   array(
   *     'action' => 'actionName',
   *     'ajaxTpl' => 'common/form'
   *   )
   * )
   *
   * @var array
   */
  protected $html2ajaxActions = [];

  public $tplTrace = [];

  /**
   * @var Router
   */
  public $router;

  /**
   * @var Req
   */
  public $req;

  /**
   * @var TT
   */
  public $tt;

  /**
   * @var TtPath
   */
  public $path;

  public function __construct(Router $router, array $options = []) {
    $this->router = $router;
    $this->setOptions($options);
    $this->req = empty($this->options['req']) ? $router->req : $this->options['req'];
    $this->tt = new Tt($this->req);
    $this->d['oController'] = $this;
    $this->d['ctrlName'] = $this->getName();
    if (!isset($this->defaultAction)) {
      if (method_exists($this, 'action_json_default')) $this->defaultAction = 'json_default';
      elseif (method_exists($this, 'action_ajax_default')) $this->defaultAction = 'ajax_default';
      else
        $this->defaultAction = 'default';
    }
  }

  public $subControllers = [];

  protected function addSubController(SubPa $oSubPa) {
    $this->subControllers[$oSubPa->getName()] = $oSubPa;
  }

  function __call($method, array $param = []) {
    foreach ($this->subControllers as $oSubPa) {
      if (is_callable([$oSubPa, $method])) {
        if ($oSubPa->disable) return;
        return call_user_func_array([$oSubPa, $method], $param);
      }
    }
    if (method_exists($this, $method)) {
      return call_user_func_array([$this, $method], $param);
    }
    else {
      throw new NoMethodException($method);
    }
  }

  private function callDirect($method, array $param = []) {
    call_user_func_array([$this, $method], $param);
  }

  function beforeAction() {
    if ($this->error404) return;
    $this->initParams();
    $this->setTheme();
    $this->setAuthUserId();
    $this->paramActionN = $this->getParamActionN();
    $this->addSubControllers();
    $this->initAction();
    $this->setPostAction();
    $this->setActionParams();
    $this->beforeInit();
    $this->init();
    $this->initSubControllers();
    $this->afterInit();
  }

  protected function addSubControllers() {
  }

  final protected function initSubControllers() {
    foreach ($this->subControllers as $o) $o->init();
  }

  public $actionResult = null;

  /**
   * Конструктор
   *
   * @param mixed   Все данные текущей страницы
   * @param array   Параметры запроса
   * @param string  Шаблон вывода по умолчанию
   * @param string  Действие
   */
  function dispatch() {
    if ($this->error404) return $this;
    $this->beforeAction();
    if (!$this->actionDisabled) {
      $this->actionResult = $this->action();
      $this->setDefaultTpl();
      if (!$this->afterActionDisabled) $this->afterAction();
    }
    $this->extendTplData();
    $this->prepareTplPath();
    return $this;
  }

  protected function setTheme() {
  }

  protected function beforeInit() {
  }

  protected function afterInit() {
  }

  protected $extendTplNames = [];

  /**
   * Определяем имя файла, который будет добавлять дополнительные
   * данные в $this->d
   *
   * @param   string  Имя файла
   */
  protected function setExtendTplName($name) {
    $this->extendTplNames[] = $name;
  }

  protected function setExtendTplNames($names) {
    $this->extendTplNames = $names;
  }

  /**
   * Добавляет дополнительные данные в $this->d
   */
  protected function extendTplData() {
  }

  protected function prepareTplPath() {
  }

  protected $output;

  /**
   * Вывод шаблона этого контроллера
   */
  function getOutput() {
    if ($this->isJson) {
      // JSON OUTPUT HERE
      if (!empty($this->req->r['ifr'])) return '<textarea id="json">'.json_encode($this->json).'</textarea>';
      else {
        if (JSON_DEBUG !== true) header('Content-type: application/json');
        if (is_string($this->json)) return $this->json;
        if ($this->actionDisabled) $this->json['actionDisabled'] = true;
        if (Sflm::$frontend) {
          if (($deltaUrl = Sflm::flm('js')->getDeltaUrl())) $this->json['sflJsDeltaUrl'] = $deltaUrl;
          if (($deltaUrl = Sflm::flm('css')->getDeltaUrl())) $this->json['sflCssDeltaUrl'] = $deltaUrl;
        }
        return json_encode($this->json);
      }
    }
    else {
      if (isset($this->ajaxSuccess) or isset($this->ajaxOutput)) {
        if (isset($this->ajaxSuccess)) return $this->ajaxSuccess ? 'success' : 'failed';
        else
          return $this->ajaxOutput;
      }
    }
    if (!$this->hasOutput) return '';
    header("Content-type: text/html; charset=".CHARSET);
    if (isset($this->output)) return $this->output;
    if (empty($this->d['tpl'])) {
      throw new Exception("<b>\$this->d['tpl']</b> in <b>".get_class($this)."</b> class not defined");
    }
    $html = $this->tt->getTpl($this->d['mainTpl'], $this->d);
    $this->d['processTime'] = getProcessTime();
    return $html;
  }

  /**
   * Здесь должны происходить операции, необходимые до вызова $this->action()
   */
  protected function init() {
  }

  /**
   * Должна определять параметры экшенов
   * ===================================
   *
   * Пример использования для _REQUEST параметра:
   *
   * $this->actionParams['json_citySearch'] = array(
   *   'name' => 'mask',
   *   'notRequired' => 1
   * )
   *
   * Пример использования для path-параметра:
   *
   * $this->actionParams['test'] = array(
   *   'n' => 5
   * )
   *
   * По умолчанию все параметры обязательны.....
   * Метод по умолчанию - param
   *
   */
  function setActionParams() {
  }

  protected function setAuthUserId() {
    $this->authUserId = Auth::get('id');
    $this->d['authorized'] = $this->authUserId ? true : false;
  }

  protected function initParams() {
    $this->d['curUrl'] = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    $this->d['params'] = $this->req->params = $this->req->params;
    $this->d['base'] = $this->req->getAbsBase();
  }

  protected function setActionIfNotDefined($action) {
    if (!isset($this->action)) $this->setAction($action);
  }

  protected function setActionIfNrequestAction($action) {
    if (!isset($this->req->r['action'])) $this->setAction($action);
  }

  protected $subAction = false;

  protected function setAction($action) {
    if (!preg_match('/^[A-Za-z_0-9]+$/', $action)) throw new Exception("Action name '$action' not allowed. Req: ".getPrr($this->req->r));
    if (Misc::hasPrefix('sub_', $action)) {
      $this->subAction = true;
      $action = Misc::removePrefix('sub_', $action);
    }
    $this->d['action'] = $this->action = $action;
    $this->isJson = false;
    $this->isAjax = false;
    $this->hasOutput = true;
    // Для этих типов экшена, вывод основного шаблона запрещается
    // Это значит что вывод будет осуществляться в самих экшенах
    if (($a = $this->parsePrefixedAction($this->action)) !== false) {
      $this->hasOutput = false;
      $this->actionPrefix = $a[0];
      $this->actionBase = $a[1];
      if ($this->actionPrefix == 'json') {
        $this->isAjax = true;
        $this->isJson = true;
      }
      elseif ($this->actionPrefix == 'ajax') {
        $this->isAjax = true;
        header("Content-type: text/plain; charset=".CHARSET);
      }
    }
    if (!R::get('plainText')) // переопределяем только, если PLAINT TEXT режим выключен 
      R::set('plainText', ($this->actionPrefix == 'ajax' or $this->actionPrefix == 'json'));
  }

  protected function parsePrefixedAction($action) {
    if (preg_match('/('.implode('|', $this->noLayoutPrefixes).')_(.+)/', $action, $m)) {
      return [$m[1], $m[2]];
    }
    return false;
  }

  protected function initAction() {
    // Определяем параметр из массива $this->req->r, если разрешено
    if ($this->allowRequestAction) {
      // Экшн из $this->req->r'а имеет приемственность, поэтому, если он определём,
      // переопределяем полюбому
      if ( /*! $this->action and */
        isset($this->req->r['action'])
      ) {
        if (empty($this->req->r['action'])) throw new EmptyException("\$this->req->r['action']");
        $this->setAction($this->req->r['action']);
        //$possibleAction = $this->req->r['action'];
      }
    }
    // Определяем action, получая его из параметров строки запроса
    if (!isset($this->action) and ($paramAction = $this->getParamAction()) !== false) {
      if ($this->getActionObject($paramAction) !== false) {
        $this->setAction($paramAction);
      }
    }
    if (!isset($this->action)) $this->setAction($this->defaultAction);
  }

  protected function getNumParam($n) {
    if (!isset($this->req->params[$n])) throw new Exception('$this->req->params[3] not defined');
    if (!is_numeric($this->req->params[$n])) throw new Exception('$this->req->params[3] is not numeric');
    return $this->req->params[$n];
  }

  protected function getParamAction() {
    if (!isset($this->paramActionN)) return false;
    return isset($this->req->params[$this->paramActionN]) ? $this->req->params[$this->paramActionN] : false;
  }

  /**
   * Должна определять $this->paramActionN
   */
  protected function getParamActionN() {
    return 1;
  }

  // Экшн для формы шаблона
  protected function setPostAction() {
    if ($this->action == 'edit' or $this->action == 'update') $this->d['postAction'] = 'update';
    elseif ($this->action == 'new' or $this->action == 'create') $this->d['postAction'] = 'create';
  }

  protected function setDefaultTpl() {
    if (empty($this->d['tpl'])) $this->d['tpl'] = 'default';
    if (empty($this->d['mainTpl'])) $this->d['mainTpl'] = 'main';
  }

  protected function afterAction() {
  }

  /*
  protected function formAction() {
    $actionMethod = 'action_'.$this->action;
    if (method_exists($this, $actionMethod)) {
      if (($oF = $this->$actionMethod()) === null or !is_a($oF, 'Form')) return false;
      $this->d['form'] = $oF->html();
      if (Misc::hasPrefix('ajax_', $this->action)) $this->ajaxFormAction($oF, $updated);
      else if ($updated) $this->redirect();
      return true;
    }
    if (!Misc::hasPrefix('ajax_', $this->action)) return false;
    $actionMethod = 'action_'.Misc::removePrefix('ajax_', $this->action);
    if (!method_exists($this, $actionMethod)) return false;
    $this->action = Misc::removePrefix('ajax_', $this->action);
    if (($oF = $this->$actionMethod()) === null or !is_a($oF, 'Form')) return false;
    $this->ajaxFormAction($oF);
    return true;
  }
  */

  protected function ajaxFormAction(Form $oF) {
    $oF->disableSubmit = true;
    $this->ajaxOutput = $this->tt->getTpl('common/form', ['form' => $oF->html()]);
  }

  protected $actionMethod;

  protected $actionObjects;

  protected function getActionObject($action) {
    $actionMethod = 'action_'.$action;
    if (method_exists($this, $actionMethod)) return $this;
    if (!empty($this->subControllers)) {
      foreach ($this->subControllers as $subController) {
        if (method_exists($subController, $actionMethod)) {
          return $subController;
        }
      }
    }
    return false;
  }

  /**
   * Вызываются экшены
   */
  protected function action() {
    if ($this->error404) return;
    if (!$this->action) throw new Exception('$this->action not defined');
    $this->checkActionParams($this->action);
    $actionMethod = 'action_'.$this->action;
    $action = $this->getActionObject($this->action);
    if ($action !== false) {
      $this->isAction = true;
      if ($this->isJson) {
        $oF = $this->actionJson($action, $actionMethod);
        if (is_object($oF) and is_a($oF, 'Form')) return $this->jsonFormAction($oF);
      }
      else {
        return $action->$actionMethod();
      }
    }
    else {
      // Меняем флаги на формат обычного экшена с лейаутом
      $this->hasOutput = true;
      $this->isJson = false;
      $this->actionNotFound($actionMethod);
      return false;
    }
  }

  /*
  protected function action_() {
    if ($this->error404) return;
    if (!$this->action) throw new Exception('$this->action not defined');
    $this->checkActionParams($this->action);
    $actionMethod = 'action_'.$this->action;
    if (method_exists($this, $actionMethod)) {
      $this->isAction = true;
      if ($this->isHtml2ajaxAction) {
        $this->actionHtml2ajax($actionMethod);
      } elseif ($this->isJson) {
        $oF = $this->actionJson($actionMethod);
        if (is_object($oF) and is_a($oF, 'Form'))
          $this->jsonFormAction($oF);
      } else {
        $oF = $this->$actionMethod();
        if ($this->isAjax and is_object($oF) and is_a($oF, 'Form'))
          $this->ajaxFormAction($oF);
      }
    } else {
      $this->hasOutput = true;
      $this->isJson = false;
      $this->actionNotFound($this->actionMethod);
    }
  }
  */

  protected function actionJson($oAction, $actionMethod) {
    ini_set('html_errors', false);
    // Если это JSON запрос, выключаем отображение ошибок и 
    // сохраняем последнюю (если она есть) в json-массив
    R::set('showErrors', false); // --- Отключаем показ ошибок
    try {
      $r = $oAction->$actionMethod(); // --- Выполняем экшн
    } catch (Exception $e) { // --- Лобим исключение, записываем его в json
      // Формирование массива error для исключений необходимо делать здесь, потому что
      // exceptionHandler срабатывает только, если исключение не поймано.
      // В конструкторе Exception нельзя делать создание этого массива, т.к.
      // эксепшены могут быть созданы вендорными классами, которые не унеаследованы от
      // Exception
      if (getConstant('IS_DEBUG')) {
        $this->json['error'] = [
          'message' => $e->getMessage(),
          'code'    => $e->getCode(),
          'file'    => $e->getFile(),
          'trace'   => getTraceText($e, false)
        ];
      }
      else {
        $this->json['error'] = $e->getMessage();
      }
      LogWriter::v('errors', 'exception: '.$e->getMessage(), getFullTrace($e));
      return;
    }
    if (($lastError = R::get('lastError')) !== false) $this->json['error'] = $lastError; // --- Добавляем ошибку, если она есть, в json
    return $r;
  }

  /*
  protected function actionHtml2ajax($actionMethod) {
    $this->$actionMethod();
    if (!($ajaxTpl = Arr::getSubValue($this->html2ajaxActions, 'action', $this->action, 'ajaxTpl')))
      throw new Exception(
        'ajaxTpl not defined in array $this->html2ajaxActions for action '.
        $this->action.': '.getPrr($this->html2ajaxActions));
    print $this->tt->getTpl($ajaxTpl, $this->d);
  }
  */

  protected function getActionMethod() {
    return 'action_'.$this->action;
  }

  protected function actionNotFound($actionMethod) {
    throw new Error404('Method <b>'.get_class($this).'::'.$actionMethod.'</b> not found.');
  }

  public $actionReqParams;

  public $actionPathParams;

  /**
   * Enter description here...
   *
   * @param unknown_type $action
   * @param unknown_type $name
   * @param unknown_type text/num/array/array2
   */
  function addActionReqParam($action, $name, $type = 'text') {
    $this->actionReqParams[$action][] = [
      'name' => $name,
      'type' => $type
    ];
  }

  function addActionReqParams($action, $params) {
    $this->actionReqParams[$action] = Arr::append($this->actionReqParams[$action], $params);
  }

  function addActionPathParam($action, $n) {
    $this->actionPathParams[$action][] = $n;
  }

  /**
   * Проверяем наличие необходимых параметров для выполнения экшена
   *
   * @todo  Экшены проверяются только для основного контроллера..
   *        Саб-конттроллер же пущен по боку
   */
  private function checkActionParams($action) {
    if (isset($this->actionReqParams[$action])) {
      foreach ($this->actionReqParams[$action] as $param) {
        // Проверяем наличие нужного параметра в массиве $this->req->r
        if (!isset($this->req->r[$param['name']])) throw new Exception("\$this->req->r[{$param['name']}] required");
        if ($param['type'] == 'num') if (!is_numeric($this->req->r[$param['name']])) throw new Exception("\$this->req->r[{$param['name']}] in not numeric.<br />"."<b>Controller:</b> ".get_class($this).", <b>Action:</b> $action, "."<b>Param:</b> ".$param['name'].'. $this->req->r: '.getPrr($this->req->r));
        elseif ($param['type'] == 'array') if (!is_array($this->req->r[$param['name']])) throw new Exception("\$this->req->r[{$param['name']}] is not an array");
      }
    }
    if (isset($this->actionPathParams[$action])) {
      foreach ($this->actionPathParams[$action] as $param) {
        // Проверяем наличие нужного параметра в массиве параметров запроса
        if (!$this->req->params[$param]) {
          throw new Exception('Path param "'.str_repeat('/param', $param).'/{x}" required');
        }
      }
    }
  }

  // --------------------------------------------------------------------


  function action_default() {
  }

  //  function actionNotExists() {    Err::warning("Method '".get_class($this)."->action_{$this->action}' not exists");  }


  /**
   * @manual
   * Перенаправляет страницу, отключая при это вывод
   *
   * @param   string    null - редирект на страницу без QUERY_STRING
   *                    'referer' - редирект на реферер этой страницы
   *                    все остальные значение - ссылка для редиректа
   */
  function redirect($path = null) {
    if (!$this->allowRedirect) return;
    $this->hasOutput = false;
    if ($path == 'referer') {
      if (isset($this->req->r['referer'])) {
        $path = $this->req->r['referer'];
      }
      else {
        $path = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
      }
    }
    elseif ($path == 'fullpath') {
      redirect($this->tt->getPath().($_SERVER['QUERY_STRING'] ? '?'.$_SERVER['QUERY_STRING'] : ''));
      return;
    }
    if ($path) {
      if ($path == $this->tt->getPath() and !count($_GET) and !count($_POST)) print('Can not redirect to itself');
      else {
        redirect($path);
      }
    }
    elseif (count($_GET) or count($_POST)) {
      redirect($this->tt->getPath());
    }
    else
      print('Can not redirect to itself');
  }

  protected function getName() {
    return lcfirst(str_replace('Ctrl', '', get_class($this)));
  }

  protected function setPageTitle($title) {
    $this->d['pageTitle'] = $title;
    $this->d['pageHeadTitle'] = $title.'. '.SITE_TITLE;
  }

  protected function getActionMethods() {
    $methods = [];
    foreach (get_class_methods(get_class($this)) as $method) {
      if (preg_match('/action_(.*)/', $method)) {
        $methods[] = $method;
      }
    }
    return $methods;
  }

  protected function getActions() {
    $methods = [];
    foreach (get_class_methods(get_class($this)) as $method) {
      if (preg_match('/action_(.*)/', $method, $m)) {
        $methods[] = $m[1];
      }
    }
    return $methods;
  }

  ///////////// Actions ////////////

  /*

  function action_json_userSearch() {
    if (! $mask = $this->req->r['mask'] or ! $name = $this->req->r['name'])
      return;
    $this->json['html'] = $this->tt->getTpl('common/searchResults',
      array(
        'name' => $name,
        'items' => UsersCore::searchUser($mask)
      ));
  }

  function action_json_pageSearch() {
    if (! $mask = $this->req->r['mask'])
      return;
    $this->json['html'] = $this->tt->getTpl('common/searchResults',
      array(
        'name' => 'pageId',
        'items' => Pages::searchPage($mask)
      ));
  }

  function action_json_userAutocomplete() {
    $mask = $this->req->rq('mask');
    if ($mask[0] == '_') {
      $this->json = array(
        ALL_USERS_ID => 'Все пользователи',
        REGISTERED_USERS_ID => 'Зарегистированые пользователи'
      );
      return;
    }
    $this->json = db()->selectCol("
      SELECT id AS ARRAY_KEY, login FROM users WHERE
      login LIKE ? ORDER BY id LIMIT 10",
      $mask.'%');
  }

  function action_json_pageItemsAutocomplete() {
    $this->json = DbModelPages::searchPage($this->req->r['mask'], "pages.controller='items'");
  }

  function action_json_pageAlbumsAutocomplete() {
    $this->json = DbModelPages::searchPage($this->req->r['mask'], "pages.controller='albums'");
  }

  function action_json_pageAutocomplete() {
    $this->json = DbModelPages::searchPage($this->req->r['mask']);
  }

  function action_json_folderAutocomplete() {
    $this->json = DbModelPages::searchFolder($this->req->r['mask']);
  }

  */

  /**
   * Очищает экшн от layout-префиксов
   *
   * @param   string  action
   * @return  string  очищенный action
   */
  protected function clearActionPrefixes($action) {
    foreach ($this->noLayoutPrefixes as $v) $noLayoutPrefixes[] = $v.'_';
    $action = str_replace($noLayoutPrefixes, '', $action);
    if (isset($action[0]) and $action[0] == '_') $action = substr($action, 1, strlen($action));
    return $action;
  }

  protected function error($msg) {
    $this->error404 = true;
    $this->hasOutput = false;
    Err::warning($msg);
  }

  protected function getPjLastStepKey(PartialJob $oPJ) {
    return $oPJ->getId().'LastStep';
  }

  protected function getPjLastStep(PartialJob $oPJ) {
    return Settings::get($this->getPjLastStepKey($oPJ));
  }

  protected function actionJsonPJ(PartialJob $oPJ) {
    $settingsKey = $this->getPjLastStepKey($oPJ);
    $step = $this->req->rq('step');
    $this->json['step'] = $step;
    if (!$step and ($_step = Settings::get($settingsKey))) {
      // Если 0-й шаг, начинаем с последнего сохраненного шага
      $step = $_step + 1;
    }
    $this->json = $oPJ->stepData($step);
    try {
      $oPJ->makeStep($step);
    } catch (Exception $e) {
      if ($e->getCode() == 1040) {
        // Шаг больше максимально возможного.
        // Значит по какой-то причине предыдущий шаг не был успешно завершен
        // Завершаем
        $oPJ->complete();
        return;
      }
      // 'continueErrorCodes' - коды ошибок, для которых включена ф-я "продолжить"
      // Если эти коды существуют
      // Проверяем выброшеное исключение на наличие в них
      elseif (!empty($this->req->r['continueErrorCodes']) and
        in_array($e->getCode(), $this->req->r['continueErrorCodes'])
      ) {
        // И, если оно там есть, переходим к следующему шагу 
        Settings::set($settingsKey, $step);
      }
      // И выбрасываем ошибку, она нам ещё понадобиться в формировании ответного json-массива
      throw $e;
    }
    Settings::set($settingsKey, $step);
  }

  protected function cleanupPJStep(PartialJob $oPJ) {
    Settings::delete($this->getPjLastStepKey($oPJ));
  }

  function error404($title = 'Страница не найдена', $text = '') {
    header('HTTP/1.0 404 Not Found');
    throw new Error404($title);
    if (!$this->hasOutput) {
      if ($this->isJson) $this->json['error'] = $title;
      else print "<h1>$title</h1>$text";
      return;
    }
    $this->setDefaultTpl();
    $this->isDefaultAction = false;
    // Если в результате экшенов получилось так, что была определена 404 страница,
    // это значит, что экшен не прошел успешно и действий никаких после него 
    // вызывать не надо
    $this->afterActionDisabled = true;
    $this->error404 = [
      'backtrace' => getBacktrace(false), $title
    ];
    $this->d['tpl'] = 'errors/404';
    $this->d['text'] = $text;
  }

  protected function jsonFormAction(Form $form) {
    $form->disableSubmit = true;
    $form->defaultData = $this->req->r;
    $this->json['form'] = $this->tt->getTpl('common/form', ['form' => $form->html()]);
    if (!empty($form->options['title'])) $this->json['title'] = $form->options['title'];
    $this->json['submitTitle'] = $form->options['submitTitle'];
    return $form;
  }

  protected function jsonFormActionUpdate(Form $form) {
    if ($form->update()) return true;
    return $form;
  }

  protected function processForm(Form $oF) {
    $this->d['tpl'] = 'common/form';
    if ($oF->update()) return true;
    $this->d['form'] = $oF->html();
    return false;
  }

  protected function rss(array $header, array $items) {
    header('Content-type: text/xml; charset='.CHARSET);
    if (!isset($header['link'])) $header['link'] = Tt()->getPath();
    $this->hasOutput = false;
    print
      (new Rss('default'))->getXml([
        'header' => $header,
        'items'  => $items
      ]);
  }

}
