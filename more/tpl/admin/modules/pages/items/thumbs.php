<?php 

/**
 * @todo Нужно использовать это только для модуля "Звук"
 */
foreach (SoundStat::getTotalTracksTime(
$d['page']['strName'], array_keys($d['items'])) as $itemId => $v) {
  $d['items'][$itemId]['soundListenTime'] = $v['sec'];
}

/* @var $oDdoFields DdoFields */
$oDdo = O::get('DdoPage',
  $d['page'], 'adminItems'
)->setItems($d['items']);

$oDdo->ddddByName['title'] = '`<h2>`.$v.`</h2>`';
$oDdo->ddddByType['author'] = '`<a href="`.$this->getPath(1).`/users/?a=edit&id=`.$v[`id`].`">`.$v[`login`].`</a>`';
$oDdo->ddddByType['image'] = '$v ? `<a href="`.Misc::getFilePrefexedPath($v, `md_`).`" target="_blank" class="thumb" rel="ngnLightbox[set1]"><img src="`.Misc::getFilePrefexedPath($v, `sm_`).`" /></a>` : ``';
$oDdo->ddddByType['tagsMultiselect'] = 
  '$this->enumDddd($v, `<a href="`.$this->getPath(4).`/t2.$groupName.$name">$title</a>`, `, `)';
$oDdo->ddddByType['tagsSelect'] =
  '`<a href="`.$this->getPath(4).`/t2.`.$v[`groupName`].`.`.$v[`name`].`">`.$v[`title`].`</a>`';
$oDdo->ddddByType['bool'] = '`<a href="" class="iconBtn flag flag`.($v ? `On` : `Off`).` tooltip" title="`.$name.`"><i></i></a>`';

$oDdo->ddddByType['sound'] = $oDdo->ddddByType['sound'].
  '.($o->items[$id][`soundListenTime`] ? '.
  '`<span class="soundListenTime tooltip" title="Общее время прослушивания трека">`.round($o->items[$id][`soundListenTime`]/60).` мин.</span>'.
  '<a href="`.$this->getPath(4).`/soundStat/`.$o->strName.`/`.$id.`" class="soundStat smIcons stat gray"><i></i>Статистика</a>` : ``)';

$oDdo->setElementsData(
  O::get('DdoFields', $d['page']['strName'], 'adminItems')->getFields());

?>