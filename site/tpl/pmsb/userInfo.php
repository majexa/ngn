<h2><?= $d['title'] ?></h2>

<? $i = UsersCore::getImageData($d['user']['id']); ?>
<? if ($i['image']) { ?>
  <div class="avatarLarge"><img src="/<?= $i['md_image'] ?>" style="width:182px" /></div>
<? } ?>

<? if (Config::getVarVar('privMsgs', 'enable')) { ?>
<div class="iconsSet">
  <a href="#" class="btn btn1 privMsgs" id="sendPrivMsg"><span><i></i>Отправить сообщение</span></a>
</div>
<script>
$('sendPrivMsg').addEvent('click', function(e) {
  e.preventDefault();
  new Ngn.Dialog.RequestForm({
    url: '/c/userSendEmail?toUserId=<?= $d['user']['id'] ?>'
  });
});
</script>
<? } ?>