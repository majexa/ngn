<div class="arrow">→</div>
<?= Html::select($d['name'], $d['options'], null, [
  'class' => 'required',
  'data' => [
    'source' => 'ajax'
  ]
]) ?>