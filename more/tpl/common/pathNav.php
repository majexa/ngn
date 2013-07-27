<div id="pathNav">
  <? if ($d['contentButtons']) { ?>
    <div class="btns">
      <div class="smIcons bordered last"><?= $d['editPageBlock'] ?></div>
    </div>
  <? } ?>
  <? $this->tpl('common/path', $d['pathData']) ?>
</div>
