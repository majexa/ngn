<? if (Auth::get('login')) { ?>
  <?= Locale::get('loggedAs') ?>
  <b>
  <? if (AdminModule::isAllowed('profile')) { ?>
    <a href="<?= $this->getPath(1).'/profile' ?>" class="smIcons inline edit"><i></i><?= Auth::get('login') ?></a>
  <? } else { ?>
    <?= Auth::get('login') ?>
  <? } ?>
  </b>
  <? if (!$d['god'] and Misc::isGod()) { ?>
    <div class="mode"><a href="<?= str_replace('/admin', '/god', $_SERVER['REQUEST_URI']) ?>" class="smIcons god"><i></i><?= Locale::get('switchGodMode') ?></a></div>
  <? } elseif ($d['god']) { ?>
    <div class="mode"><a href="<?= str_replace('/god', '/admin', $_SERVER['REQUEST_URI']) ?>" class="smIcons god"><i></i><?= Locale::get('switchAdminMode') ?></a></div>
 <? } ?>
<? } ?>
