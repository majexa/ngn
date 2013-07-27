<ul>
<? foreach ($d['items'] as $name => $v) { ?>
  <li><a href="<?= $this->getPath(2).'/flash/'.$name ?>"><?= $v['title'] ?></a></li>
<? } ?>
</ul>