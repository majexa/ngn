<?

$checkbox = '<input type="checkbox" name="'.$d['name'].'[]" value="`.$id.`" id="'.$d['name'].'_`.$id.`"`.(in_array($id, $values) ? ` checked` : ``).` />';
print $this->getDbTree(
  $d['tree'],
  '`<li><span class="marker">↓</span>'.$checkbox.' <a href="#" data-id="`.$id.`" data-loadChildren="`.(bool)$childNodesExists.`" class="pseudoLink">`.$title.`</a></li>`',
  '`<li id="leaf_`.$id.`_`.$parentId.`"><span class="marker"></span>'.$checkbox.'<label for="'.$d['name'].'_`.$id.`"><span class="checkboxText">`.$title.`</span></label></li>`',
  '`<ul`.($id ? ` class="nodes_`.$id.`"` : ``).`>`',
  '`</ul>`',
  ['values' => is_array($d['value']) ? $d['value'] : []]
);