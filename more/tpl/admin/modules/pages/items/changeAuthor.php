<h2>Изменение автора <b><?= count($d['itemIds']) ?></b> записей.</h2>
<form action="<?= $this->getPath() ?>" method="POST">
  <input type="hidden" name="action" value="<?= $d['postAction'] ?>" />
  <? foreach ($d['itemIds'] as $id) { ?>
    <input type="hidden" name="itemIds[]" value="<?= $id ?>" />
  <? } ?>
  <div>
    <p><b>Найдите и выберите нового автора:</b></p>
    <? $this->tpl('common/autocompleter', ['name' => 'userId', 'actionKey' => 'user']) ?>
  </div>
  <p><input type="submit" value="Изменить" style="width:200px;height:25px;" /></p>
</form>
