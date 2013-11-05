<?php

/**
 * - Преобразует данные из формы в формат для сохранения
 * - Преобразует данные в формат для формы
 */
abstract class DataManagerAbstract extends Options2 {

  /**
   * @param $id
   * @return array
   */
  abstract public function getItem($id);

  abstract protected function _create();

  abstract protected function _update();

  abstract protected function _delete();

  /**
   * @var Form
   */
  public $form;

  /**
   * HTML сгенерированой формы
   *
   * @var string
   */
  public $formHtml;

  public $imageSizes;

  public $videoSizes;

  /**
   * Тип ресайза превьюшек (resize/resample)
   *
   * @var string
   */
  public $smResizeType = 'resize';

  /**
   * Тип ресайза скринов (resize/resample)
   *
   * @var string
   */
  public $mdResizeType = 'resample';

  public $defaultActive = 1;

  protected $beforeUpdateData;

  static $defaultImageSizes = [
    'smW' => 100,
    'smH' => 60,
    'mdW' => 400,
    'mdH' => 300
  ];

  /**
   * @var Request
   */
  //protected $req;

  function __construct(Form $form, array $options = []) {
    if (!is_object($form)) throw new Exception('$form is not object');
    $this->form = $form;
    $this->imageSizes = self::$defaultImageSizes;
    parent::__construct($options);
    $this->initTempId();
    $this->initTempUpload();
    $this->setAuthorId(isset($this->options['authorId']) ? $this->options['authorId'] : Auth::get('id'));
    $this->init();
  }

  protected function defineOptions() {
    return ['strict' => true];
  }

  protected function init() {
  }

  /**
   * Данные записи для сохранения
   *
   * @var array
   */
  public $data;

  /**
   * Текущий ID при работе с create, update
   *
   * @var integer
   */
  public $id = false;

  function getName() {
    return get_class($this);
  }

  /**
   * Используется в вызове из контроллера
   * Обрабатывает пользовательские данные, преобразовывая их с помощью класса формы
   *
   * @param  array  Данные по умолчанию
   * @return bool|integer
   */
  function requestCreate(array $default = []) {
    $this->form->create = true;
    $this->initTinyInitJs();
    $this->setFormElementsData($default);
    if ($this->form->isSubmittedAndValid()) {
      return $this->makeCreate();
    }
    return false;
  }

  function create(array $data, $throwFormErrors = true) {
    $this->form->fromRequest = false;
    $this->form->create = true;
    $this->setFormElementsData($data);
    if (!$this->form->validate()) {
      if ($throwFormErrors) throw new Exception((IS_DEBUG ? get_class($this).': ' : '').$this->form->lastError.(IS_DEBUG ? '. data: '.getPrr($data) : ''));
      else return false;
    }
    $r = $this->makeCreate();
    if ($r === false and $throwFormErrors) {
      if (!isset($this->form->lastError)) throw new EmptyException('$this->form->lastError');
      throw new Exception($this->form->lastError.'. data: '.getPrr($data));
    }
    return $r;
  }

  function createAnyway(array $data) {
    $r = false;
    try {
      $r = $this->create($data);
    } catch (Exception $e) {
    }
    return $r;
  }

  public $defaultData;

  /**
   * Производит обработку действия с формы перед созданием, создаёт форму, обрабатывает значения полученные в результате её создания
   * и изменяет значения записи. Последнее делает только в случае если параметр $_data пределен.
   *
   * 1) Получает данные, поступившие для апдейта записи либо
   *    из текщих значений самой записи.
   * 2) Преобразует необходимые значения в вид, необходимый для класса формы
   * 3) Выполняет создание полей формы для каждого из значений записи. Каждое поле
   *    соответственно возвращает преобразованное значение. Преобразования значений
   *    происходит в соответствующих обработчиках формы (функции формата f_fieldName в
   *    класса формы).
   * 4) Выполняет функцию апдейта записи.
   *
   * @param   integer ID аписи
   * @param   array   Массив с данными для апдейта
   * @return  bool
   */
  function requestUpdate($id) {
    if (!is_numeric($id)) throw new Exception('$id not numeric: '.$id);
    $this->defaultData = $this->getItem($id);
    $this->fieldTypeAction('source2formFormat', $this->defaultData);
    $this->source2formFormat();
    $this->initTinyInitJs($id);
    $this->setFormElementsData($this->defaultData);
    $this->afterFormElementsInit();
    if ($this->form->isSubmittedAndValid()) {
      return $this->makeUpdate($id);
    }
    return false;
  }

