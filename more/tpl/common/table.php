<style>
.simpleTable td {
padding: 5px;
line-height: 1.3em;
border-bottom: 1px solid #CCC;
border-right: 1px solid #CCC;
}
</style>
<table class="simpleTable" cellspacing="0" border="1">
<? foreach ($d as $row) { ?>
<tr>
  <? foreach ($row as $v) { ?>
    <td><?= is_array($v) ? getPrr($v) : $v ?></td>
  <? } ?>
</tr>
<? } ?>
</table>