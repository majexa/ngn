<?php

class DmfaDdTagsTreeMultiselectAc extends DmfaDdTagsTreeMultiselect {

  function source2formFormat($v) {
    return $v;
  }

  function form2sourceFormat($v) {
    return array_map('intval', Misc::quoted2arr($v));
  }

}