<div class="path dgray">
  <? if (count(params())) { ?>
    <?= $this->enumDddd($d, '`<a href="`.$link.`">`.$title.`</a>`', '/') ?>
  <? }
  else { ?>
    &nbsp;
  <? } ?>
</div>
