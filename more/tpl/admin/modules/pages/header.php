<?

$links = [];
$curPageModulePath = $this->getPath(1).'/pages/'.($d['page']['id'] ? $d['page']['id'] : 0);

$isItems = DdCore::isItemsController($d['page']['controller']);
$isMasterItems = DdCore::isMasterController($d['page']['controller']);

if ($d['action'] != 'editContent' and ($d['page']['folder'] or !$d['page'])) {
  $t = $d['page']['id'] ? LANG_CREATE_SECTION : 'Создать корневой раздел';
  $links[] = [
    'title' => $t,
    'class' => 'add createModulePage',
    'link'  => $curPageModulePath.'/newModulePage'
  ];
  if ($d['params'][0] == 'god') {
    $links[] = [
      'title' => $t.' (ручное)',
      'class' => 'add2 createPage',
      'link'  => $curPageModulePath.'/newPage'
    ];
  }
}
/*
if ($d['params'][0] == 'god') {
  $links[] = array(
    'title' => 'Создать slave-раздел',
    'class' => 'add',
    'link' => $this->getPath(3).'?a=newSlavePage'
  );
}
*/
if ($d['page']) {
  $editContentPath = $curPageModulePath.'/editContent';
  if ($d['page']['strName']) {
    if ($d['action'] == 'editContent' and $isItems) {
      $editContentFullPath = $curPageModulePath.'/editContent'.(!empty($d['pageCtrl']->page['slave']) ? '/v.'.DdCore::masterFieldName.'.'.$d['pageCtrl']->d['masterItem']['id'] : '');
      $links[] = [
        'title' => '<b>Добавить '.NgnMorph::cast($d['pageCtrl']->settings['itemTitle'], ['ЕД', 'ВН']).'</b>',
        'descr' => 'Создать новую запись в разделе «<b>'.$d['page']['title'].'</b>»',
        'class' => 'add',
        'link'  => $editContentFullPath.'?a=new'.(!empty($d['pageCtrl']->page['slave']) ? '&default['.DdCore::masterFieldName.']='.$d['pageCtrl']->d['masterItem']['id'] : '')
      ];
      if ($isItems) {
      	$links[] = [
      			'title' => '<b>'.ucfirst(NgnMorph::singular2plural($d['pageCtrl']->settings['itemTitle'])).'</b>',
      			'descr' => 'Список записей раздела «<b>'.$d['page']['title'].'</b>»',
      			'class' => 'list',
      			'link'  => $editContentPath
      	];
      }
      else {
      	$links[] = [
      			'title' => '<b>Содержание</b>',
      			'descr' => 'Редактировать содержание раздела «<b>'.$d['page']['title'].'</b>»',
      			'class' => 'edit',
      			'link'  => $editContentPath
      	];
      }
      /*
      $links[] = [
        'title' => 'Настройки контроллера',
        'class' => 'settings editControllerSettings',
        'link'  => ''
      ];
      */
      if ($d['pcd']['items']) {
        if ($d['pcd']['action'] == 'list') {
          $links[] = [
            'title' => 'Выделить все',
            'class' => 'select'
          ];
        }
        /*
        $links[] = array(
          'title' => 'Переместить выделенные',
          'class' => 'move',
          'link' => $this->getPath().'?a=moveGroupForm'
        );
        */
        $links[] = [
          'title' => 'Удалить выделенные',
          'class' => 'deleteGroup',
          'link'  => $this->getPath().'?a=deleteGroup'
        ];
        if (AdminModule::isAllowed('users')) {
          $links[] = [
            'title' => 'Сменить автора',
            'class' => 'users',
            'link'  => $this->getPath().'?a=changeAuthorGroupForm'
          ];
        }
      }
    }
  }
  $links[] = [
    'title'  => '<b>Открыть</b>',
    'descr'  => 'Открыть раздел «<b>'.$d['page']['title'].'</b>» на сайте',
    'class'  => 'link',
    'link'   => $this->getPath(0).'/'.$d['page']['path'].($d['page']['slave'] ? $this->getPathLast(1) : ''),
    'target' => '_blank'
  ];
  if (AdminModule::_isAllowed('slices') or $d['params'][0] == 'god') {
    $r = db()->query("SELECT id, title FROM slices WHERE pageId=?d", $d['page']['id']);
    foreach ($r as $v) {
      $links[] = [
        'title' => '<b>'.$v['title'].'</b>',
        'descr' => 'Редактировать: '.$v['title'],
        'class' => 'slices',
        'link'  => $this->getPath(1).'/slices/'.$d['page']['id'].'/'.$v['id'],
        //'target' => '_blank'
      ];
    }
  }
  $links[] = [
    'title' => LANG_PAGE_PROPERTIES,
    'class' => 'editProp',
    'link'  => $curPageModulePath.'/editPage'
  ];
  if ($d['extraButtons']) {
    foreach ($d['extraButtons'] as $v) {
      $links[] = [
        'title' => $v['title'],
        'class' => $v['privileges'],
        'link'  => $v['link']
      ];
    }
  }
  if (AdminModule::_isAllowed('slices') or $d['params'][0] == 'god') {
    // Slices
    $links[] = [
      'title' => 'Создать слайс',
      'class' => 'add',
      'link'  => $this->getPath(1).'/slices/'.$d['page']['id'].'/new'
    ];
  }
  if ((AdminModule::_isAllowed('pageLayout') or $d['params'][0] == 'god')) {
    $links[] = [
      'title'   => 'Формат страницы',
      'descr'   => 'Задать особый формат страницы для раздела «<b>'.$d['page']['title'].'</b>»',
      'class'   => 'layout',
      'link'    => $this->getPath(1).'/pageLayout/'.$d['page']['id'],
      'onclick' => "if (confirm('Вы действительно хотите создать особый формат страницы для раздела «{$d['page']['title']}»?')) window.location = this.href; return false;"
    ];
  }
  if (DdCore::isDdController($d['page']['controller']) and (AdminModule::_isAllowed('ddo') or $d['params'][0] == 'god')) {
    $links[] = [
      'title' => 'Управление выводом полей',
      'class' => 'list',
      'link'  => $this->getPath(1).'/ddo/'.$d['page']['id'],
    ];
  }
  $links[] = [
    'title' => 'Выгрузить в Excel',
    'class' => 'xls'
  ];

  /*
  if (AdminModule::_isAllowed('pageBlocks') or $d['params'][0] == 'god') {
    $blocksCount = PageBlockCore::getDynamicBlocksCount($d['page']['id']);
    $links[] = array(
      'title' => 'Блоки'.($blocksCount ? " (<b>$blocksCount</b>)" : ''),
      'class' => 'pageBlocks',
      'link'  => $this->getPath(1).'/pageBlocks/'.$d['page']['id']
    );
  }
  /*
  if ($d['params'][0] == 'god') {
    $links[] = array(
      'title' => 'Параметры блоков',
      'descr' => 'Параметры блоков раздела «<b>'.$d['page']['title'].'</b>»',
      'class' => 'editOptions',
      'link' => $this->getPath(3).'/editBlocksSettings'
    );
    $links[] = array(
      'title' => 'Параметры блоков по умолчанию',
      'class' => 'editOptions',
      'link' => $this->getPath(3).'/editBlocksDefaultSettings'
    );
  }
  */

  if ($d['pageBlocks']) {
    foreach ($d['pageBlocks'] as $v) {
      $links[] = [
        'title' => $v['title'],
        'class' => 'edit',
        'link'  => $this->getPath(3).'?a=editBlock&blockId='.$v['id']
      ];
    }
  }
}
if ($d['page']) {
  if (!empty($d['page']['settings']['tagField'])) {
    $links[] = [
      'title' => 'Теги',
      'class' => 'tags',
      'link'  => $this->getPath(1).'/tags/'.db()->getCell('tags_groups', 'id', 'name', $d['page']['settings']['tagField']).'/list',
    ];
  }
}

