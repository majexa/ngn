#Static File Library Manager#
Автозагрузчик JavaScript объектов

Предпосылки создания автозагрузчика JS-файлов с необходимыми объектами/классами вылились из концепции
fullstack-фреймворка, где существуют компоненты не только на стороне сервера, но и клиент-серверные компоненты,
представляющие сотобой набор из контроллера, моделей базы данных, конфигурации, классов сервероной бизнес логики,
а так же клиентской части: визуальное отображение (рендеринг HTML), та же бизнес-логика и классы, компоненты фреймворка,
необходимые для реализации всего этого. Создавая диалог или поле формы, которое требует JS-логики. Каждый раз
мы нуждаемся в автозагрузке базовых объектов. Как Вы бы вели себя при классическом подходе? Подключили бы
`<script src=".../someJQueryComponent.js"></script>`. И остались бы счастиливы. Но в больших приложения
компоненты не являются нечтом монолитным. Они состоят из множества саб-классов и саб-объектов. В Ngn для того, что
бы создать диалог с формой, нужно подключить файл Ngn.Dialog.RequestForm. Но будем ли мы знать о том, что нам нужно это сделать,
если поле формы использует класс `Ngn.Dialog.Abc` наследуемый от `Ngn.Dialog.RequestForm`. Конечно да. И мы подключим этот
`Ngn.Dialog.RequestForm` и `Ngn.Dialog.Abc`, а потом сольём их в один файл. Как же быть, когда мы новый элемент 
формы создаётся через web-интерфейс контент-менеджеров? Элемент формы, который использует совершенно уникальный для,
него JS-код который не нужен был до этого момента. Конечно. Ответ один. Весь JS-код для всех типов полей должен
быть подключён всегда. Но как быть, если в нашем фреймворке сотня типов полей? И эта сотня типов использует ещё
около сотни базовых объектов-хелперов, объединяющих функции вроде работы со строками и т.п. Подключать все имеющиеся
в фреймворке JS-объекты не кажется правельным. А отслеживать все эти связи вручную - не лёгкая и адски-рутинная задача.
На помощь приходит Sflm. Он сем отследит связи, подключит только нужное в нужном порядке.

##Какие паттерны парсятся в коде##
Кажде слово с большой буквы

 - Ngn.Asd
 - Ngn.Asd.Asd
 - Ngn.Asd.Asd.Asd

##Как происходит поиск файлов по Валидным Именам##

regexp: [A-Z]*.js

##Немного логов##

Посмотрите на лог вызовов библиотеки, что бы примерно понять как работает автозагрузчик.

Первый пример - пустой вызов. Подключаются базовые библиотеки.
<div class="console">
  <div class="help">
    > <span class="cmd">run "Sflm::$output = true; Sflm::$uploadPath = '/home/user/ngn-env/temp'; Sflm::setFrontendName('a'); Sflm::clearCache(); Sflm::frontend('js')->store();"</span>
  </div>
  Generate frontend [js::a] instance<br>Got package 'core' libs recursive: i/js/mootools/mootools-core-1.4.0.js, i/js/mootools/mootools-more-1.4.0.1.js, i/js/ngn/Ngn.js, s2/js/common/Ngn, i/js/ngn/Ngn.Request.js, i/js/phpFunctions.js<br>js: No new paths. Storing skipped<br>
</div>

