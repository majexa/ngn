<? $this->tpl('admin/modules/privMsgs/header') ?>

<h2><?= Locale::get('sendMessage') ?></h2>

<form action="<?= $this->getPath()?>" method="post">
  <input type="hidden" name="action" value="send" />
  <? if (empty($d['toUser']['id'])) { ?>
    <p><?= Locale::get('sendTo') ?>: <small class="gray">(<?= Locale::get('findUser') ?>)</small></p>
    <p><? $this->tpl('common/autocompleter', ['name' => 'user']) ?></p>
  <? } else { ?>
    <h3>Отправка сообщения пользователю <b><?= $d['toUser']['login'] ?></b></h3>
    <input type="hidden" name="user" value="<?= $d['toUser']['id'] ?>" />
  <? } ?>
  <textarea name="text" style="width:500px;height:200px;"></textarea>
  <p><input type="submit" value="<?= Locale::get('send') ?>" style="width:200px;height:30px;" /></p>
</form>