  protected function beforeFormElementsInit() {
  }

  protected function afterFormElementsInit() {
  }

  /**
   * Должна работать с массивом $this->defaultData
   */
  protected function source2formFormat() {
  }

  /**
   * Должна работать с массивом $this->data
   */
  protected function form2sourceFormat() {
  }

  protected function setFormElementsData(array $data) {
    $this->beforeFormElementsInit();
    //$this->form->defaultData = $data;
    $this->form->setElementsData($data);
  }

  function update($id, array $data, $throwFormErrors = true) {
    $this->form->fromRequest = false;
    $this->setFormElementsData($data);
    if ($this->form->hasErrors) {
      if ($throwFormErrors) throw new Exception($this->form->lastError.'. data: '.getPrr($data));
      else return false;
    }
    $r = $this->makeUpdate($id);
    if ($r === false and $throwFormErrors) {
      if (!isset($this->form->lastError)) throw new EmptyException('$this->form->lastError');
      throw new Exception($this->form->lastError.'. data: '.getPrr($data));
    }
    return $r;
  }

  /**
   * @var NgnValidError
   */
  public $validError;

  public $beforeCreateAction = false;

  /**
   * Создает запись валидируя входные данные с помощью класса формы
   *
   * @param   array     Пример:
   *                    array(
   *                      'title' => 'The title',
   *                      'file' => array(
   *                        'tmp_name' => '873yq2f.tmp'
   *                      )
   *                    )
   * @return  mixed   Item ID или false, если валидация данных не прошла успешно
   */
  protected function makeCreate() {
    try {
      // Данные необходимо обязательно получать из формы, т.к. обработка их происходит внутри
      // элементов полей. Форма будет возвращать единственно правильный вариант данных
      $this->beforeUpdateData = $this->data = $this->form->getData();
      if ($this->beforeCreateAction) call_user_func($this->beforeCreateAction, $this);
      $this->addCreateData();
      $this->elementTypeAction('beforeCreateUpdate');
      $this->fieldTypeAction('form2sourceFormat', $this->data);
      $this->form2sourceFormat();
      $this->replaceData();
      $this->beforeCreate();
      $id = $this->_create();
    } catch (NgnValidError $e) {
      $this->validError = $e;
      if (get_class($e) == 'FormError') $this->form->getElement($e->elementName)->error($e->getMessage());
      else
        $this->form->globalError($e->getMessage());
      return false;
    }
    if (empty($id)) throw new Exception('id is empty. check what '.get_class($this).'::_create returns. create data: '.getPrr($this->data));
    $this->id = $id;
    $this->_afterCreate($id);
    return $id;
  }

  protected function _afterCreate($id) {
    $this->elementTypeAction('afterCreateUpdate', $id);
    $this->elementTypeAction('afterCreate', $id);
    $this->afterCreate();
    $this->afterCreateUpdate();
  }

  function setDataValue($flatName, $value) {
    BracketName::setValue($this->data, $flatName, $value);
  }

  protected function beforeCreate() {
  }

  protected function afterCreate() {
  }

  public $disableFUdelete = false;

  protected function afterCreateUpdate() {
  }

  protected function makeUpdate($id) {
    $this->id = $id;
    $this->beforeUpdateData = $this->getItemNonFormat($this->id);
    try {
      $this->data = $this->form->getData();
      $this->fieldTypeAction('form2sourceFormat', $this->data);
      $this->form2sourceFormat();
      $this->replaceData();
      $this->beforeUpdate();
      $this->elementTypeAction('beforeCreateUpdate');
      $this->_update();
    } catch (NgnValidError $e) {
      $this->validError = $e;
      $this->form->globalError($e->getMessage());
      return false;
    }
    $this->elementTypeAction('afterCreateUpdate');
    $this->elementTypeAction('afterUpdate');
    $this->afterUpdate();
    $this->afterCreateUpdate();
    return true;
  }

  abstract function _updateField($id, $fieldName, $value);

  function updateField($id, $fieldName, $value) {
    $this->form->fields->fields = Arr::filterByKeys($this->form->fields->fields, $fieldName);
    $this->update($id, [$fieldName => $value]);
  }

  function updateData($id, $data) {
    $this->form->fields->fields = Arr::filterByKeys($this->form->fields->fields, array_keys($data));
    $this->update($id, $data);
  }

  protected function beforeUpdate() {
  }

  protected function afterUpdate() {
  }

