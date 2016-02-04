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
  <div class="help" title="Команда, выдающая текст в рамке">
    <span class="blink">></span> <span class="cmd">run "Sflm::$output = true; Sflm::setFrontendName('a'); Sflm::clearCache(); Sflm::frontend('js')->store();"</span>
  </div>
  &lt;Generate frontend [js::a] instance&gt;<br>&lt;Package 'core' is empty&gt;<br>&lt;js: Update collected 'a.js' file after adding lib from 'root' source&gt;<br>&lt;Package 'a' (sfl/js/a) does not exists&gt;<br>&lt;Storing existing objects. Nothing to store. Skipped&gt;<br>
</div>

В следующем примере происходит добавление всего одного класса `Ngn.Dialog`. Посмотрите какие необходимые библиотеки подключаются в результате:
<div class="console">
  <div class="help" title="Команда, выдающая текст в рамке">
    <span class="blink">></span> <span class="cmd">run "Sflm::$output = true; Sflm::setFrontendName('a'); Sflm::clearCache(); Sflm::frontend('js')->addClass('Ngn.Dialog'); Sflm::frontend('js')->store();"</span>
  </div>
  &lt;Generate frontend [js::a] instance&gt;<br>&lt;Package 'core' is empty&gt;<br>&lt;Add frontend class 'Ngn.Dialog'. src: root&gt;<br>&lt;Processing contents of 'i/js/ngn/dialog/Ngn.Dialog.js'&gt;<br>&lt;Add frontend class 'Ngn.RequiredOptions'. src: Ngn.Dialog preload&gt;<br>&lt;Processing contents of 'i/js/ngn/core/Ngn.RequiredOptions.js'&gt;<br>&lt;Adding class 'Ngn.RequiredOptions' (src: i/js/ngn/core/Ngn.RequiredOptions.js). PATH i/js/ngn/core/Ngn.RequiredOptions.js&gt;<br>&lt;js: Adding path i/js/ngn/core/Ngn.RequiredOptions.js&gt;<br>&lt;Processing valid-class patterns in 'i/js/ngn/core/Ngn.RequiredOptions.js'&gt;<br>&lt;Adding class 'Ngn.Dialog' (src: i/js/ngn/dialog/Ngn.Dialog.js). PATH i/js/ngn/dialog/Ngn.Dialog.js&gt;<br>&lt;js: Adding path i/js/ngn/dialog/Ngn.Dialog.js&gt;<br>&lt;Processing valid-class patterns in 'i/js/ngn/dialog/Ngn.Dialog.js'&gt;<br>&lt;Class 'Ngn.RequiredOptions' exists. Skipped. src: i/js/ngn/dialog/Ngn.Dialog.js valid-class pattern&gt;<br>&lt;Add frontend class 'Ngn.String'. src: i/js/ngn/dialog/Ngn.Dialog.js valid-class pattern&gt;<br>&lt;Processing contents of 'i/js/ngn/core/Ngn.String.js'&gt;<br>&lt;Adding class 'Ngn.String' (src: i/js/ngn/core/Ngn.String.js). PATH i/js/ngn/core/Ngn.String.js&gt;<br>&lt;js: Adding path i/js/ngn/core/Ngn.String.js&gt;<br>&lt;Processing valid-class patterns in 'i/js/ngn/core/Ngn.String.js'&gt;<br>&lt;Add frontend class 'Ngn.Number'. src: i/js/ngn/core/Ngn.String.js valid-class pattern&gt;<br>&lt;Processing contents of 'i/js/ngn/core/Ngn.Number.js'&gt;<br>&lt;Adding class 'Ngn.Number' (src: i/js/ngn/core/Ngn.Number.js). PATH i/js/ngn/core/Ngn.Number.js&gt;<br>&lt;js: Adding path i/js/ngn/core/Ngn.Number.js&gt;<br>&lt;Processing valid-class patterns in 'i/js/ngn/core/Ngn.Number.js'&gt;<br>&lt;Add frontend class 'Ngn.Dialog.VResize'. src: i/js/ngn/dialog/Ngn.Dialog.js valid-class pattern&gt;<br>&lt;Processing contents of 'i/js/ngn/dialog/Ngn.Dialog.VResize.js'&gt;<br>&lt;Adding class 'Ngn.Dialog.VResize' (src: i/js/ngn/dialog/Ngn.Dialog.VResize.js). PATH i/js/ngn/dialog/Ngn.Dialog.VResize.js&gt;<br>&lt;js: Adding path i/js/ngn/dialog/Ngn.Dialog.VResize.js&gt;<br>&lt;Processing valid-class patterns in 'i/js/ngn/dialog/Ngn.Dialog.VResize.js'&gt;<br>&lt;Add frontend class 'Ngn.Element'. src: i/js/ngn/dialog/Ngn.Dialog.VResize.js valid-class pattern&gt;<br>&lt;Processing contents of 'i/js/ngn/core/Ngn.Element.js'&gt;<br>&lt;Adding class 'Ngn.Element' (src: i/js/ngn/core/Ngn.Element.js). PATH i/js/ngn/core/Ngn.Element.js&gt;<br>&lt;js: Adding path i/js/ngn/core/Ngn.Element.js&gt;<br>&lt;Processing valid-class patterns in 'i/js/ngn/core/Ngn.Element.js'&gt;<br>&lt;Add frontend class 'Ngn.Storage'. src: i/js/ngn/dialog/Ngn.Dialog.VResize.js valid-class pattern&gt;<br>&lt;Processing contents of 'i/js/ngn/core/Ngn.Storage.js'&gt;<br>&lt;Adding class 'Ngn.Storage' (src: i/js/ngn/core/Ngn.Storage.js). PATH i/js/ngn/core/Ngn.Storage.js&gt;<br>&lt;js: Adding path i/js/ngn/core/Ngn.Storage.js&gt;<br>&lt;Processing valid-class patterns in 'i/js/ngn/core/Ngn.Storage.js'&gt;<br>&lt;Add frontend class 'Ngn.LocalStorage'. src: i/js/ngn/core/Ngn.Storage.js valid-class pattern&gt;<br>&lt;Processing contents of 'i/js/ngn/core/Ngn.LocalStorage.js'&gt;<br>&lt;Adding class 'Ngn.LocalStorage' (src: i/js/ngn/core/Ngn.LocalStorage.js). PATH i/js/ngn/core/Ngn.LocalStorage.js&gt;<br>&lt;js: Adding path i/js/ngn/core/Ngn.LocalStorage.js&gt;<br>&lt;Processing valid-class patterns in 'i/js/ngn/core/Ngn.LocalStorage.js'&gt;<br>&lt;Add frontend class 'Ngn.Request.JSON'. src: i/js/ngn/dialog/Ngn.Dialog.js valid-class pattern&gt;<br>&lt;Add frontend class 'Ngn.Request'. src: Ngn.Request.JSON parent namespace&gt;<br>&lt;Processing contents of 'i/js/ngn/Ngn.Request.js'&gt;<br>&lt;Add frontend class 'Ngn.Request.Loading'. src: i/js/ngn/Ngn.Request.js&gt;<br>&lt;Add frontend class 'Ngn.Request.Iface'. src: i/js/ngn/Ngn.Request.js&gt;<br>&lt;Adding class 'Ngn.Request' (src: i/js/ngn/Ngn.Request.js). PATH i/js/ngn/Ngn.Request.js&gt;<br>&lt;js: Adding path i/js/ngn/Ngn.Request.js&gt;<br>&lt;Processing valid-class patterns in 'i/js/ngn/Ngn.Request.js'&gt;<br>&lt;Class 'Ngn.String' exists. Skipped. src: i/js/ngn/Ngn.Request.js valid-class pattern&gt;<br>&lt;Add frontend class 'Ngn.Arr'. src: i/js/ngn/Ngn.Request.js valid-class pattern&gt;<br>&lt;Processing contents of 'i/js/ngn/core/Ngn.Arr.js'&gt;<br>&lt;Adding class 'Ngn.Arr' (src: i/js/ngn/core/Ngn.Arr.js). PATH i/js/ngn/core/Ngn.Arr.js&gt;<br>&lt;js: Adding path i/js/ngn/core/Ngn.Arr.js&gt;<br>&lt;Processing valid-class patterns in 'i/js/ngn/core/Ngn.Arr.js'&gt;<br>&lt;Path 'i/js/ngn/Ngn.Request.js' in cache. Skipped&gt;<br>&lt;Class 'Ngn.Request' exists. Skipped. src: i/js/ngn/dialog/Ngn.Dialog.js valid-class pattern&gt;<br>&lt;Add frontend class 'Ngn.Btn'. src: i/js/ngn/dialog/Ngn.Dialog.js valid-class pattern&gt;<br>&lt;Processing contents of 'i/js/ngn/core/controls/Ngn.Btn.js'&gt;<br>&lt;Add frontend class 'Ngn.Btn.Action'. src: i/js/ngn/core/controls/Ngn.Btn.js&gt;<br>&lt;Add frontend class 'Ngn.Btn.FileUpload'. src: i/js/ngn/core/controls/Ngn.Btn.js&gt;<br>&lt;Adding class 'Ngn.Btn' (src: i/js/ngn/core/controls/Ngn.Btn.js). PATH i/js/ngn/core/controls/Ngn.Btn.js&gt;<br>&lt;js: Adding path i/js/ngn/core/controls/Ngn.Btn.js&gt;<br>&lt;Processing valid-class patterns in 'i/js/ngn/core/controls/Ngn.Btn.js'&gt;<br>&lt;Add frontend class 'Ngn.Dialog.Confirm.Mem'. src: i/js/ngn/core/controls/Ngn.Btn.js valid-class pattern&gt;<br>&lt;Add frontend class 'Ngn.Dialog.Confirm'. src: Ngn.Dialog.Confirm.Mem parent namespace&gt;<br>&lt;Processing contents of 'i/js/ngn/dialog/Ngn.Dialog.Confirm.js'&gt;<br>&lt;Add frontend class 'Ngn.Dialog.Msg'. src: Ngn.Dialog.Confirm preload&gt;<br>&lt;Processing contents of 'i/js/ngn/dialog/Ngn.Dialog.Msg.js'&gt;<br>&lt;Class 'Ngn.Dialog' exists. Skipped. src: Ngn.Dialog.Msg preload&gt;<br>&lt;Adding class 'Ngn.Dialog.Msg' (src: i/js/ngn/dialog/Ngn.Dialog.Msg.js). PATH i/js/ngn/dialog/Ngn.Dialog.Msg.js&gt;<br>&lt;js: Adding path i/js/ngn/dialog/Ngn.Dialog.Msg.js&gt;<br>&lt;Processing valid-class patterns in 'i/js/ngn/dialog/Ngn.Dialog.Msg.js'&gt;<br>&lt;Class 'Ngn.Dialog' exists. Skipped. src: i/js/ngn/dialog/Ngn.Dialog.Msg.js valid-class pattern&gt;<br>&lt;Adding class 'Ngn.Dialog.Confirm' (src: i/js/ngn/dialog/Ngn.Dialog.Confirm.js). PATH i/js/ngn/dialog/Ngn.Dialog.Confirm.js&gt;<br>&lt;js: Adding path i/js/ngn/dialog/Ngn.Dialog.Confirm.js&gt;<br>&lt;Processing valid-class patterns in 'i/js/ngn/dialog/Ngn.Dialog.Confirm.js'&gt;<br>&lt;Class 'Ngn.Dialog.Msg' exists. Skipped. src: i/js/ngn/dialog/Ngn.Dialog.Confirm.js valid-class pattern&gt;<br>&lt;Processing contents of 'i/js/ngn/dialog/Ngn.Dialog.Confirm.Mem.js'&gt;<br>&lt;Class 'Ngn.Dialog.Confirm' exists. Skipped. src: Ngn.Dialog.Confirm.Mem preload&gt;<br>&lt;Adding class 'Ngn.Dialog.Confirm.Mem' (src: i/js/ngn/dialog/Ngn.Dialog.Confirm.Mem.js). PATH i/js/ngn/dialog/Ngn.Dialog.Confirm.Mem.js&gt;<br>&lt;js: Adding path i/js/ngn/dialog/Ngn.Dialog.Confirm.Mem.js&gt;<br>&lt;Processing valid-class patterns in 'i/js/ngn/dialog/Ngn.Dialog.Confirm.Mem.js'&gt;<br>&lt;Class 'Ngn.Dialog.Confirm' exists. Skipped. src: i/js/ngn/dialog/Ngn.Dialog.Confirm.Mem.js valid-class pattern&gt;<br>&lt;Class 'Ngn.Storage' exists. Skipped. src: i/js/ngn/dialog/Ngn.Dialog.Confirm.Mem.js valid-class pattern&gt;<br>&lt;Class 'Ngn.Element' exists. Skipped. src: i/js/ngn/core/controls/Ngn.Btn.js valid-class pattern&gt;<br>&lt;Class 'Ngn.Request' exists. Skipped. src: i/js/ngn/core/controls/Ngn.Btn.js valid-class pattern&gt;<br>&lt;Add frontend class 'Ngn.Request.File'. src: i/js/ngn/core/controls/Ngn.Btn.js valid-class pattern&gt;<br>&lt;Processing contents of 'i/js/ngn/form/Ngn.Request.File.js'&gt;<br>&lt;Class 'Ngn.Request.JSON' exists. Skipped. src: Ngn.Request.File preload&gt;<br>&lt;Adding class 'Ngn.Request.File' (src: i/js/ngn/form/Ngn.Request.File.js). PATH i/js/ngn/form/Ngn.Request.File.js&gt;<br>&lt;js: Adding path i/js/ngn/form/Ngn.Request.File.js&gt;<br>&lt;Processing valid-class patterns in 'i/js/ngn/form/Ngn.Request.File.js'&gt;<br>&lt;Class 'Ngn.Request.JSON' exists. Skipped. src: i/js/ngn/form/Ngn.Request.File.js valid-class pattern&gt;<br>&lt;Class 'Ngn.String' exists. Skipped. src: i/js/ngn/form/Ngn.Request.File.js valid-class pattern&gt;<br>&lt;Class 'Ngn.Request' exists. Skipped. src: i/js/ngn/form/Ngn.Request.File.js valid-class pattern&gt;<br>&lt;Add frontend class 'Ngn.Frm'. src: i/js/ngn/core/controls/Ngn.Btn.js requiredAfter&gt;<br>&lt;Processing contents of 'i/js/ngn/form/Ngn.Frm.js'&gt;<br>&lt;Adding class 'Ngn.Frm' (src: i/js/ngn/form/Ngn.Frm.js). PATH i/js/ngn/form/Ngn.Frm.js&gt;<br>&lt;js: Adding path i/js/ngn/form/Ngn.Frm.js&gt;<br>&lt;Processing valid-class patterns in 'i/js/ngn/form/Ngn.Frm.js'&gt;<br>&lt;Class 'Ngn.Storage' exists. Skipped. src: i/js/ngn/form/Ngn.Frm.js valid-class pattern&gt;<br>&lt;Add frontend class 'Ngn.Dotter'. src: i/js/ngn/dialog/Ngn.Dialog.js valid-class pattern&gt;<br>&lt;Processing contents of 'i/js/ngn/core/controls/Ngn.Dotter.js'&gt;<br>&lt;Adding class 'Ngn.Dotter' (src: i/js/ngn/core/controls/Ngn.Dotter.js). PATH i/js/ngn/core/controls/Ngn.Dotter.js&gt;<br>&lt;js: Adding path i/js/ngn/core/controls/Ngn.Dotter.js&gt;<br>&lt;Processing valid-class patterns in 'i/js/ngn/core/controls/Ngn.Dotter.js'&gt;<br>&lt;Class 'Ngn.Storage' exists. Skipped. src: i/js/ngn/dialog/Ngn.Dialog.js valid-class pattern&gt;<br>&lt;js: Update collected 'a.js' file after adding lib from 'root' source&gt;<br>&lt;Mt: adding &quot;Core&quot;, src: Array&gt;<br>&lt;Mt: adding &quot;Array&quot;, src: Browser&gt;<br>&lt;Mt: adding &quot;Function&quot;, src: Browser&gt;<br>&lt;Mt: adding &quot;Number&quot;, src: Browser&gt;<br>&lt;Mt: adding &quot;String&quot;, src: Browser&gt;<br>&lt;Mt: adding &quot;Browser&quot;, src: Element&gt;<br>&lt;Mt: adding &quot;Object&quot;, src: Element&gt;<br>&lt;Mt: adding &quot;Slick.Parser&quot;, src: Element&gt;<br>&lt;Mt: adding &quot;Slick.Finder&quot;, src: Element&gt;<br>&lt;Mt: adding &quot;Element&quot;, src: Element.Event&gt;<br>&lt;Mt: adding &quot;Event&quot;, src: Element.Event&gt;<br>&lt;Mt: adding &quot;Element.Event&quot;, src: root&gt;<br>&lt;Mt: adding &quot;Class&quot;, src: Class.Extras&gt;<br>&lt;Mt: adding &quot;Class.Extras&quot;, src: root&gt;<br>&lt;Mt: adding &quot;Fx&quot;, src: root&gt;<br>&lt;Mt: adding &quot;Element.Style&quot;, src: Fx.CSS&gt;<br>&lt;Mt: adding &quot;Fx.CSS&quot;, src: Fx.Morph&gt;<br>&lt;Mt: adding &quot;Fx.Morph&quot;, src: root&gt;<br>&lt;Mt: adding &quot;Fx.Tween&quot;, src: root&gt;<br>&lt;Mt: adding &quot;Request&quot;, src: root&gt;<br>&lt;Mt: adding &quot;JSON&quot;, src: Request.JSON&gt;<br>&lt;Mt: adding &quot;Request.JSON&quot;, src: root&gt;<br>&lt;Mt: adding &quot;More&quot;, src: Hash&gt;<br>&lt;Mt: adding &quot;Hash&quot;, src: root&gt;<br>&lt;Mt: adding &quot;Cookie&quot;, src: root&gt;<br>&lt;Mt: adding &quot;Element.Dimensions&quot;, src: Drag&gt;<br>&lt;Mt: adding &quot;Drag&quot;, src: root&gt;<br>&lt;Mt: adding &quot;Drag.Move&quot;, src: root&gt;<br>&lt;Mt: adding &quot;Elements.From&quot;, src: root&gt;<br>&lt;Mt: adding &quot;Tips&quot;, src: root&gt;<br>&lt;Mt: adding &quot;Asset&quot;, src: root&gt;<br>
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

