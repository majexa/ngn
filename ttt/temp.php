<div class="sendMsg">
<form action="<?= $this->getPath()?>" method="post" id="msgForm">
  <input type="hidden" name="action" value="<?= $d['postAction'] ?>" />
  <? if (!empty($d['toUser'])) { ?>
    Сообщение пользователю <a href="<?= $this->getUserPath($d['toUser']['id'])?>"><?= $d['toUser']['login'] ?></a>:<br />
    <input type="hidden" name="user" value="<?= $d['toUser']['id'] ?>" />
  <? } else { ?>
    <p><?= sendTo ?>: <small class="gray">(<?= findUser ?>)</small></p>
    <p><? $this->tpl('common/autocompleter', ['name' => 'user']) ?></p>
  Текст сообщения:<br>
  <? } ?>
  <textarea name="text" id="msgText"></textarea>
  <p><a href="" class="btn btnSubmit btnSubmitLarge" title="(Ctrl+Enter)"><span>Отправить (Ctrl + Enter)</span></a><div class="clear"><!-- --></div></p>
</form>
<script type="text/javascript">
$('msgText').addEvent('keydown', function(e){
  if (e.key == 'enter' && e.control) {
    $('msgForm').submit();
  }
});
$('msgForm').getElement('.btnSubmit').addEvent('click', function(e){
  e.preventDefault();
  $('msgForm').submit();
});
</script>
</div>