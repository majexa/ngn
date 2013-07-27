<div id="path" class="dgray">
  <? if (count(params())) { ?>
    <?= $this->enumDddd($d, '`<a href="`.$link.`">`.$title.`</a>`',
      Config::getVarVar('pages', 'pathSeparator')) ?>
  <? } else { ?>
    &nbsp;
  <? } ?>
</div>
