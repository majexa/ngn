<?php

return [
  'core'     => [
    'title'  => 'Core',
    'static' => true,
    'fields' => [
      'IS_DEBUG'     => [
        'title'   => 'Отладка',
        'type'    => 'bool',
        'default' => false
      ],
      'DO_NOT_LOG'   => [
        'title' => 'Не вести логи',
        'type'  => 'bool'
      ],
      /*
      'LOG_OUTPUT' => array(
        'title' => 'Выводить лог', 
        'type' => 'bool',
        'default' => false
      ),
      */
      'DATA_CACHE'   => [
        'title'   => 'Кеш данных',
        'type'    => 'bool',
        'default' => false
      ],
      'CACHE_METHOD' => [
        'title'   => 'Метод кэширования',
        'type'    => 'select',
        'default' => 'File',
        'options' => [
          'File'      => 'Файлы',
          'Memcached' => 'Memcached'
        ]
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
  'more'     => [
    'title'  => 'More',
    'static' => true,
    'fields' => [
      'PROJECT_KEY'  => [
        'title'    => 'Ключ проекта',
        'disabled' => true,
        'required' => true
      ],
      'SITE_DOMAIN'              => [
        'title' => 'Домен',
      ],
      'LOCALE'                     => [
        'title'   => 'Язык',
        'type'    => 'select',
        'options' => ['ru-RU', 'en-US']
      ],
      'DEBUG_STATIC_FILES'       => [
        'title' => 'Отладка статических файлов (кеш выключен)',
        'type'  => 'bool',
      ],
      'FORCE_STATIC_FILES_CACHE' => [
        'title' => 'Выключить кэширование статических файлов',
        'type'  => 'bool',
      ],
      'TEMPLATE_DEBUG'           => [
        'title' => 'Отладка шаблонов',
        'type'  => 'bool',
      ],
      'JSON_DEBUG'               => [
        'title' => 'Отладка JSON',
        'type'  => 'bool',
      ],
      'SESSION_EXPIRES'          => [
        'title' => 'Время жизни сессии',
        'type'  => 'expires',
      ],
      'ALLOW_GOD_MODE'           => [
        'title' => 'Позволить режим Бога',
        'type'  => 'bool',
      ],
      'BUILD_MODE' => [
          'title' => 'Включить режим рантайм сбрки статических файлов',
          'type'  => 'bool',
      ],
      /**
       * Номер первого параметра в строке запроса (начиная с нуля)
       * Т.е. если корень сайта находится тут
       * http://site.com/folder/subfolder/, то номер первого параметра
       * должен быть 2
       */
      'FIRST_URL_PARAM_N'        => [
        'title' => 'Номер первого параметра в строке запроса',
        'type'  => 'num',
      ],
    ]
  ],
  'site'     => [
    'title'  => 'Сайт',
    'static' => true,
    'fields' => [
      'SITE_TITLE'      => [
        'title'    => 'Название проекта',
        'default'  => 'Rename project!',
        'required' => true
      ],
      'ALLOW_SEND'      => [
        'title' => "Разрешена отправка email'a",
        'type'  => 'bool',
      ],
      'JS_REDIRECT'     => [
        'title'    => 'Java-script редирект вместо HTTP-редиректа',
        'type'     => 'bool',
        'required' => true
      ],
//      'LAST_DB_PATCH'   => [
//        'title' => '№ последнего примененного патча БД',
//        'type'  => 'num'
//      ],
//      'LAST_FILE_PATCH' => [
//        'title' => '№ последнего примененного патча файлов',
//        'type'  => 'num'
//      ],
    ]
    ////////////////// ================ //////////////////
  ],
  'database' => [
    'title'  => 'База данных',
    'static' => true,
    'fields' => [
      'DB_HOST'    => [
        'title' => 'Хост'
      ],
      'DB_NAME'    => [
        'title' => 'Имя базы'
      ],
      'DB_USER'    => [
        'title' => 'Пользователь'
      ],
      'DB_PASS'    => [
        'title' => 'Пароль',
        'type'  => 'password'
      ],
      'DB_LOGGING' => [
        'title' => 'Логировать SQL-запросы',
        'type'  => 'bool'
      ],
    ]
  ],
];