  protected function getItemNonFormat($id) {
    return $this->getItem($id);
  }

  function delete($id) {
    $this->id = $id;
    $this->data = $this->getItem($id);
    if (empty($this->data)) {
      if ($this->options['strict']) throw new Exception('No item by id='.$id);
      return false;
    }
    try {
      Dir::remove($this->getAttachePath());
      $this->beforeDelete();
      $this->form->setElementsData();
      $this->elementTypeAction('beforeDelete');
    } catch (Exception $e) {
      $this->_delete($id);
      throw $e;
    }
    $this->_delete($id);
  }

  public $authorId = null;

  function setAuthorId($id) {
    $this->authorId = $id;
    return $this;
  }

  function unsetAuthorId() {
    $this->authorId = null;
  }

  /**
   * Добавляет или заменяет значения в массиве с данными из формы
   *
   * @param   array   Данные из формы
   */
  protected function replaceData() {
  }

  protected function beforeDelete() {
  }

  /**
   * @param  string
   * @return Dmfa
   */
  protected function getDmfa($fieldType) {
    if (($class = ClassCore::getFirstAncestor($fieldType, 'FieldE', 'Dmfa')) === false) {
      return false;
    }
    return O::get($class, $this);
  }

  /**
   * Вызывает статический метод класса FieldE[fieldType] и заменяет с помощью него значение
   * данных
   *
   * @param  string $method
   * @param  array $data
   * @param  integer/null  $id
   */
  protected function fieldTypeAction($method, array &$data) {
    foreach (array_keys($data) as $k) {
      if (($fieldType = $this->form->fields->getType($k)) === false) continue;
      if (($o = $this->getDmfa($fieldType)) === false) continue;
      if (!method_exists($o, $method)) continue;
      if (($r = $o->$method($data[$k], $k, $data)) !== null) $data[$k] = $r;
    }
  }

  protected $elementTypeActionProcessed = [];

  protected function elementTypeAction($method) {
    $this->fieldTypeAction($method, $this->data);
    $method = 'el'.ucfirst($method);
    foreach ($this->form->getElements() as $el) {
      $this->elementTypeActionProcessed[] = $el['name'];
      if (($o = $this->getDmfa($this->form->fields->getType($el['name']))) === false) continue;
      if (!method_exists($o, $method)) continue;
      $o->$method($el);
    }
  }

  /**
   * Типографировать ли HTML для типа 'wisiwig'
   *
   * @var bool
   */
  public $typo = true;

  public $tempId;

  protected function initTempId() {
    $tempId = session_id();
    if (!$tempId) {
      $tempId = isset($_POST['tempId']) ? $_POST['tempId'] : Misc::randString(8);
    }
    $this->form->addHiddenField([
      'name'  => 'tempId',
      'value' => $tempId
    ]);
    $this->tempId = $tempId;
    return $this;
  }

  /**
   * Используется полями типа "wisiwig"
   */
  function moveTempFiles(&$html, $itemId, $fieldName) {
    if (!isset($this->tempId)) throw new Exception('$this->tempId must be defined. Use DataManagerAbstract::initTempId() after DbItemsManager initialization');
    TinyAttachManager::moveTempFiles($html, $this->getTinyAttachTempId($fieldName), $this->getTinyAttachItemId($itemId, $fieldName));
  }

  function cleanupImages(&$html, $itemId, $fieldName) {
    TinyAttachManager::cleanupImages($html, $this->getTinyAttachItemId($itemId, $fieldName));
  }

  function getTinyAttachTempId($fieldName) {
    Misc::checkEmpty($this->tempId);
    return 'temp-'.$this->tempId.'-'.$fieldName;
  }

  function getTinyAttachItemId($itemId, $fieldName) {
    return 'common-'.$itemId.'-'.$fieldName;
  }

  //////////////// STATIC ID ///////////////

  public $isStatic;

  public $static_id;

  function setStaticId($id) {
    if (empty($id)) throw new Exception('$id is empty');
    $this->static_id = $id;
    $this->isStatic = true;
    return $this;
  }

  public $createData = [];

  protected function addCreateData() {
    if ($this->createData) $this->data = array_merge($this->data, $this->createData);
    // Добавляет static_id в данные создаваемой записи
    if ($this->isStatic) {
      if (!$this->static_id) throw new Exception('$this->static_id not defined');
      $this->data['static_id'] = $this->static_id;
    }
  }

  // ------- thumbs ------------

