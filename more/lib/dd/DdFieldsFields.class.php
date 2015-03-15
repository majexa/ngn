<?php

/**
 * Поля формы создания dd-поля
 */
class DdFieldsFields extends Fields {

  function __construct() {
    $this->addFields([
      [
        'type' => 'col'
      ],
      [
        'name'     => 'title',
        'title'    => 'Название',
        'required' => true
      ],
      [
        'name'     => 'name',
        'title'    => 'Имя',
        'type'     => 'ddFieldName',
        'required' => true
      ],
      [
        'name'  => 'default',
        'title' => 'Значение по умолчанию'
      ],
      [
        'name'  => 'help',
        'title' => 'Описание',
        'type'  => 'textarea',
        'help'  => 'Будет выводится справа или под полем'
      ],
      [
        'name'  => 'maxlength',
        'title' => 'Максимальная длина',
        'help'  => 'Оставьте пустым, если максимальная длина не нужна'
      ],
      ['type' => 'col'],
      [
        'name'     => 'type',
        'title'    => 'Тип',
        'type'     => 'ddFieldType',
        'required' => true
      ],
      ['type' => 'col'],
      [
        'name'  => 'required',
        'title' => 'обязательно для заполнения',
        'type'  => 'bool'
      ],
      [
        'name'  => 'notList',
        'title' => 'не выводить поле в списках',
        'type'  => 'bool',
      ],
      [
        'name'  => 'defaultDisallow',
        'title' => 'не доступно по умолчанию',
        'type'  => 'bool',
        'help'  => 'Используется в том случае, если поле отображается при редактировании только при необходимых привилегиях, но при этом по-умолчанию выводится в списках'
      ],
      [
        'name'  => 'system',
        'title' => 'системное',
        'type'  => 'bool',
        'help'  => 'Используется в том случае, если изменение пользователем этого поля не предполагается'
      ],
      [
        'name'  => 'virtual',
        'title' => 'виртуальное',
        'type'  => 'bool',
        'help'  => ''
      ]
    ]);
    if (Config::getVarVar('dd/common', 'enableFilters')) {
      $this->addField([
        'name'    => 'filterable',
        'title'   => 'фильтруемое',
        'type'    => 'bool',
        'default' => true
      ]);
    }
  }

}