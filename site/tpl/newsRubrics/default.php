<div class="items">
<? foreach ($d['tags'] as $k => $v) { ?>
  <div class="item">
  <a href="<?= $this->getPath().'/'.$v['name'] ?>"><?= $v['title'] ?></a>
  </div>
<? } ?>
</div>