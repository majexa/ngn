<?

if ($d['success'])
  $this->tpl(
    'common/success',
    'Обновление E-maila прошло успешно');
elseif ($d['errors']) $this->tpl('common/errors', $d['errors']);
  
?>