<? $id = Misc::name2id($d['name']) ?>
<? if (!$d['actionKey']) $d['actionKey'] = $d['name']; ?>
<input type="text" class="fld" value="<?= $d['acDefault'] ?>" />
<input type="text" class="val" style="display:none" name="<?= $d['name'] ?>" value="<?= $d['default'] ?>" />