В следующем примере происходит добавление всего одного класса `Ngn.Dialog`. Посмотрите какие необходимые библиотеки подключаются в результате:
<div class="console">
  <div class="help">
    > <span class="cmd">run "Sflm::$output = true; Sflm::$uploadPath = '/home/user/ngn-env/temp'; Sflm::setFrontendName('a'); Sflm::clearCache(); Sflm::frontend('js')->addClass('Ngn.Dialog'); Sflm::frontend('js')->store();"</span>
  </div>
  Generate frontend [js::a] instance<br>Got package 'core' libs recursive: i/js/mootools/mootools-core-1.4.0.js, i/js/mootools/mootools-more-1.4.0.1.js, i/js/ngn/Ngn.js, s2/js/common/Ngn, i/js/ngn/Ngn.Request.js, i/js/phpFunctions.js<br><span style="color: #ff0">Add frontend class 'Ngn.Dialog'. src: direct</span><br>Processing contents of 'i/js/ngn/dialog/Ngn.Dialog.js'<br><span style="color: #00C0C0">Adding class 'Ngn.Dialog' (src: i/js/ngn/dialog/Ngn.Dialog.js). PATH i/js/ngn/dialog/Ngn.Dialog.js</span><br><span style="color: aqua">js: Adding path i/js/ngn/dialog/Ngn.Dialog.js</span><br>Processing valid-class patterns in 'i/js/ngn/dialog/Ngn.Dialog.js'<br><span style="color: gray">Class 'Ngn.RequiredOptions' exists. Skipped. src: i/js/ngn/dialog/Ngn.Dialog.js valid-class pattern</span><br><span style="color: #00C0C0">Add frontend class 'Ngn.Dialog.VResize'. src: i/js/ngn/dialog/Ngn.Dialog.js valid-class pattern</span><br>Processing contents of 'i/js/ngn/dialog/Ngn.Dialog.VResize.js'<br><span style="color: #00C0C0">Adding class 'Ngn.Dialog.VResize' (src: i/js/ngn/dialog/Ngn.Dialog.VResize.js). PATH i/js/ngn/dialog/Ngn.Dialog.VResize.js</span><br><span style="color: aqua">js: Adding path i/js/ngn/dialog/Ngn.Dialog.VResize.js</span><br>Processing valid-class patterns in 'i/js/ngn/dialog/Ngn.Dialog.VResize.js'<br><span style="color: gray">Class 'Ngn.Request.JSON' exists. Skipped. src: i/js/ngn/dialog/Ngn.Dialog.js valid-class pattern</span><br><span style="color: gray">Class 'Ngn.Request' exists. Skipped. src: i/js/ngn/dialog/Ngn.Dialog.js valid-class pattern</span><br><span style="color: #00C0C0">Add frontend class 'Ngn.Dotter'. src: i/js/ngn/dialog/Ngn.Dialog.js valid-class pattern</span><br>Processing contents of 'i/js/ngn/core/controls/Ngn.Dotter.js'<br><span style="color: #00C0C0">Adding class 'Ngn.Dotter' (src: i/js/ngn/core/controls/Ngn.Dotter.js). PATH i/js/ngn/core/controls/Ngn.Dotter.js</span><br><span style="color: aqua">js: Adding path i/js/ngn/core/controls/Ngn.Dotter.js</span><br>Processing valid-class patterns in 'i/js/ngn/core/controls/Ngn.Dotter.js'<br>js: Update collected 'a.js' file after adding lib from 'direct' source<br><br>Undefined property: SflmFrontendJs::$abs<br>---------------<br>C:\www\refactor\ngn-env\ngn\core\lib\Err.class.php:37<br>C:\www\refactor\ngn-env\ngn\core\lib\Err.class.php:128<br>C:\www\refactor\ngn-env\ngn\more\lib\sflm\SflmFrontend.class.php:183<br>C:\www\refactor\ngn-env\ngn\more\lib\sflm\SflmFrontend.class.php:169<br>C:\www\refactor\ngn-env\ngn\more\lib\sflm\SflmFrontendJs.class.php:47<br>C:\www\refactor\ngn-env\run\lib\ClRun.class.php(121) : eval()'d code:1<br>C:\www\refactor\ngn-env\run\lib\ClRun.class.php:121<br>C:\www\refactor\ngn-env\run\lib\ClRun.class.php:61<br>C:\www\refactor\ngn-env\run\run.php:20<br><br>
</div>

##Архитектура##
Sflm работает в контексте _sflm-фронтенда_. sflm-фронтенд - это своеобразное хранилище объектов,
которое инкрементально пополняется каждый раз, когда происходит добавление нового объекта.
sflm-фронтенд определяется до создания контроллера. Контроллер - это то место, где начинается
бизнес-логика, а значит и подключения JS-объектов. Так что точкой определения sflm-фронтенда является
роутер. Это то место где вы ещё в силах выбрать sflm-фронтенд.

    // метод Роутера
    protected function init() {
      Sflm::setFrontendName('frontendName');
    }

Далее выполняется вся бизнес-логика от экшенов контроллера до php-кода находящегося в шаблонах. Добавление
JS-объектов может происходить где угодно, но должно закончиться до выполнения метода `SflmFrontendJs::store()`.
Он вызывается в `CtrlBase::getOutput()` уже после вывода.

##Кэширование##

Кэширование происходит на уровне sflm-фронтенда.
Сохранение путей фронтенда осуществляется методом `SflmFrontend::storePaths()`.

Стандартное поведение базового контрллера `CtrlBase` вызывает сохранение путей
при генерации вывода `CtrlBase::getOutput()`.

Это означает, что до этого момента в любых местах кода может быть использован
метод `SflmFrontend::_addPath($path)` для добавления в кэш sflm-фронтенда новых путей.
`SflmFrontend::storePaths()` выполняет сохранение только если добавленный путь ещё не был в кэше.