  /**
   * Создаёт превьюшки изображения
   *
   * @param   string    Путь до картинки от корня
   */
  function makeThumbs($imageRoot) {
    $this->makeSmallThumbs($imageRoot);
    $this->makeMiddleThumbs($imageRoot);
  }

  /**
   * @var Image
   */
  protected $image;

  function makeSmallThumbs($imageRoot) {
    if (!file_exists($imageRoot)) throw new Exception("File '$imageRoot' does not exists");
    if (!isset($this->image)) $this->image = new Image();
    if ($this->smResizeType == 'resample') {
      $this->image->resampleAndSave($imageRoot, Misc::getFilePrefexedPath2($imageRoot, 'sm_'), $this->imageSizes['smW'], $this->imageSizes['smH']);
    }
    else {
      $this->image->resizeAndSave($imageRoot, Misc::getFilePrefexedPath2($imageRoot, 'sm_'), $this->imageSizes['smW'], $this->imageSizes['smH']);
    }
  }

  function makeMiddleThumbs($imageRoot) {
    File::checkExists($imageRoot);
    if (!isset($this->image)) $this->image = new Image();
    if ($this->mdResizeType == 'resize') {
      $this->image->resizeAndSave($imageRoot, Misc::getFilePrefexedPath2($imageRoot, 'md_'), $this->imageSizes['mdW'], $this->imageSizes['mdH']);
    }
    else {
      $this->image->resampleAndSave($imageRoot, Misc::getFilePrefexedPath2($imageRoot, 'md_'), $this->imageSizes['mdW'], $this->imageSizes['mdH']);
    }
  }

  function getAttacheFilenameByEl(FieldEFile $el) {
    if (empty($el['postValue'])) throw new Exception('strange =(');
    return $this->getAttacheFilename($el['name']);
  }

  function getAttacheFilename($fieldName) {
    return Misc::name2id($fieldName);
  }

  function deleteFile($id, $fieldName) {
    $this->_updateField($id, $fieldName, '');
    $this->id = $id;
    if (($dmfa = $this->getDmfa($this->form->fields->fields[$fieldName]['type'])) !== false) {
      if (method_exists($dmfa, 'deleteAttaches')) $dmfa->deleteAttaches($fieldName);
    }
  }

  /**
   * Только форма созданная из ДатаМенеджера может именть wisiwig-элементы с аттач-кнопками
   */
  protected function initTinyInitJs($itemId = null) {
    if (!$this->form->hasAttachebleWisiwig()) return;
    $opt = [
      'parent'  => "$('{$this->form->id()}')",
      'attachs' => 'true'
    ];
    if (!$this->form->create) {
      $opt += ['attachIdTpl' => "'".$this->getTinyAttachItemId($itemId, '{fn}')."'"];
    }
    else {
      $opt += ['attachIdTpl' => "'".$this->getTinyAttachTempId('{fn}')."'"];
    }
    $opt = Arr::jsObj($opt, false);
    $this->form->defaultElements[] = [
      'type' => 'js',
      'js'   => '
new Ngn.TinyInit($merge(
  {settings: new Ngn.TinySettings().getSettings()},
  '.$opt.'
));'
    ];
    $this->form->tinyInitialized = true;
  }

  /**
   * @var UploadTemp
   */
  public $ut;

  /**
   * Добавляет в объект формы опции для инициализации fancyUpload
   */
  protected function initTempUpload() {
    $this->ut = UploadTemp::extendFormOptions($this->form);
  }

  /**
   * Должен возвращаеть путь до каталога с изображением относительно UPLOAD_PATH.
   * При вызове этого метода $this->id определен.
   */
  function getAttacheFolder() {
    throw new NoMethodException('getAttacheFolder');
  }

  function getAttachePath() {
    return UPLOAD_PATH.'/'.$this->getAttacheFolder();
  }

  static function extendImageData(array $data, array $fields) {
    foreach ($fields as $v) {
      if (empty($data[$v['name']])) continue;
      if (FieldCore::hasAncestor($v['type'], 'image')) {
        $data[$v['name']] = '/'.UPLOAD_DIR.'/'.$data[$v['name']];
        if (FieldCore::hasAncestor($v['type'], 'imagePreview')) {
          $data['sm_'.$v['name']] = Misc::getFilePrefexedPath($data[$v['name']], 'sm_');
          $data['md_'.$v['name']] = Misc::getFilePrefexedPath($data[$v['name']], 'md_');
        }
      }
    }
    return $data;
  }

}