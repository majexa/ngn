<? if (!$d) { print 'отсутствуют'; return; } ?>
<?= $this->enumDddd($d, '`<a href="`.$this->getUserPath($userId).`">`.$login.`</a>`') ?>
<div class="clear"><!-- --></div>
