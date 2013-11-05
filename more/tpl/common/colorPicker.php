<? $id = Misc::name2id($d['default']); ?>
<div class="color" style="background-color:<?= $d['default'] ?>; border: 1px solid #CCCCCC; width: 18px; height: 17px; float: left; margin-right: 5px;">&nbsp;</div>
<input name="<?= $d['name'] ?>" type="text" value="<?= $d['default'] ?>" 
       id="<?= $id ?>" style="width:60px"<?= $d['classAtr'] ?> />
