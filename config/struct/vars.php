<?php

/**
 * Формат "visibilityConditions":
 * array(
'headerName' => 'имя поля хедера',
'condFieldName' => 'имя элемента условие для которго проверяется',
'cond' => 'условие (javascript-код)',
)
 *
 */

$enable = [
  'type'  => 'bool',
  'title' => 'Включено'
];

return [
  // Тестовые
  'showItemsOnMap'           => [
    'type'   => 'array',
    'title'  => 'Страницы',
    'fields' => [
      'page'  => [
        'title' => 'Раздел',
        'type'  => 'page'
      ],
      'dummy' => [
        'title' => 'dummy'
      ]
    ]
  ],
  'lang-admin-en'            => [
    'type' => 'hash'
  ],
  // Стандартные 
  'developer-ips'            => [
    'title' => 'IP адреса разработчиков'
  ],
  'lang'                     => [
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
  'hideOnlineStatusUsers'    => [
    'title'      => 'Не показывать в списке онлайн-пользователей',
    'type'       => 'fieldList',
    'fieldsType' => 'user'
  ],
  'adminExtras'              => [
    'title'  => 'Админ: дополнения',
    'fields' => [
      'homeHtml' => [
        'title'     => 'Дополнительный код для главной страницы админки',
        'type'      => 'textarea',
        'maxlength' => 10000
      ]
    ]
  ],
  'admins'                   => [
    'title'      => 'Админы',
    'type'       => 'fieldList',
    'fieldsType' => 'user'
  ],
  'gods'                     => [
    'title'      => 'Боги',
    'type'       => 'fieldList',
    'fieldsType' => 'user'
  ],
  'layout'                   => [
    'title'  => 'Оформление',
    'fields' => [
      'pageTitleFormat'   => [
        'title'   => 'Вид отображения заголовка в теге TITLE',
        'type'    => 'select',
        'default' => 1,
        'options' => [
          1 => 'Название сайта — Имя страницы',
          2 => 'Имя страницы — Название сайта',
        ]
      ],
      'enableShareButton' => [
        'title' => 'Включить кнопку "Поделиться"',
        'type'  => 'bool'
      ]
    ]
  ],
  'rating'                   => [
    'title'                => 'Рейтинг',
    'fields'               => [
      'ratingVoterType'      => [
        'title'   => 'Тип голосования',
        'type'    => 'select',
        'options' => [
          'simple' => 'Голосовать может любой посетитель',
          'auth'   => 'Голосовать может любой авторизованый пользователь',
          'level'  => 'Голосовать может любой пользователь с уровнем выше нуля'
        ]
      ],
      'maxStarsN'            => [
        'title' => 'Максимальное количество звёзд для голосования. Используется в том случае, если используется тип голосования без ограничений по уровню',
        'type'  => 'num'
      ],
      'isMinus'              => [
        'title' => 'Минусовое голосование',
        'type'  => 'bool'
      ],
      'allowVotingLogForAll' => [
        'title' => 'Разрешить просмотр лога голосований для всех',
        'type'  => 'bool'
      ],
      'grade'                => [
        'title' => 'Настройки оценки (только для типа с авторизованными пользователями)',
        'type'  => 'header'
      ],
      'gradeEnabled'         => [
        'title' => 'Оценка включена',
        'type'  => 'bool'
      ],
      'gradeBegin'           => [
        'type' => 'header'
      ],
      'gradeSetPeriod'       => [
        'title'   => 'Период по истечении которого выставляется оценка',
        'type'    => 'select',
        'options' => [
          86400    => 'сутки',
          259200   => '3 дня',
          604800   => 'неделя',
          1209600  => '2 недели',
          2592000  => 'месяц',
          5184000  => '2 месяца',
          7776000  => '3 месяца',
          15552000 => '6 месяцев',
          31104000 => 'год',
        ]
      ],
      'gradeSetDay'          => [
        'title'   => 'День недели для назначения оценки (время: 4 утра)',
        'type'    => 'select',
        'options' => Arr::filterByKeys(Misc::weekdays(), [1, 6, 7]),
      ],
      'grade5percent'        => [
        'title' => '% от всех записей за указанный период, набирающих 5 баллов',
        'type'  => 'num'
      ],
      'grade4percent'        => [
        'title' => '% от всех записей за указанный период, набирающих 4 балла',
        'type'  => 'num'
      ],
      'grade3percent'        => [
        'title' => '% от всех записей за указанный период, набирающих 3 балла',
        'type'  => 'num'
      ],
    ],
    'visibilityConditions' => [
      [
        'headerName'    => 'gradeBegin',
        'condFieldName' => 'gradeEnabled',
        'cond'          => 'v == true',
      ]
    ]
  ],
  'level'                    => [
    'title'                => 'Уровни',
    'fields'               => [
      'on'                      => [
        'title'   => 'Включены',
        'type'    => 'bool',
        'default' => false,
      ],
      'interval'                => [
        'title'   => 'Интервал для сбора данных для назначения уровня',
        'type'    => 'select',
        'options' => [
          43200    => '12 часов',
          86400    => 'сутки',
          172800   => '2 суток',
          604800   => 'неделя',
          1209600  => '2 недели',
          2592000  => 'месяц',
          7776000  => '3 месяца',
          15552000 => '6 месяцев',
          31104000 => 'год',
          62208000 => '2 года',
          93312000 => '3 года'
        ],
        'default' => 43200
      ],
      'avatars'                 => [
        'title' => 'Добавлять иконку уровня на аватар',
        'type'  => 'bool'
      ],
      'commentsTagsLayer2Level' => [
        'title'   => 'Уровень для дополнительных тэгов <!-- 2-го ранга --> в комментариях',
        'type'    => 'select',
        'options' => [
          1  => 1,
          2  => 2,
          3  => 3,
          4  => 4,
          5  => 5,
          6  => 6,
          7  => 7,
          8  => 8,
          9  => 9,
          10 => 10,
        ],
      ],
    ],
    'visibilityConditions' => [
      [
        'headerName'    => 'begin',
        'condFieldName' => 'on',
        'cond'          => 'v == true',
      ]
    ]
  ],
  'levelStars'               => [
    'title'  => 'Уровни: звёзды',
    'fields' => [
      'level'     => [
        'title' => 'Уровень',
        'type'  => 'num'
      ],
      'maxStarsN' => [
        'title' => 'Максимальное количество звёзд за раз',
        'type'  => 'num'
      ]
    ]
  ],
  'dd'                       => [
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
      /*
      'itemsN' => [
        'title' => 'Кол-во записей на странице по умолчанию',
        'type' => 'select',
        'options' => [
          3=>3, 5=>5, 10=>10, 15=>15, 20=>20, 30=>30, 40=>40, 50=>50, 100=>100, 200=>200, 300=>300, 1000=>1000, 9999999 => 'очень много'
        ],
        'default' => 30
      ],
      */
      'enableSubscribe'         => [
        'title' => 'Включить подписку',
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
        'title'  => 'Использовать значения следующие полей в качестве классов записей',
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
  'ddLayouts'                => [
    'title'  => 'DD-Layouts',
    'fields' => [
      [
        'title'  => 'layouts',
        'type'   => 'fieldSet',
        'fields' => [
          [
            'title' => 'Имя',
            'name'  => 'name',
            'type'  => 'name',
          ], [
            'title' => 'Название',
            'name'  => 'title'
          ],
        ]
      ]
    ]
  ],
  'watermark'                => [
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
  'eventsInfo'               => [
    'title'  => 'Шаблоны событий',
    'fields' => [
      'title' => [
        'title' => 'Заголовок',
        'type'  => 'text'
      ],
      'text'  => [
        'title' => 'Текст',
        'type'  => 'textarea'
      ]
    ]
  ],
  'mail'                     => [
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
  'smtp'                     => [
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
  'subscribe'                => [
    'title'                => 'Рассылка',
    'fields'               => [
      'onReg'          => [
        'title'   => 'Включить подписку на листы рассылок при регистрации',
        'type'    => 'bool',
        'default' => false
      ],
      'reg'            => ['type' => 'header'],
      'regHeaderTitle' => [
        'title' => 'Заголовок для секции регистрации',
      ],
      'other'          => ['type' => 'header'],
      'jobsInStep'     => [
        'title'   => "По сколько писем отправлять за один запрос",
        'type'    => 'num',
        'default' => 5
      ]
    ],
    'visibilityConditions' => [
      [
        'headerName'    => 'reg',
        'condFieldName' => 'onReg',
        'cond'          => 'v == true',
      ]
    ]
  ],
  'userReg'                  => [
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
      'pageIds'              => [
        'title'      => 'Дополнительные разделы в блоке авторизованого пользователя',
        'type'       => 'fieldList',
        'fieldsType' => 'pageId',
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
  'role'                     => [
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
          ], [
            'title' => 'Название роли',
            'name'  => 'title'
          ], [
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
          ], [
            'title' => 'Раздел',
            'name'  => 'pageId',
            'type'  => 'pageId'

            //'type' => 'select',
            //'options' => Config::getVarVar('role', 'roles', true)
          ], [
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
  'adminPriv'                => [
    'title'  => 'Админ: привелегии',
    'fields' => [
      ['type' => 'col'], [
        'name'       => 'allowedAdminModules',
        'title'      => 'Доступные модули панели управления',
        'type'       => 'fieldList',
        'fieldsType' => 'adminModules'
      ], ['type' => 'col'], [
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
  'tiny'                     => [
    'title'  => 'Визуальный редактор',
    'fields' => [
      'typo' => [
        'title' => 'Типографировать текст',
        'type'  => 'bool'
      ]
    ]
  ],
  'tiny.admin.allowedTags'   => [
    'title' => 'Админ: доступные HTML-тэги'
  ],
  'tiny.admin.classes'       => [
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
  'tiny.admin.disableBtns'   => [
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
  'plusItemsDefault'         => [
    'title'  => 'Присвоение плюсов за записи',
    'static' => true,
    'fields' => [
      'n' => [
        'title' => 'Кол-во записей за единицу времени, которое будет давать плюсы',
        'type'  => 'num'
      ],
      't' => [
        'title' => 'Единица времени (секунд)',
        'type'  => 'num'
      ],
      'e' => [
        'title' => 'Кол-во плюсов, получаемое в результате добавления n работ за t время',
        'type'  => 'num'
      ]
    ]
  ],
  'commentsPages'            => [
    'title'      => 'Разделы для последних комментариев',
    'type'       => 'fieldList',
    'fieldsType' => 'pageId'
  ],
  'sape'                     => [
    'title'                => 'SAPE',
    'fields'               => [
      'enable'    => [
        'title' => 'Включено',
        'type'  => 'bool',
      ],
      'begin'     => ['type' => 'header'],
      'code'      => [
        'title' => 'Код',
      ],
      'multiSite' => [
        'title' => 'Мультисайт',
        'type'  => 'bool'
      ],
      'linksN'    => [
        'title' => 'Количество ссылок',
        'type'  => 'num'
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
  'grabber'                  => [
    'title'                => 'Граббер',
    'fields'               => [
      'enable'                    => [
        'title' => 'Включено',
        'type'  => 'bool',
      ],
      'begin'                     => ['type' => 'header'],
      'period'                    => [
        'title'   => 'Частота сбора данных',
        'type'    => 'select',
        'options' => Arr::toOptions(CronPeriod::getPeriods(), 'title')
      ],
      'attemptsBeforeDisactivate' => [
        'title'   => 'Количество неудачных попыток до отключения канала',
        'type'    => 'select',
        'options' => [
          1   => 1,
          2   => 2,
          5   => 5,
          10  => 10,
          30  => 30,
          100 => 100
        ]
      ],
      'admin'                     => [
        'title' => 'Пользователь, получающий уведомления об ошибках',
        'type'  => 'user',
      ],
      'itemsLimit'                => [
        'title' => 'Лимит записей получаемых при каждой проверке обновлений на канале (значение по умолчанию)',
        'type'  => 'num'
      ]
    ],
    'visibilityConditions' => [
      [
        'headerName'    => 'begin',
        'condFieldName' => 'enable',
        'cond'          => 'v == true',
      ]
    ],
  ],
  'mysite'                   => [
    'title'                => 'Мой сайт',
    'fields'               => [
      'enable'                   => [
        'title' => 'Включен',
        'type'  => 'bool',
      ],
      'allowHomeRedefineByOwner' => [
        'title' => 'Разрешить переопределение домашней странички владельцем Моего сайта',
        'type'  => 'bool'
      ],
      'homeType'                 => [
        'title'   => 'Тип домашней странички',
        'type'    => 'select',
        'options' => [
          'userData' => 'Профиль',
          'items'    => 'Записи',
          'blocks'   => 'Блоки',
        ]
      ],
      'pageBegin'                => ['type' => 'header'],
      'homePageId'               => [
        'title' => 'Раздел для домашней странички',
        'type'  => 'page'
      ],
      //'pageEnd' => array('type' => 'header'), 
    ],
    'visibilityConditions' => [
      [
        'headerName'    => 'pageBegin',
        'condFieldName' => 'homeType',
        'cond'          => 'v == "items"',
      ]
    ]
  ],
  'mysiteReservedSubdomains' => [
    'title' => 'Мой сайт: зарезервированые сабдомены', [
      [
        'title' => 'Сабдомен',
      ],
    ]
  ],
  'notify'                   => [
    'title'  => 'Уведомления',
    'fields' => [
      'enable' => [
        'title' => 'Включены',
        'type'  => 'bool',
      ],
    ]
  ],
  'event'                    => [
    'title'  => 'События',
    'fields' => [
      'forceModerSubscribe' => [
        'title' => 'Принудительная отправка уведомления не обращая внимания на подписку',
        'type'  => 'bool',
      ],
    ]
  ],
  'theme'                    => [
    'title'                => 'Тема',
    'fields'               => [
      'enabled' => [
        'title'   => 'Включена',
        'type'    => 'bool',
        'default' => false,
      ],
      'begin'   => ['type' => 'header'],
      'theme'   => [
        'title' => 'Тема',
        'type'  => 'stmThemeSelect'
      ]
    ],
    'visibilityConditions' => [
      [
        'headerName'    => 'begin',
        'condFieldName' => 'enabled',
        'cond'          => 'v == true',
      ]
    ]
  ],
  'url'                      => [
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
  'google'                   => [
    'title'  => 'Google',
    'fields' => [
      'mapKey' => [
        'title' => 'Google Map Key',
      ]
    ]
  ],
  'yandex'                   => [
    'title'  => 'Яндекс',
    'fields' => [
      'verification' => [
        'title' => 'Код подтверждения подлинности сайта',
      ]
    ]
  ],
  'menu'                     => [
    'title'                => 'Меню',
    'fields'               => [
      'useTagsAsSubmenu'  => [
        'title' => 'Использовать тэги раздела в качестве подразделов меню',
        'type'  => 'bool'
      ],
      'begin'             => ['type' => 'header'],
      'showNullCountTags' => [
        'title' => 'Показывать тэги без записей',
        'type'  => 'bool'
      ]
    ],
    'visibilityConditions' => [
      [
        'headerName'    => 'begin',
        'condFieldName' => 'useTagsAsSubmenu',
        'cond'          => 'v == true',
      ]
    ]
  ],
  'piwik'                    => [
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
  'stat'                     => [
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
  'vk'                       => [
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
  'vkAuth'                   => [
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
  'store'                    => [
    'title'  => 'Магазин',
    'fields' => [
      'enable'                => [
        'type'  => 'bool',
        'title' => 'Включен'
      ],
      'orderControllerSuffix' => [
        'title' => 'Контроллер заказа',
        'type'  => 'storeOrderControllerSuffix'
      ],
      'ordersPageId'          => [
        'title' => 'Раздел с базой заказов',
        'type'  => 'pageId'
      ],
      'orderParams'           => [
        'title' => 'Дополнительные поля заказа',
        'type'  => 'storeOrderFields'
      ],
      'orderBehaviors'        => [
        'title'   => 'Опции заказа',
        'type'    => 'multiselect',
        'options' => [
          'sendToAdmins' => 'Отправлять e-mail с текстом заказа <a href="/admin/configManager/vvv/admins">администраторам</a>'
        ]
      ],
    ]
  ],
  'userStore'                => [
    'title'                => 'Пользовательский магазин',
    'fields'               => [
      'enable' => [
        'title' => 'Включен',
        'type'  => 'bool'
      ],
      'begin'  => ['type' => 'header'],
      'roles'  => [
        'title' => 'Роли пользователей, имеющих доступ к магазину',
        'type'  => 'roleMultiselect'
      ],
    ],
    'visibilityConditions' => [
      [
        'headerName'    => 'begin',
        'condFieldName' => 'enable',
        'cond'          => 'v == true',
      ]
    ]
  ],
  'userGroup'                => [
    'title'  => 'Сообщества',
    'fields' => [
      'enable' => [
        'title' => 'Включены',
        'type'  => 'bool'
      ]
    ],
  ],
  'profile'                  => [
    'title'                => 'Профиль',
    'fields'               => [
      'enable'             => [
        'title' => 'Включен',
        'type'  => 'bool'
      ],
      'userInfoBlockType'  => [
        'title'   => 'Тип блока с информацией пользователя',
        'type'    => 'select',
        'options' => [
          ''             => 'Логин + изображение, если есть, кнопки',
          'profileField' => 'Поле из профиля + изображение, если есть, кнопки',
        ]
      ],
      'profileFieldBegin'  => ['type' => 'header'],
      'userInfoBlockField' => [
        'title' => 'Поле заголовка блока с информацией пользователя',
        'type'  => 'profileFields'
      ],
    ],
    'visibilityConditions' => [
      [
        'headerName'    => 'profileFieldBegin',
        'condFieldName' => 'userInfoBlockType',
        'cond'          => 'v == "profileField"',
      ]
    ]
  ],
  'privMsgs'                 => [
    'title'  => 'Приватные сообщения',
    'fields' => [
      'enable' => $enable
    ]
  ],
  'littleSms'                => [
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
