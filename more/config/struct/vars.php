<?php

/**
 * Формат "visibilityConditions":
 * array(
 * 'headerName' => 'имя поля хедера',
 * 'condFieldName' => 'имя элемента условие для которго проверяется',
 * 'cond' => 'условие (javascript-код)',
 * )
 *
 */

$enable = [
  'type'  => 'bool',
  'title' => 'Включено'
];

return [
  'lang-admin-en'          => [
    'type' => 'hash'
  ],
  // Стандартные 
  'lang'                   => [
    'title'  => 'Языки',
    'fields' => [
      'admin' => [
        'title'   => 'Язык панели управления',
        'type'    => 'select',
        'options' => [
          'ru' => 'Русский',
          'en' => 'Английский'
        ]
      ]
    ]
  ],
  'adminExtras'            => [
    'title'  => 'Админ: дополнения',
    'fields' => [
      'homeHtml' => [
        'title'     => 'Дополнительный код для главной страницы админки',
        'type'      => 'textarea',
        'maxlength' => 10000
      ]
    ]
  ],
  'admins'                 => [
    'title'      => 'Админы',
    'type'       => 'fieldList',
    'fieldsType' => 'user'
  ],
  'gods'                   => [
    'title'      => 'Боги',
    'type'       => 'fieldList',
    'fieldsType' => 'user'
  ],
  'dd'                     => [
    'title'  => 'Динамические данные',
    'fields' => [
      'forceCache'              => [
        'title' => 'Выключить кэш',
        'type'  => 'bool'
      ],
      'typo'                    => [
        'title' => 'Типографирование значений форм и стриппинг допустимых тэгов',
        'type'  => 'bool'
      ],
      'allowEditSystemDates'    => [
        'title' => 'Разрешить изменение системных дат',
        'type'  => 'bool'
      ],
      'fancyUploader'           => [
        'title' => 'Загрузка файлов со статусом загрузки',
        'type'  => 'bool'
      ],
      'contentWidth'            => [
        'title'   => 'Ширина контентной области',
        'type'    => 'num',
        'default' => 600
      ],
      'smW'                     => [
        'title'   => 'Ширина превьюшки',
        'type'    => 'num',
        'default' => 100
      ],
      'smH'                     => [
        'title'   => 'Высота превьюшки',
        'type'    => 'num',
        'default' => 80
      ],
      'mdW'                     => [
        'title'   => 'Ширина уменьшенной копии',
        'type'    => 'num',
        'default' => 600
      ],
      'mdH'                     => [
        'title'   => 'Высота уменьшенной копии',
        'type'    => 'num',
        'default' => 400
      ],
      'resizeType'              => [
        'title'   => 'Метод создания превьюшки по умолчанию',
        'type'    => 'select',
        'default' => 'resize',
        'options' => [
          'resize'   => 'Обрезать',
          'resample' => 'Вписывать'
        ]
      ],
      'enableFilters'           => [
        'title' => 'Включить механизм фильтров',
        'type'  => 'bool'
      ],
      'useFieldNameAsItemClass' => [
        'title'  => 'Использовать значения следующих полей в качестве классов записей',
        'type'   => 'fieldSet',
        'fields' => [
          [
            'name'  => 'field',
            'title' => 'Поле'
          ]
        ]
      ]
    ]
  ],
  'watermark'              => [
    'title'                => 'Водяной знак',
    'fields'               => [
      'enable'       => [
        'title'   => 'Вкоючен',
        'type'    => 'bool',
        'default' => false
      ],
      'begin'        => ['type' => 'header'],
      'rightOffset'  => [
        'title'   => 'Отступ от правой границы изображения до водяного знака',
        'type'    => 'num',
        'default' => 10
      ],
      'bottomOffset' => [
        'title'   => 'Отступ от нижней границы изображения до водяного знака',
        'type'    => 'num',
        'default' => 10
      ],
      'q'            => [
        'title'   => 'Качество JPEG (от 0 до 100)',
        'type'    => 'num',
        'default' => 100
      ],
      'path'         => [
        'title' => 'Путь до изображения относительно www корня'
      ]
    ],
    'visibilityConditions' => [
      [
        'headerName'    => 'begin',
        'condFieldName' => 'enable',
        'cond'          => 'v == true',
      ]
    ]
  ],
  'mail'                   => [
    'title'  => 'Почта',
    'fields' => [
      'method'    => [
        'title'   => 'Метод',
        'type'    => 'select',
        'options' => [
          'mail' => 'mail()',
          'smtp' => 'SMTP'
        ]
      ],
      'from'      => [
        'title' => 'Данные для поля "от кого" автоматически рассылаемых писем',
        'type'  => 'header'
      ],
      'fromEmail' => [
        'title'    => 'E-mail',
        'type'     => 'text',
        'required' => true
      ],
      'fromName'  => [
        'title'    => 'Имя',
        'required' => true
      ],
    ]
  ],
  'smtp'                   => [
    'title'                => 'SMTP',
    'fields'               => [
      'server'    => [
        'title' => 'Сервер'
      ],
      'port'      => [
        'title' => 'Порт',
        'type'  => 'num',
        'help'  => '0 - использовать по умолчанию 25 порт'
      ],
      'auth'      => [
        'title' => 'Включить SMTP авторизацию',
        'type'  => 'bool'
      ],
      'authBegin' => [
        'type' => 'header'
      ],
      'user'      => [
        'title' => 'Пользователь'
      ],
      'pass'      => [
        'title' => 'Пароль',
        'type'  => 'password'
      ],
    ],
    'visibilityConditions' => [
      [
        'headerName'    => 'authBegin',
        'condFieldName' => 'auth',
        'cond'          => 'v == true',
      ]
    ]
  ],
  'userReg'                => [
    'title'                => 'Пользователи',
    'fields'               => [
      'enable'               => [
        'title' => 'Регистрация включена',
        'type'  => 'bool'
      ],
      'activation'           => [
        'title'   => 'Активация после регистрации',
        'type'    => 'select',
        'options' => [
          ''      => 'отключена',
          'email' => "по email'у",
          'admin' => 'только администратором'
        ]
      ],
      'noActivation'         => ['type' => 'header'],
      'authorizeAfterReg'    => [
        'title' => 'Авторизовывать после регистрации',
        'type'  => 'bool'
      ],
      'other'                => ['type' => 'header'],
      'loginEnable'          => [
        'title' => "Включить заполнение логина при регистрации",
        'type'  => 'bool'
      ],
      'emailEnable'          => [
        'title' => "Включить заполнение e-mail'a при регистрации",
        'type'  => 'bool'
      ],
      'phoneConfirm'         => [
        'title' => 'Включить подтверждение регистрации по телефону',
        'type'  => 'bool'
      ],
      'phoneEnable'          => [
        'title' => "Включить заполнение телефона при регистрации",
        'type'  => 'bool'
      ],
      'loginAsFullName'      => [
        'title' => "Использовать поле логин для ФИО",
        'type'  => 'bool'
      ],
      'vkAuthEnable'         => [
        'title' => 'Включить авторизацию Вконтакте',
        'type'  => 'bool'
      ],
      'allowLoginEdit'       => [
        'title' => 'Разрешить изменение логина',
        'type'  => 'bool'
      ],
      'allowPassEdit'        => [
        'title' => 'Разрешить изменение пароля',
        'type'  => 'bool'
      ],
      'allowEmailEdit'       => [
        'title' => 'Разрешить изменение e-mail',
        'type'  => 'bool'
      ],
      'col1'                 => ['type' => 'col'],
      'allowPhoneEdit'       => [
        'title' => 'Разрешить изменение телефона',
        'type'  => 'bool'
      ],
      'allowNameEdit'        => [
        'title' => 'Разрешить изменение домена',
        'type'  => 'bool'
      ],
      'allowMysiteThemeEdit' => [
        'title' => 'Разрешить изменение оформления Моего сайта',
        'type'  => 'bool'
      ],
      'redirectToFirstPage'  => [
        'title' => 'Перенаправлять после авторизации с фронтенда на первый раздел из указанных выше',
        'type'  => 'bool'
      ],
      'extraData'            => [
        'title' => 'Дополнительные данные',
        'type'  => 'bool'
      ],
      'titleName'            => [
        'title'    => 'Использовать в качестве подписи пользователя',
        'required' => true,
        'type'     => 'select',
        'options'  => UsersCore::getTitleNames()
      ]
    ],
    'visibilityConditions' => [
      [
        'headerName'    => 'noActivation',
        'condFieldName' => 'activation',
        'cond'          => 'v == false',
      ]
    ],
    'dependRequire'        => [
      ['phoneConfirm', 'phoneEnable']
    ],
  ],
  'role'                   => [
    'title'                => 'Роли',
    'fields'               => [
      'enable' => [
        'title' => 'Включить роль',
        'type'  => 'bool'
      ],
      'role'   => ['type' => 'header'],
      'roles'  => [
        'title'  => 'Роли',
        'type'   => 'fieldSet',
        'fields' => [
          [
            'title' => 'Имя роли',
            'name'  => 'name',
            'type'  => 'name'
          ],
          [
            'title' => 'Название роли',
            'name'  => 'title'
          ],
          [
            'title' => 'Описание роли',
            'name'  => 'text',
            'type'  => 'textarea'
          ],
        ]
      ],
      'priv'   => [
        'title'  => 'Привелегии',
        'type'   => 'fieldSet',
        'fields' => [
          [
            'title' => 'Имя роли',
            'name'  => 'role',
            //'type' => 'select',
            //'options' => Config::getVarVar('role', 'roles', true)
          ],
          [
            'title' => 'Раздел',
            'name'  => 'pageId',
            'type'  => 'pageId'

            //'type' => 'select',
            //'options' => Config::getVarVar('role', 'roles', true)
          ],
          [
            'title' => 'Разрешенная привелегия',
            'name'  => 'priv'
          ]
        ]
      ]
    ],
    'visibilityConditions' => [
      [
        'headerName'    => 'role',
        'condFieldName' => 'enable',
        'cond'          => 'v == true',
      ]
    ]
  ],
  'adminPriv'              => [
    'title'  => 'Админ: привелегии',
    'fields' => [
      ['type' => 'col'],
      [
        'name'       => 'allowedAdminModules',
        'title'      => 'Доступные модули панели управления',
        'type'       => 'fieldList',
        'fieldsType' => 'adminModules'
      ],
      ['type' => 'col'],
      [
        'name'   => 'allowedPageModules',
        'title'  => 'Доступные модули разделов сайта',
        'type'   => 'fieldSet',
        'fields' => [
          [
            'title' => 'Модуль',
            'name'  => 'module',
            'type'  => 'pageModules'
          ]
        ]
      ],
      /*
      array(
        'name' => 'allowedPageConstructors',
        'title' => 'Доступные контроллеры разделов',
        'type' => 'fieldSet',
        'fields' => array(array(
          'title' => 'Контроллер',
          'name' => 'module',
          'type' => 'pageControllers'
        ))
      ),
      array(
        'name' => 'allowedConfigVars',
        'title' => 'Доступные секции конфигурации',
        'type' => 'fieldSet',
        'fields' => array(array(
          'title' => 'Имя секции',
          'name' => 'module',
          'type' => 'configVarNames'
        ))
      )
      */
    ]
  ],
  'tiny'                   => [
    'title'  => 'Визуальный редактор',
    'fields' => [
      'typo' => [
        'title' => 'Типографировать текст',
        'type'  => 'bool'
      ]
    ]
  ],
  'tiny.admin.allowedTags' => [
    'title' => 'Админ: доступные HTML-тэги'
  ],
  'tiny.admin.classes'     => [
    'title'  => 'Админ: доступные CSS-классы',
    'fields' => [
      'title' => [
        'title' => 'Название'
      ],
      'class' => [
        'title' => 'Класс'
      ]
    ]
  ],
  'tiny.admin.disableBtns' => [
    'title'  => 'Админ: выключеные кнопки',
    'fields' => [
      [
        'type'    => 'select',
        'options' => [
          ''              => '—',
          'anchor'        => 'anchor',
          'outdent'       => 'outdent',
          'indent'        => 'indent',
          'strikethrough' => 'strikethrough',
          'justifycenter' => 'justifycenter',
          'justifyfull'   => 'justifyfull',
          'help'          => 'help'
        ]
      ]
    ]
  ],
  'url'                    => [
    'title'  => 'URL',
    'fields' => [
      'cache'          => [
        'title' => 'Кэшировать',
        'type'  => 'bool',
      ],
      'sitePreviewUrl' => [
        'title' => 'Ссылка на генератор превьюшек веб-страниц',
      ],
    ]
  ],
  'google'                 => [
    'title'  => 'Google',
    'fields' => [
      'mapKey' => [
        'title' => 'Google Map Key',
      ]
    ]
  ],
  'yandex'                 => [
    'title'  => 'Яндекс',
    'fields' => [
      'verification' => [
        'title' => 'Код подтверждения подлинности сайта',
      ]
    ]
  ],
  'piwik'                  => [
    'title'  => 'Сервер статистики',
    'fields' => [
      'url'       => [
        'title' => 'Ссылка',
        'type'  => 'url'
      ],
      'authToken' => [
        'title' => 'token авторизации',
      ]
    ]
  ],
  'stat'                   => [
    'title'  => 'Статистика',
    'fields' => [
      'enable' => [
        'title' => 'Включена',
        'type'  => 'bool'
      ],
      'siteId' => [
        'title' => 'ID сайта в системе статистики',
        'help'  => 'определяется автоматически, при включении статистики',
        'type'  => 'hidden'
      ]
    ]
  ],
  'vk'                     => [
    'title'  => 'Вконтакте',
    'fields' => [
      'appId'  => [
        'title' => 'ID приложения',
      ],
      'secKey' => [
        'title' => 'Защищенный ключ',
      ]
    ]
  ],
  'vkAuth'                 => [
    'title'  => 'Вконтакте: авторизация',
    'fields' => [
      'login' => [
        'title' => 'Логин / email',
      ],
      'pass'  => [
        'title' => 'Пароль',
        'type'  => 'password'
      ]
    ]
  ],
  'userGroup'              => [
    'title'  => 'Сообщества',
    'fields' => [
      'enable' => [
        'title' => 'Включены',
        'type'  => 'bool'
      ]
    ],
  ],
  'littleSms'              => [
    'title'  => 'Little SMS',
    'fields' => [
      'user' => [
        'title' => 'Пользователь'
      ],
      'key'  => [
        'title' => 'API-key',
      ],
    ]
  ]
];
