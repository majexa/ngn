<ul>
  <li>
    <? if (0 and ($path = $this->getUserPath(Auth::get('id'), true))) { ?>
      <a href="<?= $path ?>"><b><?= UsersCore::getTitle(Auth::get('id')) ?></b></a><br />
    <? } else { ?>
      <b><?= UsersCore::getTitle(Auth::get('id')) ?></b><br />
    <? } ?>
  </li>
  <? if (Misc::isAdmin()) { ?>
    <li><a href="/admin">Панель управления</a></li>
  <? } ?>
  <li><a href="<?= $this->getPath() ?>?logout=1">Выйти</a></li>
</ul>
