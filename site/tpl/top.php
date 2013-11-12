<? $d['path'] = $d['path'] ? : $this->getPath() ?>
<div id="auth">
  <? /*if ($this->getControllerPath('searcher', true)) { ?>
  <div class="item" style="float:right">
    <div id="searchBox">
      <input class="fld" name="s" id="fldSearch" value="text" />
      <a href="#" class="btn btn1" id="btnSearch">Искать</a>
    </div>
  </div>
  <? }*/ ?>
  <? if (!Auth::get('id')) { ?>
  <form action="<?= $d['path'] ?>" method="post" id="authForm">
    <div class="item"><input type="text" class="fld" name="authLogin" id="authLogin" value="login"/></div>
    <div class="item"><input type="password" class="fld" name="authPass" id="authPass" value="password"/></div>
    <!--
    <div class="item">
      <label for="myComputer">
      <input type="checkbox" name="expires" value="1" checked id="myComputer" />
        <small>чужой компьютер</small>
      </label>
    </div>
    -->
    <div class="item" style="position:relative">
      <a href="#" class="btn btnSubmit" id="btnLogin"><span>Войти</span></a>
      <? if ($d['errors']) $this->tpl('slideTips/auth', $d['errors']) ?>
    </div>
    <div class="item">
      <a href="/c/userReg" class="btn btn1" id="btnReg"><span>Регистрация</span></a>
    </div>
    <div class="item">
      <a href="/c/userReg/lostpass" class="btn nobg">Забыли?</a>
    </div>
    <div class="clear"><!-- --></div>
  </form>
  <?
}
else {
  $links = [];
  $links['pseudoLink briefcase'] = [UsersCore::getTitle(Auth::get('id')), '/c/userReg/editPass'];
  //$links['settings notext'] = ['Регистрационные данные', $this->getControllerPath('userReg').'/editPass'];
  //$d['privMsgs']['newMsgsCount'] = 2;
  //$links['send'.($d['privMsgs']['newMsgsCount'] ? '2' : 'Off').' gray'] = ['Приватные сообщения', '/privMsgs'];
  //$this->tpl('slideTips/privMsgs');
  if (isset($d['links'])) $links = array_merge($links, $d['links']);
  if (Misc::isGod()) $links['dgary god'] = ['Храм Господен', '/god'];
  elseif (Misc::isAdmin()) $links['dgray admin'] = ['Панель управления', '/admin'];
  $links['logout gray'] = ['Выйти', $d['path'].'?logout=1'];
  ?>
  <div id="personal">
    <div class="item iconsSet">
      <? foreach ($links as $cls => $v) { ?>
      <a href="<?= $v[1] ?>" class="<?= $cls ?>"><i></i><?= $v[0] ?></a>
      <? } ?>
      <div class="clear"></div>
    </div>
  </div>
  <? } ?>
</div>