<? foreach ($d['forms'] as $v) { ?>
<h2 class="tab" data-id="<?= $v['id'] ?>"><?= $v['title'] ?></h2>
<div data-submitTitle="<?= $v['submitTitle'] ?>">
  <?= $v['html'] ?>
  <div class="clear"><!-- --></div>
</div>
<? } ?>