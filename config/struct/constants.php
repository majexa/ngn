<?php

return [
  'core' => [
    'title' => 'Core',
    'static' => true, 
    'fields' => [
      'DO_NOT_LOG' => [
        'title' => 'Не вести логи', 
        'type' => 'bool'
      ],
      /*
      'LOG_OUTPUT' => array(
        'title' => 'Выводить лог', 
        'type' => 'bool',
        'default' => false
      ),
      */ 
      'IS_DEBUG' => [
        'title' => 'Отладка', 
        'type' => 'bool',
        'default' => false
      ], 
      'DATA_CACHE' => [
        'title' => 'Кеш данных', 
        'type' => 'bool',
        'default' => false
      ],
      'CACHE_METHOD' => [
        'title' => 'Метод кэширования', 
        'type' => 'select',
        'default' => 'File',
        'options' => [
          'File' => 'Файлы',
          'Memcached' => 'Memcached'
        ]
      ],
      'PROJECT_KEY' => [
        'title' => 'Ключ проекта',
        'required' => true 
      ]
      /*
      'IS_MEMCACHED' => array(
        'title' => 'Мемкешед включен', 
        'type' => 'bool',
        'default' => false
      ),
      */
    ]
  ],
  'more' => [
    'title' => 'More',
    'static' => true, 
    'fields' => [
      'SITE_DOMAIN' => [
        'title' => 'Домен',
      ],
      'DEBUG_STATIC_FILES' => [
        'title' => 'Отладка статических файлов (кеш выключен)', 
        'type' => 'bool',
      ],
      'FORCE_STATIC_FILES_CACHE' => [
        'title' => 'Выключить кэширование статических файлов',
        'type' => 'bool',
      ],
      'TEMPLATE_DEBUG' => [
        'title' => 'Отладка шаблонов', 
        'type' => 'bool',
      ],
      'JSON_DEBUG' => [
        'title' => 'Отладка JSON', 
        'type' => 'bool',
      ],
      'SESSION_EXPIRES' => [
        'title' => 'Время жизни сессии',
        'type' => 'expires',
      ],
      'ALLOW_GOD_MODE' => [
        'title' => 'Позволить режим Бога', 
        'type' => 'bool',
      ],
      /**
       * Номер первого параметра в строке запроса (начиная с нуля)
       * Т.е. если корень сайта находится тут
       * http://site.com/folder/subfolder/, то номер первого параметра
       * должен быть 2
       */
      'FIRST_URL_PARAM_N' => [
        'title' => 'Номер первого параметра в строке запроса',
        'type' => 'num',
      ],
    ]
  ],
  'site' => [
    'title' => 'Сайт', 
    'static' => true, 
    'fields' => [
      'SITE_TITLE' => [
        'title' => 'Название проекта',
        'default' => 'Rename project!',
        'required' => true
      ],
      'UPDATER_URL' => [
        'title' => 'Ссылка на NGN-апдейтер',
      ],
      'ALLOW_SEND' => [
        'title' => "Разрешена отправка email'a",
        'type' => 'bool',
      ],
      'ACCESS_MODE' => [
        'title' => "Режим доступа к сайту",
        'type' => 'select',
        'options' => [
          'all' => 'Все',
          'registered' => 'Зарегистрированые',
        ],
      ],
      /*
      'ROBOT_ID' => [
        'title' => 'Обычный робот', 
        'type' => 'user',
      ],
      'NOTIFY_ROBOT_ID' => [
        'title' => 'Пользователя, от имени которого рассылаются уведомления', 
        'type' => 'user',
      ],
      'RSS_ROBOT_ID' => [
        'title' => 'Пользователь, от имени которого добавляются записи с RSS', 
        'type' => 'user',
      ],
      */
      'JS_REDIRECT' => [
        'title' => 'Java-script редирект вместо HTTP-редиректа',
        'type' => 'bool',
        'required' => true
      ],
      'ADMIN_THEME' => [
        'title' => 'Тема для админки',
        'default' => 'admin'
      ],
      'LAST_DB_PATCH' => [
        'title' => '№ последнего примененного патча БД', 
        'type' => 'num'
      ], 
      'LAST_FILE_PATCH' => [
        'title' => '№ последнего примененного патча файлов', 
        'type' => 'num'
      ], 
    ]////////////////// ================ //////////////////
  ], 
  'database' => [
    'title' => 'База данных', 
    'static' => true, 
    'fields' => [
      'DB_HOST' => [
        'title' => 'Хост'
      ], 
      'DB_NAME' => [
        'title' => 'Имя базы'
      ], 
      'DB_USER' => [
        'title' => 'Пользователь'
      ], 
      'DB_PASS' => [
        'title' => 'Пароль',
        'type' => 'password'
      ],
      'DB_LOGGING' => [
        'title' => 'Логировать SQL-запросы',
        'type' => 'bool'
      ], 
    ]
  ],
  ]
;
