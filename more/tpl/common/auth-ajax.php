<? $this->tpl('common/dialogFormTabs', $d) ?>

<? if (Config::getVarVar('userReg', 'vkAuthEnable')) { ?>
<h2 class="tab" title="Войти с помощью «Вконтакте»" data-name="vk">
  <img src="/i/img/icons/vk.png" />
</h2>
<div id="vkAuth"></div>
<? } ?>
