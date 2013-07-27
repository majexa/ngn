<?php

class DdGridFilters {

  public $form;

  static function getAll($strName, array $names = null) {
    $filters = [];
    /*
    $filters[] = [
      'title'          => 'ID',
      'name'           => 'id',
      'type'           => 'num',
      'pathFilterType' => 'v'
    ];
    */
    $filters[] = [
      'title' => 'Дата создания',
      'name'  => 'dateCreate',
      'pathFilterType' => 'd',
      'date'  => true
    ];
    foreach ((new DdFields($strName, [
      'getDisallowed' => true,
      'getSystem'     => true
    ]))->getFields() as $v) {
      if (DdTags::isTagType($v['type'])) {
        $filters[] = [
          'title'          => $v['title'],
          'tree'           => DdTags::isTagTreeType($v['type']),
          'name'           => $v['name'],
          'options'        => Html::defaultOption() + Arr::get(DdTags::get($strName, $v['name'])->getData(), 'title', 'id'),
          'pathFilterType' => 't2'
        ];
      }
      elseif (DdFieldCore::isBoolType($v['type'])) {
        $filters[] = [
          'title'          => $v['title'],
          'name'           => $v['name'],
          'options'        => Html::defaultOption() + [
            1 => 'Да',
            0 => 'Нет'
          ],
          'pathFilterType' => 'v'
        ];
      }
      elseif (FieldCore::hasAncestor($v['type'], 'user')) {
        $filters[] = [
          'title'          => $v['title'],
          'name'           => $v['name'],
          'options'        => UsersCore::getUserOptions(),
          'pathFilterType' => 'v'
        ];
      }
    }
    if ($names) $filters = array_filter($filters, function ($v) use ($names) {
      return in_array($v['name'], $names);
    });
    return $filters;
  }

  /**
   * @param array example: [
   *   [
   *     [title] => Выберите модель авто
   *     [tree] => true
   *     [name] => model
   *     ... supported all options from corresponding FieldE* class
   *   ]
   * ]
   *
   * @param $strName
   */
  function __construct(array $filters, $strName) {
    $fields = [];
    foreach ($filters as $v) {
      if (empty($v['type'])) {
        if (!empty($v['date'])) {
          $v['type'] = 'dateRange';
        }
        else {
          if (!empty($v['tree'])) {
            $v['type'] = 'ddTagsTreeMultiselect';
          }
          else {
            if (!isset($v['type'])) $v['type'] = 'select';
          }
        }
      }
      $fields[] = $v;
    }
    foreach ($fields as &$v) {
      if (FieldCore::hasAncestor($v['type'], 'select')) $v['cssClass'] = 'allowReload';
      $v['dataParams']['pathFilterType'] = $v['pathFilterType'];
      $v['dataParams']['name'] = isset($v['filterName']) ? $v['filterName'] : $v['name'];
    }
    $this->form = new DdForm($fields, $strName);
    $this->form->disableSubmit = true;
  }

}