<? if ($d['action'] == 'complete') { ?>
  <p><?= Locale::get('emailSent') ?></p>
<? } elseif ($d['action'] == 'failed') { ?>
  <p><?= Locale::get('sendError') ?></p>
<? } else { ?>
  <div class="apeform"><?= $d['form'] ?></div>
<? } ?>