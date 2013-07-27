<?php

    $oF = new Form(new Fields([
      [
        'title' => 'Использовать имена файлов в качестве названий',
        'name' => 'filenameAsTitle',
        'type' => 'boolCheckbox'
      ],
      [
        'title' => 'Изображения',
        'name' => 'images',
        'type' => 'image',
        'multiple' => true
      ]
    ]), [
      'submitTitle' => 'Добавить'
    ]);
    
    print $oF->html();
    
    return;

$forms = [
new Form(new Fields([
  [
    'type' => 'fieldList',
    'fieldsType' => 'image',
    'name' => 'a',
    'title' => 'images'
  ]
])),
new Form(new Fields([
  [
    'type' => 'image',
    'name' => 'b',
    'title' => 'image'
  ]
]))
];
?>
<style>
table td {
vertical-align: top;
}
table form {
width: 400px;
}
</style>
<?
print "<table><tr>";
foreach ($forms as $n => $f) {
  UploadTemp::extendFormOptions($f);
  $f->setElementsData();
  print "<td>".$f->html();
  print "\n<script>Ngn.Form.factory($('{$f->id}'));</script>\n";
  if ($f->isSubmitted()) prr($f->getData());
  print "</td>";
}
print "</tr></table>";
