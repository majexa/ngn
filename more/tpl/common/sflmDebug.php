<style>
  .sflmPaths {
    background: yellow;
    padding: 10px;
    position: absolute;
    top: 0px;
    left: 0px;
    z-index: 10;
    font-size: 11px;
    line-height: 12px;
  }
</style>
<div class="sflmPaths"><?= implode(Sflm::frontend('js')->getPaths(), "<br>") ?></div>
