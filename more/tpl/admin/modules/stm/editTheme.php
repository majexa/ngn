<? $this->tpl('admin/modules/stm/header', $d) ?>

<?
$this->tpl('common/form', [
  'form'             => $d['form'],
  'forceDefaultInit' => true
])
?>

<?
$link = '/?theme[location]='.$d['params'][3].'&theme[design]='.$d['params'][5].'&theme[n]='.$d['params'][6];
?>

<script type="text/javascript" src="/i/js/ngn/Ngn.Frm.stmEditFieldsSaver.js"></script>
<script type="text/javascript">

  var form = Ngn.Form.factory(document.getElement('.apeform form'), {
    equalElementHeights: true
  });
  Ngn.Frm.stmEditFieldsSaver.delay(500, null, {
    formId: form.eForm.get('id'),
    updateAction: 'ajax_updateTheme',
    fancyUploadAction: 'json_themeFancyUpload',
    sessionId: '<?= session_id() ?>',
    useSaver: true
  });
  var eMenuSelect = $('menui');
  Ngn.Btn.btn({
    title: 'Редактировать меню',
    prop: {
      events: {
        click: function(e) {
          window.open(Ngn.getPath(false, 123).replace('editTheme', 'editMenu'));
          return false;
        }
      }
    }
  }).inject(eMenuSelect, 'after');
</script>
