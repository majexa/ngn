<div class="arrow">→</div>
<?= Html::select($d['name'], $d['options'], $d['default'], [
  'class' => 'required',
  'data' => [
    'source' => 'ajax'
  ]
]) ?>