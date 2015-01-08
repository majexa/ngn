<h2 class="tab">Зарегистрированы</h2>
<div class="apeform">
<?
$oF1 = new AuthForm('Подписаться');
$oF1->action = $this->getPath(0).'/c/auth/ajax_popup';
print $oF1->html().'<div class="clear"><!-- --></div>';
?>
</div>
<h2 class="tab">Новый пользователь</h2>
<div class="apeform">
<?
$oF2 = new UserRegForm([
  'submitTitle' => 'Подписаться'
]);
$oF2->action = $this->getControllerPath('userReg').'/ajax_popupReg';
print $oF2->html().'<div class="clear"><!-- --></div>';
?>
</div>
