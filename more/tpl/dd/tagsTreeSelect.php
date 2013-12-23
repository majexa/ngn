<div class="tagsTreeSelect" id="tagsTreeSelect_<?= $d['name'] ?>"
  <?= ($d['required'] ? ' class="validate-one-required"' : '') ?>>
  <?=
  $this->getDbTree( //
    $d['tree'], //
    '`<li>↓ <a href="#" data-id="`.$id.`" class="pseudoLink">`.$title.`</a></li>`', //
    '`<li><input type="radio" name="'.$d['name'].'" value="`.$id.`" id="f_'.$d['name'].'_`.$id.`"`.($id == $value ? ` checked` : ``).`'.' /><label for="f_'.$d['name'].'_`.$id.`">`.$title.`</label></li>`', //
    '`<ul class="nodes_`.$id.`">`', //
    '`</ul>`', //
    ['value' => $d['value']] //
  ) ?>
</div>