if ($d['action'] == 'editControllerSettings' and $isItems) {
  if ($d['page']['settings']['order'] != 'oid' and $d['page']['settings']['order'] != 'oid DESC') {
    $links[] = [
      'title'   => 'Включить ручную сортировку',
      'class'   => 'turnOn',
      'link'    => $this->getPath().'?a=setOidPageOrder',
      'onclick' => "if (confirm('Включение ручной сортировки изменит сортировку записей этого раздела. Вы действительно уверены, что хотите этого?')) window.location = this.href; return false;"
    ];
  }
  else {
    $links[] = [
      'title'   => 'Выключить ручную сортировку',
      'class'   => 'turnOff',
      'link'    => $this->getPath().'?a=resetOidPageOrder',
      'onclick' => "if (confirm('Выключение ручной сортировки удалит сортировку записей этого раздела. Вы действительно уверены, что хотите этого?')) window.location = this.href; return false;"
    ];
  }
  if (empty($d['page']['settings']['showRating'])) {
    $links[] = [
      'title' => 'Включить рейтинг',
      'class' => 'turnOn confirm',
      'link'  => $this->getPath().'?a=setRatingOn'
    ];
  }
  else {
    $links[] = [
      'title' => 'Выключить рейтинг',
      'class' => 'turnOff confirm',
      'link'  => $this->getPath().'?a=setRatingOff'
    ];
  }
}
/*
if (0 and $d['pcd']['itemId']) {
  $links[] = [
    'title' => 'Версии',
    'class' => 'versions',
    'link'  => $this->getPath(3).'/versions/'.$d['pcd']['itemId'],
  ];
  $links[] = [
    'title' => 'Автосохранения',
    'class' => 'autosave',
    'link'  => $this->getPath(3).'/autosaves/'.$d['pcd']['itemId']
  ];
}
*/
if ($d['page']['controller'] == 'albums') {
  if (AdminModule::_isAllowed('importAlbums') or $d['params'][0] == 'god') {
    $links[] = [
      'title' => 'Импортировать альбомы',
      'class' => 'import',
      'link'  => $this->getPath(1).'/importAlbums/'.$d['page']['id']
    ];
  }
}

?>

<style>
  .navSub a.move, .navSub a.deleteGroup, .navSub a.users {
    display: none;
  }
</style>

<?
$this->tpl('admin/common/module-header', [
  //'title' => $d['page']['title'],
  'links' => $links,
  'target' => 'about:blank'
])
?>

<script type="text/javascript">
  // Добавляем подтверждение к кнопке удаления раздела
  var eSubNav = $('subNav');
  if (eSubNav) {
    eDelete = $('subNav').getElement('a[class=delete]');
    if (eDelete)
      eDelete.addEvent('click', function(e) {
        e.preventDefault();
        if (confirm('Вы уверены?')) console.debug(this);
      });
  }
</script>