<p class="important" markdown="1">Используйте константу BUILD_MODE что бы включить режим runtime-сборки в базовом контроллере `CtrlBase`.</p>

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
Коллекцианирует и кэширует пути к статическим файлам во время выполнения приложения<pre><code class="php">// Возвращает код Sflm-фронтенда
SflmFrontend::</code></pre><div></div><pre><code class="php">// Добавляет все файлы в каталоге к Sflm-фронтенду
SflmFrontend::</code></pre><div></div><pre><code class="php">// Добавляет файл к Sflm-фронтенду
SflmFrontend::</code></pre><div></div><pre><code class="php">// Сохраняет все новые пути кэш данных и создаёт веб-кэш. После выполнения этого метода в фронтенд уже нельзя добавлять ничего
SflmFrontend::</code></pre><div class="apiParams"><p><span class="varType">string</span> <b>$source</b></p></div>


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

<p class="important" markdown="1"> После добавления путей в Sflm::$debugPaths, необходимо очистить кэш приложения</p>
    
##Примеры состояний sflm-фронтенда##
Текущее содержание sflm-фронтенда можно посмотреть через командную строку. Для этого
существует cli-утилита `sflm`.

<div class="console">
  <div class="help" title="Команда, выдающая текст в рамке">
    <span class="blink">></span> <span class="cmd">sflm docdemo</span>
  </div>
  
</div>
<div class="console">
  <div class="help" title="Команда, выдающая текст в рамке">
    <span class="blink">></span> <span class="cmd">sflm docdemo paths default css</span>
  </div>
  
</div>
<div class="console">
  <div class="help" title="Команда, выдающая текст в рамке">
    <span class="blink">></span> <span class="cmd">sflm docdemo paths default js</span>
  </div>
  
</div>
