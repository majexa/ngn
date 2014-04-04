<?php

class DdoMethods extends Singletone {
  
  public $field;
  
  function __construct() {
    $this->field = [
      'author' => [
        'avatar' => [
          'title' => 'Аватар',
          'dddd' => 'UsersCore::avatar($authorId, $authorLogin)'
        ],
        'avatar2' => [
          'title' => 'Аватар 50%',
          'dddd' => 'UsersCore::avatar($authorId, $authorLogin, `halfSize`)'
        ],
        'avatarAndLogin' => [
          'title' => 'Аватар с логином',
          'dddd' => 'UsersCore::avatarAndLogin($authorId, $authorLogin)'
        ],
        /*
        'mysite' => array(
          'title' => 'Ссылка на Мой сайт',
          'dddd' => '`<a href="http://`.$authorName.`.`.SITE_DOMAIN.`">`.$authorLogin.`</a>`'
        )
        */
      ],
      'user' => [
        'avatar' => [
          'title' => 'Аватар',
          'dddd' => 'UsersCore::avatar($v[`id`], $v[`login`])'
        ],
        'avatar2' => [
          'title' => 'Аватар 50%',
          'dddd' => 'UsersCore::avatar($v[`id`], $v[`login`], `halfSize`)'
        ],
        'avatarAndLogin' => [
          'title' => 'Аватар с логином',
          'dddd' => 'UsersCore::avatarAndLogin($v[`id`], $v[`login`])'
        ],
      ],
      'static' => [
        'clear' => [
          'title' => 'clear',
          'dddd' => '`<div class="clear"><!-- --></div>`'
        ],
        'h2' => [
          'title' => 'Заголовок 2',
          'dddd' => '`<h2>`.$title.`</h2>`'
        ],
        'userDataLink' => [
          'title' => 'Ссылка на информацию о пользователе',
          'dddd' => '`<b class="title">Автор:</b> <a href="`.Tt()->getUserPath($authorId).`">`.$authorLogin.`</a>`'
        ],
        'btnOrder' => [
          'title' => 'Кнопка "заказать"',
          'dddd' => '`<div class="iconsSet"><a href="./order/ordered/`.$id.`" class="order btn btn1"><i></i>`.$title.`</a></div>`'
        ]
      ],
      'commentsCount' => [
        'full' => [
          'title' => 'Со словом "комментарии"',
          'dddd' => '    
$v ? (`<div class="smIcons">
<a class="gray comments`.($v > 2 ? `2` : ``).`"
href="`.$ddddItemLink.`#msgs"><i></i> комментарии (`.$v.`)
</a><div class="clear"><!-- --></div>
</div>`) : ``'
        ]
      ],
      'wisiwig' => [
        'cut100' => [
          'title' => 'Ограничение по длине 100 символов',
          'dddd' => '`<p>`.Misc::cut($v, 100, ` <a href="`.$ddddItemLink.`" class="gray more">ещё...</a></p>`)'
        ],
        'cut200' => [
          'title' => 'Ограничение по длине 200 символов',
          'dddd' => '`<p>`.Misc::cut($v, 200, ` <a href="`.$ddddItemLink.`" class="gray more">ещё...</a></p>`)'
        ],
        'cut300' => [
          'title' => 'Ограничение по длине 300 символов',
          'dddd' => '`<p>`.Misc::cut($v, 300, ` <a href="`.$ddddItemLink.`" class="gray more">ещё...</a></p>`)'
        ],
        'cut500' => [
          'title' => 'Ограничение по длине 500 символов',
          'dddd' => '`<p>`.Misc::cut($v, 500, ` <a href="`.$ddddItemLink.`" class="gray more">ещё...</a></p>`)'
        ],
        'cut1000' => [
          'title' => 'Ограничение по длине 1000 символов',
          'dddd' => '`<p>`.Misc::cut($v, 1000, ` <a href="`.$ddddItemLink.`" class="gray more">ещё...</a></p>`)'
        ],
        'whole' => [
          'title' => 'Весь текст',
          'dddd' => '$v'
        ],
        'wholeMore' => [
          'title' => 'Весь текст + ссылка "читать далее" если поле "text" не пустое',
          'dddd' => '$v . (empty($o->items[$id][`text`]) ? `` : ` <a href="`.$ddddItemLink.`" class="gray more">читать далее...</a>`)'
        ],
      ],
      'typoTextarea' => [
        'titled' => [
          'title' => 'С заголовком',
          'dddd' => '$v ? `<b class="title">`.$title.`:</b> `.nl2br($v) : ``'
        ]
      ],
      'typoText' => [
        'text' => [
          'title' => 'Текст',
          'dddd' => '$v'
        ],
        'h2' => [
          'title' => 'H2',
          'dddd' => '`<h2>`.$v.`</h2>`'
        ],
        'itemLinkIfText' => [
          'title' => 'Ссылка на запись, только если поле "text" не пустое',
          'dddd' => '(isset($o->items[$id][`text`]) and empty($o->items[$id][`text`])) ? `` : `<a href="`.$ddddItemLink.`">`.$v.`</a>`'
        ],
        'h2ItemLinkIfText' => [
          'title' => 'Заголовок 2. Ссылка на запись, только если поле "text" не пустое',
          'dddd' => '(isset($o->items[$id][`text`]) and empty($o->items[$id][`text`])) ? `<h2>`.$v.`</h2>` : `<h2><a href="`.$ddddItemLink.`">`.$v.`</a></h2>`'
        ],
        'h3ItemLinkIfText' => [
          'title' => 'Заголовок 3. Ссылка на запись, только если поле "text" не пустое',
          'dddd' => '(isset($o->items[$id][`text`]) and empty($o->items[$id][`text`])) ? `<h3>`.$v.`</h3>` : `<h3><a href="`.$ddddItemLink.`">`.$v.`</a></h3>`'
        ],
      ],
      'imagePreview' => [
        'directImageLink' => [
          'title' => 'Прямая ссылка на изображение',
          'dddd' => '$v ? `<a href="`.Misc::getFilePrefexedPath($v, `md_`).`" class="thumb"><img src="`.Misc::getFilePrefexedPath($v, `sm_`).`" /></a>` : ``',
        ],
        'mdImageLink' => [
          'title' => 'Изображение md',
          'dddd' => '$v ? `<a href="`.$v.`" class="thumb lightbox" target="_blank"><img src="`.Misc::getFilePrefexedPath($v, `md_`).`" /></a>` : ``',
        ],
        'halfSmImage' => [
          'title' => '50% sm-изображения',
          'dddd' => '$v ? `<a href="`.$ddddItemLink.`" class="thumb halfSize"><img src="`.Misc::getFilePrefexedPath($v, `sm_`).`" /></a>` : ``',
        ],
        'showDummyImage' => [
          'title' => 'Показывать вместо отсутствующего изображения заглушку "нет фото"',
          'dddd' => '!$v ? `<a href="`.$ddddItemLink.`" class="thumb"><img src="/i/img/no-images.gif" /></a>` : `<a href="`.$ddddItemLink.`" class="thumb"><img src="`.Misc::getFilePrefexedPath($v, `sm_`).`" /></a>`',
        ],
        'showDummyImagePadding' => [
          'title' => 'Показывать вместо отсутствующего изображения заглушку "нет фото" + отступ',
          'dddd' => '!$v ? `<a href="`.$ddddItemLink.`" class="thumb thumbPadding"><img src="/i/img/no-images.gif" /></a>` : `<a href="`.$ddddItemLink.`" class="thumb thumbPadding"><img src="`.Misc::getFilePrefexedPath($v, `sm_`).`" /></a>`',
        ],
        'showDummyImage2' => [
          'title' => 'Показывать вместо отсутствующего изображения заглушку "нет фото". Ссылку, если есть поле text не пустое',
          'dddd' => '!$v ? `<a`.(empty($o->items[$id][`text`]) ? `` : ` href="`.Tt()->getPath(0).`/`.$ddddItemLink.`"`).` class="thumb"><img src="/i/img/no-images.gif" /></a>` : `<a `.(empty($o->items[$id][`text`]) ? `` : ` href="`.Tt()->getPath(0).`/`.$ddddItemLink.`"`).` class="thumb"><img src="`.Misc::getFilePrefexedPath($v, `sm_`).`" /></a>`',
        ],
      	'middleImageUrl' => [
          'title' => 'Средняя картинка + ссылка на URL',
          'dddd' => '$v ? `<a href="`.$o->items[$id][`url`].`" class="thumb"><img src="`.Misc::getFilePrefexedPath($v, `md_`).`" /></a>` : ``',
        ],
        'lightbox' => [
          'title' => 'Lightbox',
          'dddd' => '$v ? `<a href="`.$v.`" class="thumb lightbox"><img src="`.Misc::getFilePrefexedPath($v, `sm_`).`" alt="`.$o->items[$id][`title`].`" title="`.$o->items[$id][`title`].`"></a>` : ``'
        ]
      ],
      'url' => [
        'previewAndLink' => [
          'title' => 'Превью и ссылка',
          'dddd' => '$v ? `<a href="`.$v.`" class="thumb" target="_blank"><img src="`.SitePreview::url($v).`" alt="`.$o->items[$id][`title`].`" title="`.$o->items[$id][`title`].`"></a><h2><a href="`.$v.`" target="_blank">`.$o->items[$id][`title`].`</a></h2>` : ``'
        ]
      ],
      /*
      'float' => array(
        'rubles' => array(
          'title' => 'Рубли',
          'dddd' => '`<b class="title">`.$title.`:</b> `.$v.` руб.`'
        ),
        'rubles' => array(
          'title' => 'Доллары',
          'dddd' => '`<b class="title">`.$title.`:</b> `.$v.` $`'
        ),
        'rubles' => array(
          'title' => 'Евро',
          'dddd' => '`<b class="title">`.$title.`:</b> `.$v.` €`'
        ),
      )
      */
      'video' => [
        'popup' => [
          'title' => 'Открывается в попапе',
          'tpl' => 'elements/video.popup'
        ],
        'playlist' => [
          'title' => 'С плейлистом',
          'tpl' => 'elements/video.playlist'
        ]
      ],
      'datetime' => [
        'datetime' => [
          'title' => 'Дата и время',
          'dddd' => 'datetimeStr($o->items[$id][$name.`_tStamp`])'
        ],
        'time' => [
          'title' => 'Время',
          'dddd' => 'date(`H:i`, $o->items[$id][$name.`_tStamp`])'
        ],
        'date' => [
          'title' => 'Дата',
          'dddd' => 'dateStr($o->items[$id][$name.`_tStamp`])'
        ],
      ],
      'ddTagsSelect' => [
        'noLink' => [
          'title' => 'без ссылки',
          'dddd' => '$v ? $v[`title`] : ``',
        ],
        'control' => [
           'title' => 'элемент управления',
           'dddd' => '(new FieldEDdTagsSelect([
             `strName` => `orders`,
             `name` => $name,
             `value` => $v[`id`],
             `required` => $f[`required`]
           ]))->html()'
        ]
      ],
      'ddTags' => [
        'iconed' => [
          'title' => 'с иконками',
          'tpl' => 'dd/tagsList'
        ]
      ]
    ];
    $this->field['typoTextarea'] += $this->field['wisiwig'];
    $this->field['wisiwigSimple'] = $this->field['wisiwig'];
    $this->field['ddTagsMultiselect'] = $this->field['ddTags'];
    $this->field['ddUserImage'] = $this->field['imagePreview'];
  }
  
}
