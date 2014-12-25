<div class="tagsTreeSelect" id="tagsTreeMultiselect_<?= Misc::name2id($d['name']) ?>"<?= Html::dataParams($d['dataParams']) ?>>
  <? $this->tpl('dd/tagsTreeMultiselectInner', $d) ?>
</div>
