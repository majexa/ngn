<?php

return [
  'numberRange' => '($v[`from`] or $v[`to`]) ? $v[`from`].` — `.$v[`to`] : ``',
  //'text' => 'Misc::cut($v, 100)',
  'wisiwig' => 'Misc::cut($v, 100)',
  'wisiwigSimple' => 'Misc::cut($v, 100)',
  'typoTextarea' => 'nl2br($v)',
  'header' => '',
  'bool' => '$v ? `да` : `нет`',
  'urls' => '`<ul>`.urls($v, "\n").`</ul>`',
  'file' => '$v ? `<a href="`.$v.`" />Скачать (`.File::format2($fSize).`)</a>` : ``',
  'fieldList' => 'Tt()->enum($v, `, `, `($k+1).") $v"`)',
  'imagePreview' => '$v ? `<div class="thumbCont"><a href="`.$v.`" class="thumb" target="_blank"><img src="`.Misc::getFilePrefexedPath($v, `sm_`).`" /></a></div>` : `<div class="thumbCont"></div>`',
  'ddUserImage' => '%imagePreview',
  'ddTagsMultiselect' => '$v ? Tt()->enumDddd($v, `$title`) : ``',
  //'ddTagsSelect' => '$v ? `<a href="`.Tt()->getPath(0).$pagePath.`/t2.`.$v[`groupName`].`.`.$v[`id`].`" class="dgray">`.$v[`title`].`</a>` : ``',
  'ddTagsSelect' => '$v ? $v[`title`] : ``',
  'ddTagsSelectName' => '%ddTagsSelect',
  'ddTagsTreeSelect' => '`<span class="dgray"><b class="title">`.$title.`:</b> `.DdTagsHtml::treeArrowsLinks(array(`pagePath` => $pagePath, `tags` => $v)).`</span>`', // выводим только последний
  'ddTagsConsecutiveSelect' => 'DdTagsHtml::treeArrowsLinks(array(`pagePath` => $pagePath, `tags` => $v))',
  //'ddTagsTreeMultiselect' => '`<ul>`.DdTagsHtml::treeArrows3(array(`pagePath` => $pagePath, `tags` => $v)).`</ul>`',
  'ddTagsTreeMultiselect' => '$v ? Tt()->enum($v, ``, `"<div><small style=\\`white-space:nowrap\\`>&bull; ".TreeCommon::lastInBranch($v)["title"]."</small></div>"`) : ``',
  //'ddTagsTreeMultiselect' => '$v ? Tt()->enum($v, ``, getPrr()) : ``',
  //'ddTagsTreeMultiselect' => 'getPrr($v)',
  //'ddTagsTreeMultiselect' => '`dummy`',
  'ddCityMultiselect' => '%ddTagsTreeMultiselect',
  'ddCity' => '$v ? DdTagsHtml::tagsTreeArrowsNode(Arr::first($v)) : ``',
  //'ddCity' => '`CITY STRIPPED`',
  'ddCityRussia' => '%ddCity',
  //'ddCity' => 'getPrr($v)',
  'date' => 'date_reformat($v, `d.m.Y`, `Y-m-d`)',
  'datetime' => '$v ? datetimeStrSql($v) : ``',
  'birthDate' => '$v ? ageFromBirthDate(date_reformat($v, `d.m.Y`, `Y-m-d`)).` лет` : ``',
  'select' => '`<a href="`.Tt()->getPath(0).$pagePath.`/v.`.$name.`.`.$v[`k`].`">`.$v[`v`].`</a>`',
  'radio' => '`<a href="`.Tt()->getPath(0).$pagePath.`/v.`.$name.`.`.$v[`k`].`">`.$v[`v`].`</a>`',
  'author' => '`<a href="`.Tt()->getUserPath($authorId).`" class="dgray">`.$authorLogin.`</a>`',
  'static' => '$title',
  //'user' => '`<a href="`.Tt()->getUserPath($authorId).`">`.$authorLogin.`</a>`',
  'user' => '$v ? UsersCore::getTitle($v) : ``',
  //'author' => '%user',
  'authorId' => '`SUCK!`',
  'procent' => '$v ? $v.`%` : ``',
  'price' => '$v ? Misc::formatPrice($v).` Ᵽ` : ``',
  'phone' => '$v ? Misc::parsePhone($v) : ``',
  'icq' => '$v ? `<span><img src="http://status.icq.com/online.gif?icq=`.$v.`&img=5" alt="Статус ICQ" class="icon18" /></span>`.$v : ``',
  'skype' => '$v ? `<a href="skype:`.$v.`?call" class="dgray"><img src="/i/img/icons/skype.gif" class="icon18" />`.$v.`</a>` : ``',
  'url' => '$v ? `<a href="`.$v.`" target="_blank" class="dgray"><img src="http://www.google.com/s2/favicons?domain=`.Misc::getHost($v).`" class="icon18" />`.Misc::cut(clearUrl($v), 22).`</a>` : ``',
  'ddItemSelect' => '$v ? $v[`title`] : ``',
  'ddItemsSelect' => '%ddTagsMultiselect',
  'ddSlaveItemsSelect' => '%ddItemSelect',
  'configSelect' => '$v ? Config::getVar(`fieldE/`.$name)[$v] : ``',
  'domain' => '`<a href="http://`.$v.`" target="_blank">`.$v.`</a>`',
  'video' => '`
<div id="video_`.$id.`"></div>
<script type="text/javascript">
Ngn.video({
"container": $("video_`.$id.`"),
"width": 320,
"height": 240
},{
"file": "../../../`.str_replace(`./`, ``, $v).`",
"image": "../../../`.str_replace(`./`, `/`, File::reext($v, `jpg`)).`",
"provider": "http"
});
</script>
`',
  'flash' => '
    Tt()->getTpl(`common/flash`, array(
      `id` => `flash`.$id,
      `path` => UPLOAD_DIR.`/`.$v[`path`],
      `width` => $v[`w`],
      `height` => $v[`h`]
    ))
',
  'sound' => 'empty($v) ? `` : `<b class="title">`.$title.`:</b> <div class="mp3player">`.Tt()->getTpl(`common/mp3player`, array(`file` => $v)).`</div><div class="mp3download iconsSet"><a href="`.$v.`" class="dgray file"><i></i>Скачать (`.File::format2($fSize).`)</a></div><div class="clear"><!-- --></div>`',
  /*
  'sound' => 'empty($v) ? `` : Tt()->getTpl(`common/mooSound`, array(
      `id` => `sound`.$id,
      `path` => `./i/swf/mp/player.swf`,
      `width` => 200,
      `height` => 20,
      `flashvars` => array(
        `file` => `../../../`.str_replace(`./`, ``, $v)
      ),
      `strName` => $o->strName,
      `itemId` => $id
    )).`<p><a href="`.$v.`" class="dgray">Скачать (`.File::format2($fSize).`)</a></p>`'
  */
];