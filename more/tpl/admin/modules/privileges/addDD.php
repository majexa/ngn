<? $this->tpl('admin/modules/privileges/header') ?>

<h2>Назначение привилегий</h2>
<p>
  Назначте привилегию на это поле и пользователь сможет видеть его.
</p>

<form action="<?= $this->getPath() ?>" method="POST">
  <input type="hidden" name="action" value="createDD" />
  <input type="submit" value="Назначить" style="width:150px; height:30px;" />
  <div class="col" style="width:170px;">
    <p><b>Найдите и выберите пользователя:</b></p>
    <? $this->tpl('common/search', ['name' => 'user']) ?>
  </div>
  <div class="col" style="width:170px;">
    <p><b>Найдите и выберите поле:</b></p>
    <? $this->tpl('common/search', ['name' => 'page']) ?>
  </div>
  <div class="col" style="width:170px;">
    <? $this->tpl('common/checkboxes', ['name' => 'types', 'items' => $d['types']]) ?>
  </div>
</form>