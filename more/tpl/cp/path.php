<?

$d['filter1'] = [
  'name' => 'cityLive',
  'title' => 'Город проживания',
  'param' => 't',
  'options' => [
    'asdasd'
  ]
];

?>
    <div class="pagePath">
      <? if (isset($d['filter'])) { ?>
      <div class="itemsFilter">
        <div class="iconsSet icon_filter"><i></i><?= $d['filter']['title'] ?>:</div>
        <?= Html::select($d['filter']['name'], $d['filter']['options'], $d['filter']['selected'], ['tagId' => 'filter_'.$d['filter']['name']]) ?>
      </div>
      <script type="text/javascript">
      var eFilter = $('filter_<?= $d['filter']['name'] ?>');
      eFilter.addEvent('change', function(e) {
        if (this.get('value')) {
          window.location = Ngn.getPath(4) + '/' + '<?= $d['filter']['param'] ?>' + '.' + 
            this.get('name') + '.' + eFilter.get('value');
        } else {
          window.location = Ngn.getPath(4);
        }
      });
      </script>
      <? } ?>
      <div class="cont">

        <? if ($d['path']) { ?>
        <?= $this->enumDddd($d['path'], '`<a href="`.$link.`" class="`.$name.`">`.$title.`</a>`', ' → ') ?>
        <? } ?>
      </div>
      <div class="clear"><!-- --></div>
    </div>
