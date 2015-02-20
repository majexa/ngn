<?php

class DdItemsAddForm extends DdForm {

  function __construct(array $imageUrls) {
    $protoFields = [
      ['type' => 'col'],
      [
        'type'     => 'staticText',
        'rowClass' => 'image',
        'text'     => '<a href="{url}" class="lightbox thumb"><img src="{smUrl}"></a>'
      ],
      [
        'title'     => 'Название',
        'type'      => 'text',
        'required' => true,
        'name'      => 'title'
      ],
//      [
//        'title'     => 'Описание',
//        'type'      => 'textarea',
//        'useTypeJs' => false,
//        'name'      => 'descr'
//      ],
      [
        'title'    => 'Категория',
        'type'     => 'ddTagsTreeMultiselectDialogable',
        'required' => true,
        'name'     => 'cat'
      ],
    ];
    $fields = [];
    $fields[] = [
      'type' => 'staticText',
      'name' => 'titleHeader',
      'text' => '<h2>Вы загрузили эти фото, но ещё не добавили</h2>'
    ];
    foreach ($imageUrls as $n => $url) {
      $colFields = $protoFields;
      $colFields[1]['text'] = St::tttt($colFields[1]['text'], [
        'url'   => $url,
        'smUrl' => Misc::getFilePrefexedPath($url, 'sm_'),
      ]);
      $colFields[2]['name'] = $colFields[2]['name']."[$n]";
      $colFields[3]['name'] = $colFields[3]['name']."[$n]";
      foreach ($colFields as $f) $fields[] = $f;
    }
    if (Auth::get('id')) {
      $fields[] = [
        'type' => 'col',
        'name' => 'userInfo'
      ];
      $fields[] = [
        'type' => 'staticText',
        'text' => 'Вы добавляете это сообщения как '.Auth::get('login')
      ];
    }
    parent::__construct($fields, 'items', [
      'submitTitle' => 'Добавить',
      'idByClass' => true,
      'jsClassById' => true
    ]);
    $this->action = '/json_create';
  }

}