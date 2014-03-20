<? $this->tpl('admin/modules/stm/header', $d) ?>
<?
$this->tpl('common/form', [
  'form' => $d['form'],
  'forceDefaultInit' => true
])
?>

<script type="text/javascript" src="/i/js/ngn/Ngn.Frm.stmEditFieldsSaver.js"></script>
<script type="text/javascript">
var form = Ngn.Form.factory(document.getElement('.apeform form'), {
  equalElementHeights: true
});
Ngn.Frm.stmEditFieldsSaver.delay(500, null, {
  formId: form.eForm.get('id'), 
  updateAction: 'ajax_updateMenu',
  fancyUploadAction: 'json_menuFancyUpload',
  sessionId: '<?= session_id() ?>',
  useSaver: true
});
</script>