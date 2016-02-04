<div class="apeform<?= $d['forceDefaultInit'] ? ' forceDefaultInit' : '' ?>">
  <?= $d['form'] ?>
</div>
<script>
  Ngn.Form.factory(document.getElement('.apeform form'));
</script>