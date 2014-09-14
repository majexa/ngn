<? $d['page'] = DbModelCOre::get('pages', $_REQUEST['pageId']) ?>

<? if (isset($d['page']['settings']['mozaicW'])) { ?>
.ddItems.str_<?= $d['page']['strName'] ?> .thumb img {
width: <?= $d['page']['settings']['mozaicW'] ?>px;
height: <?= $d['page']['settings']['mozaicH'] ?>px;
}
.ddItems.str_<?= $d['page']['strName'] ?> .item {
width: <?= $d['page']['settings']['mozaicW']+15 ?>px;
}
<? } elseif ($d['page']['settings']['smW']) { ?>
.ddItems.str_<?= $d['page']['strName'] ?> .thumb img {
max-width: <?= $d['page']['settings']['smW'] ?>px;
max-height: <?= $d['page']['settings']['smH'] ?>px;
}
.ddItems.str_<?= $d['page']['strName'] ?> .thumb.halfSize img {
max-width: <?= round($d['page']['settings']['smW']/2) ?>px;
max-height: <?= round($d['page']['settings']['smH']/2) ?>px;
}
.ddItems.str_<?= $d['page']['strName'] ?>.tile .item {
width: <?= $d['page']['settings']['smW'] ?>px;
}
<? } ?>