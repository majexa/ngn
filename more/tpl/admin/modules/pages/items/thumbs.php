<?php 

/**
 * @todo Нужно использовать это только для модуля "Звук"
 */
foreach (SoundStat::getTotalTracksTime(
$d['page']['strName'], array_keys($d['items'])) as $itemId => $v) {
  $d['items'][$itemId]['soundListenTime'] = $v['sec'];
}

/* @var $ddoFields DdoFields */
$ddo = O::get('DdoPage',
  $d['page'], 'adminItems'
)->setItems($d['items']);

$ddo->ddddByName['title'] = '`<h2>`.$v.`</h2>`';
$ddo->ddddByType['author'] = '`<a href="`.$this->getPath(1).`/users/?a=edit&id=`.$v[`id`].`">`.$v[`login`].`</a>`';
$ddo->ddddByType['image'] = '$v ? `<a href="`.Misc::getFilePrefexedPath($v, `md_`).`" target="_blank" class="thumb" rel="ngnLightbox[set1]"><img src="`.Misc::getFilePrefexedPath($v, `sm_`).`" /></a>` : ``';
$ddo->ddddByType['tagsMultiselect'] =
  '$this->enumDddd($v, `<a href="`.$this->getPath(4).`/t2.$groupName.$name">$title</a>`, `, `)';
$ddo->ddddByType['tagsSelect'] =
  '`<a href="`.$this->getPath(4).`/t2.`.$v[`groupName`].`.`.$v[`name`].`">`.$v[`title`].`</a>`';
$ddo->ddddByType['bool'] = '`<a href="" class="iconBtn flag flag`.($v ? `On` : `Off`).` tooltip" title="`.$name.`"><i></i></a>`';

$ddo->ddddByType['sound'] = $ddo->ddddByType['sound'].
  '.($o->items[$id][`soundListenTime`] ? '.
  '`<span class="soundListenTime tooltip" title="Общее время прослушивания трека">`.round($o->items[$id][`soundListenTime`]/60).` мин.</span>'.
  '<a href="`.$this->getPath(4).`/soundStat/`.$o->strName.`/`.$id.`" class="soundStat smIcons stat gray"><i></i>Статистика</a>` : ``)';

$ddo->setElementsData(
  O::get('DdoFields', $d['page']['strName'], 'adminItems')->getFields());

?>