<? $this->tpl('admin/modules/pageLayout/header', $d) ?>

<div class="pad">
  <? if (!$d['page']) { ?>
  <div class="info">Для изменения
    <b>Формата страницы</b> определенного раздела, используйте кнопку для соответствующего раздела в <a
      href="<?= $this->getPath(1) ?>/pages"><b>Разделах сайта</b></a></div>
  <? } ?>
  <div class="legend">
    <div class="color" style="background-color:#F6E176"></div>
    — основная область страницы<br/>
    <div class="color" style="background-color:#CBCDD0"></div>
    — колонки для размещения блоков<br/>
    <div class="color" style="background-color:#9CF28E"></div>
    — заголовочный блок
    <!-- 
    <div class="item allowGlobalBlocks">разрешены глобальные блоки</div>
    <div class="item">не разрешены глобальные блоки</div>
     -->
  </div>
</div>

<style>
  .layouts .item a div {
    text-align: center;
    vertical-align: center;
    display: block;
    width: 139px;
    height: 94px;
  }
  .layouts .item a div span {
    margin-top: 10px;
    width: 100px;
    color: #000;
    font-size: 10px;
    border: 1px solid #B2B3B3;
    display: inline-block;
    background: #FFF;
    border-radius: 5px;
    padding: 5px;
    opacity: 0.7;
  }
</style>

<div class="layouts" id="layouts">
  <? foreach ($d['layouts'] as $v) {
  // Если это шаблон по умолчанию и лейаут не поддерживает глобальные блоки
  if (!$d['page'] and !$v['allowGlobalBlocks']) continue; ?>
  <div class="item<?= $v['allowGlobalBlocks'] ? ' allowGlobalBlocks' : '' ?>">
    <a href="" data-n="<?= $v['n'] ?>"<?= $d['layoutN'] == $v['n'] ? ' class="sel"' : '' ?>><div style="background-image: url(/i/img/layout/<?= $v['n'] ?>.png)"><span><?= str_replace(' - ', '<br>', $v['descr']) ?></span></div></a>
  </div>
  <? } ?>
  <div class="clear"><!-- --></div>
</div>

<script type="text/javascript" src="./i/js/ngn/Ngn.cp.PageLayout.js"></script>
<script type="text/javascript">
  new Ngn.cp.PageLayout($('layouts'));
</script>