##Добавление JS-объектов##
Основным и самым удобным методом для добавления объектов является `SflmFrontendJs::addClass()`. Он автоматически ищет
объект или класс во всех каталогах со статическими файлами `Sflm::$absBasePaths`. Для стандартного проекта
это будут каталоги `WEBROOT_PATH.'/m'` и `NGN_PATH.'/u'`. Объектами/классами в Sflm являются файлы формата
`Ngn.asd.Abc`/`Ngn.Asd.Abc`/`Ngn.Asd.dsa.Abc`/ и т.п. Последний кусок (часть имени файла без расширения разбитая точками)
должна начинаться с большой буквы. Объект должен обязательно находиться в неймспейсе `Ngn`. Это обусловлено
использованием в системе других JS-компонентов, подключаемых статически, не отвечающих конвенциям, необходимым
для правильной работы Sflm.

    Sflm::frontend('js')->addClass('Ngn.Name');

##Определения##

###sflm-путь###
sflm-uri - это относительный URI по которому CSS/JS файл можно получить через HTTP. URI указывается относительно базового домена проекта и не должен начинаться со слэша.

###sflm-файл###
sflm-файл - это абсолютный путь к CSS/JS файлу

###sflm-ресурс###
sflm-ресурсом может быть:

- sflm-uri
- sflm-файл

###sflm-библиотека###
sflm-библиотека - это список sflm-путей или других sflm-библиотек.
sflm-библиотека представляет собой `Config Var` находящуюся в подпапке `sfl`.
Т.е. для создания библиотеки с именем "example" нужно создать файл
в одном из базовых ngn-кталогов по пути "config/vars/sfl/example.php" с содержанием:

    <?php

    return [
      'i/js/ngn/Ngn.SomeClass.js'
    ];

###sflm-фронтенд###
sflm-фронтенда - это пространство, хранящее в себе уникальный список
sflm-ресурсов. Оно определяется обычным именем из латинских символов.
Например для всех контроллеров, унаследованных от `CtrlDefault` будет определён
sflm-фронтенд с именем "default". А для всех контроллеров админки `CtrlAdmin` - 
sflm-фронтенд "admin".

По имени sflm-фроентенда задаётся так же название sflm-библиотеки, которая будет всегда
подключатся в первую очереь для него.

##API###
<div class="api" markdown="1"><div class="help">@api</div>- SflmFrontend::__code__()<br><i style='color:#666'>Возвращает код Sflm-фронтенда</i>
- SflmFrontend::__addFolder__($absFolder)<br><i style='color:#666'>Добавляет все файлы в каталоге к Sflm-фронтенду</i>
- SflmFrontend::__addFile__($file)<br><i style='color:#666'>Добавляет файл к Sflm-фронтенду</i>
- SflmFrontend::__store__([$source])<br><i style='color:#666'>Сохраняет все новые пути фронтенда в кэш. После выполнения этого метода в фронтенд уже нельзя добавлять ничего</i>
    - string __source__
</div>


##Подключение зависимостей из JS-файлов##
При объектом подходе в клиентской разработке бывает удобно представлять JS-класс,
как готовый контрол (визуальный компонент). В таких случая кроме JS-кода, нужен ещё и CSS.

##Отладка отдельных файлов##
{tag Отладка CSS/JS файлов}
При работе с JS-библиотекми, собранными через Sflm важно сохранить быстроту разработки и отладки, но при этом не
потерять фишки по автоматическому отслеживанию связей. Ведь одни обеспечивают надёжность сборки компонентов и
уберегают от лишней рутины. Sflm имеет встроенный механизм отладки отдельных файлов на локальном сервере.
При этом важно иметь их первоначальную версию на удаленном dev-сервере, что бы сработало подключение необходимых
в файле компоненотов, средствами Sflm. Что бы начать отладку отдельных файлов используйте следующие статисеские свойства,
переопределим их в `init.php` файле проекта.

Настройки для отладки одного файла с именем `Ngn.Dialog.js`: 

    Sflm::$debugUrl = 'http://localhost:8888';
    Sflm::$debugPaths = [
      'js' => [
        'Ngn.Dialog.js'
      ]
    ];
    
Или так для всей папки:
    
    Sflm::$debugPaths = [
      'js' => [
        'i/ngn/dialog/'
      ]
    ];
    
После этого останется лишь запустить локальный веб-сервер в папке проекта

    php -S localhost:8888
    
##Примеры состояний sflm-фронтенда##
Текущее содержание sflm-фронтенда можно посмотреть через командную строку. Для этого
существует cli-утилита `sflm`.

<div class="console">
  <div class="help">
    > <span class="cmd">sflm docdemo</span>
  </div>
  
</div>
<div class="console">
  <div class="help">
    > <span class="cmd">sflm docdemo paths default css</span>
  </div>
  
</div>
<div class="console">
  <div class="help">
    > <span class="cmd">sflm docdemo paths default js</span>
  </div>
  
</div>
