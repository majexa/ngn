<? $d['page'] = DbModelCOre::get('pages', $_REQUEST['pageId']) ?>

<? if (isset($d['page']['settings']['mozaicW'])) { ?>
.str_<?= $d['page']['strName'] ?>.ddItems .thumb img {
width: <?= $d['page']['settings']['mozaicW'] ?>px;
height: <?= $d['page']['settings']['mozaicH'] ?>px;
}
.str_<?= $d['page']['strName'] ?>.ddItems .item {
width: <?= $d['page']['settings']['mozaicW']+15 ?>px;
}
<? } elseif ($d['page']['settings']['smW']) { ?>
.str_<?= $d['page']['strName'] ?>.ddItems .thumb img {
max-width: <?= $d['page']['settings']['smW'] ?>px;
max-height: <?= $d['page']['settings']['smH'] ?>px;
}
.str_<?= $d['page']['strName'] ?>.ddItems .thumb.halfSize img {
max-width: <?= round($d['page']['settings']['smW']/2) ?>px;
max-height: <?= round($d['page']['settings']['smH']/2) ?>px;
}
.ddil_tile .str_<?= $d['page']['strName'] ?>.ddItems .item {
width: <?= ($d['page']['settings']['smW']+4) ?>px;
}
<? } ?>