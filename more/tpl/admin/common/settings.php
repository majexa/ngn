<?/*

Пример входных данных:

$d = array(
  'name' => 'settings_1',
  'structure' => array(
    'name1' => 'Имя 1',
    'limit' => 'Кол-во записей на странице'
  ),
  'values' => array(
    'name1' => 'такое вот имя',
    'limit' => 10
  )
);

*/?>
<form action="<?= $this->getPath() ?>" method="post">
  <input type="hidden" name="action" value="setSettings" />
  <h2><?= Lang::get('common') ?></h2>
  <? $this->tpl('common/settings', [
    'structure' => $d['structure'],
    'values' => $d['values']
  ]) ?>
  <p><input type="submit" value="<?= Lang::get('save') ?>" style="width:200px;height:30px;" /></p>
</form>