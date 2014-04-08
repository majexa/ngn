<style>
  .sflmPaths {
    width: 300px;
    background: yellow;
    padding: 10px;
    position: absolute;
    right: 0;
    z-index: 10;
    font-size: 11px;
    line-height: 12px;
  }
</style>
<div class="sflmPaths"><?= implode(Sflm::frontend('js')->getPaths(), "<br>") ?></div>
