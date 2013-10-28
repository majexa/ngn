<style>
  .jsRedirect {
    margin-left: 30%;
    width: 400px;
    padding: 50px 10px;
  }
</style>
<div class="jsRedirect">
  <? $sec = 3; ?>
  Перенаправление на <a href="<?= $d['redirect'] ?>"><?= $d['redirect'] ?></a>
  через <span id="countdown"><?= date('m/d/Y H:i:s', time() + $sec) ?></span>.
</div>
<script type="text/javascript">
  new MooCountdown.Simple($('countdown'), {
    onComplete: function() {
      window.location = '<?= $d['redirect'] ?>';
    }
  });
</script>
