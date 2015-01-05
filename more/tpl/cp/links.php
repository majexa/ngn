<? foreach ($d as $v) { ?>
  <? if ($v['separator']) { ?>
    <div class="separaor"></div>
  <? } else { ?>
    <a href="<?= $v['link'] ?>" class="<?= $v['class'].((!empty($v['sel'])) ? ' sel' : '').(empty($v['descr']) ? '' : ' tooltip') ?>"<?= isset($v['target']) ? ' target="'.$v['target'].'"' : '' ?><?= isset($v['descr']) ? ' title="'.$v['descr'].'"' : '' ?>
><i></i><?= $v['title'] ?></a>
  <? } ?>
<? } ?>
