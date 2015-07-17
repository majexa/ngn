<?php

/**
 * Осуществляет фильтрацию, добавление, изменение и редактирование данных
 * Фильтрация осуществляется классом формы и её полями
 * Преобразует данные из формы в формат для сохранения
 * Преобразует данные в формат формы
 *
 */
abstract class DataManagerAbstract extends Options2 {
  use CallOnce;

  /**
   * @api
   * Возвращает одну запись по ID
   *
   * @param $id
   * @return array
   */
  abstract function getItem($id);

  /**
   * Cоздаёт одну запись и возвращать её уникальный ID
   *
   * @return mixed
   */
  abstract protected function _create();

  /**
   * Изменяет текущую запись
   *
   * @return mixed
   */
  abstract protected function _update();

  /**
   * Удаляет текущую запись
   *
   * @return mixed
   */
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
    return [
      'strict'         => true,
      'ignoreExisting' => false
    ];
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
   * @api
   * Создает новую запись
   *
   * @param  array  Данные для _Формы_ по умолчанию
   * @return bool|integer
   */
  function requestCreate(array $default = []) {
    $this->form->fromRequest = true;
    $this->form->create = true;
    $this->initTinyInitJs();
    $this->setFormElementsData($default);
    if ($this->form->isSubmittedAndValid()) {
      return $this->makeCreate();
    }
    return false;
  }

  function create(array $data, $throwFormErrors = true) {
    if ($this->options['ignoreExisting'] and $this->form->fields->exists($data['name'])) return false;
    $this->form->fromRequest = false;
    $this->form->create = true;
    $this->setFormElementsData($data);
    if (!$this->form->validate()) {
      if ($throwFormErrors) throw new AlreadyExistsException(get_class($this).': '.$this->form->lastError.(IS_DEBUG ? '. data: '.getPrr($data) : ''));
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
   * @api
   * - Получает значения для _Формы_ из записи: `DataManagerAbstract::getItem($id)`;<br>
   * - Преобразует значения в формат, необходимый для _Формы_;<br>
   * - Инициализирует _Форму_ и _Элементамы полей_ с преобразованными данными;<br>
   * - Получает данные из _Формы_;<br>
   * - Преобразует их в формат источника;<br>
   * - Вызывает специфичные типам полей экшены;<br>
   * - Добавляет системные значения;<br>
   * - Если произошел сабмит _Формы_
   * - Выполняет апдейт записи;<br>
   * - Вызывает специфичные типам полей пост-экшены.
   *
   * @param $id
   * @return bool
   * @throws Exception
   */
  function requestUpdate($id) {
    if (!is_numeric($id)) throw new Exception('$id not numeric: '.$id);
    $this->defaultData = Misc::checkEmpty($this->getItem($id), "item ID=$id does not exists");
    $this->fieldTypeAction('source2formFormat', $this->defaultData);
    $this->source2formFormat();
    $this->form->fromRequest = true;
    $this->initTinyInitJs($id);
    $this->setFormElementsData($this->defaultData, true);
    $this->afterFormElementsInit();
    if ($this->form->isSubmittedAndValid()) {
      return $this->makeUpdate($id);
    }
    return false;
  }

  /**
   * @api
   * Возвращает данные записи в формате _Формы_
   *
   * @param $id
   * @return array
   */
  function formData($id) {
    $data = $this->getItem($id);
    $this->fieldTypeAction('source2formFormat', $data);
    return $data;
  }

  protected function beforeFormElementsInit() {
  }

  /**
   * @api
   * Вызывается после инициализации _Элементов полей_, но до рендеринга _Формы_.
   * Таким образом этот метод можно использовать для переопределения опций _Элементов полей_
   * прямо из _Менеджера данных_.
   */
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

  /**
   * @param array $data
   * @param bool $valueFormatted Данные используют формат POST запроса
   * @param bool $filterByData
   */
  protected function setFormElementsData(array $data, $valueFormatted = false, $filterByData = false) {
    $this->beforeFormElementsInit();
    $this->form->valueFormated = $valueFormatted;
    $this->form->setElementsData($data, true, $filterByData);
  }

  protected function formatFormPostData() {
    $this->fieldTypeAction('post2formFormat', $this->form->req->p);
  }

  function update($id, array $data, $throwFormErrors = true, $filterByData = false) {
    // $filterByData используется для случаев, когда не нужно валидировать данные из формы
    // т.е. данные могут прийти частично (не все, что есть в форме) и частично же обработаны
    $this->form->fromRequest = false;
    $this->setFormElementsData($data, false, $filterByData);
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
   * @return bool|integer Item ID или false, если валидация данных не прошла успешно
   * @throws Exception
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
    $this->_afterCreate();
    return $this->id;
  }

  protected function _afterCreate() {
    Misc::checkEmpty($this->id, '$this->id');
    $this->elementTypeAction('afterCreate');
    $this->elementTypeAction('afterCreateUpdate');
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
      //die2($this->data);
      $this->_update();
    } catch (NgnValidError $e) {
      $this->validError = $e;
      $this->form->globalError($e->getMessage());
      return false;
    }
    $this->_afterUpdate();
    return true;
  }

  protected function _afterUpdate() {
    $this->elementTypeAction('afterUpdate');
    $this->elementTypeAction('afterCreateUpdate');
    $this->afterUpdate();
    $this->afterCreateUpdate();
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

  /**
   * @api
   * Действия перед апдейтов (используйте $this->data)
   */
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
   * @param  integer /null  $id
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
    foreach ($this->form->getElements() as $k => $el) {
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

  /**
   * Инициализирует ID для аплоада файлов
   *
   * @return $this
   */
  protected function initTempId() {
    $tempId = session_id();
    if (!$tempId) {
      $tempId = isset($_POST['tempId']) ? $_POST['tempId'] : Misc::randString(8);
    }
    $this->form->addNoValueHiddenField([
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
   * @param string $imageRoot Путь до картинки от корня
   * @throws Exception
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
    if (!isset($this->image)) $this->image = new Image;
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
    if ($this->form->fields[$fieldName]['required']) throw new Exception("Field '$fieldName' is required");
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
   * Добавляет в объект формы опции для инициализации загрузки файлов
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