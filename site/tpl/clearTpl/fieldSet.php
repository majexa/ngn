<div class="apeform">
<?php 

print O::get('Form', new Fields([
  [
    'title' => 'bbbbb2b',
    'type' => 'headerToggle'
  ],
  [
    'title' => 'sdfsdf',
  ],
  [
    'title' => 'asd',
    'name' => 'dddddddddd',
    'type' => 'fieldSet',
    'fields' => [
      [
        'title' => 'Fgreg',
        'name' => 'fff'
      ],
      [
        'title' => 'Element 2',
        'name' => 'ffg',
        'type' => 'select',
        'options' => [1]
      ],
    ]
  ],
  [
    'title' => 'sdfsdf',
  ],
  [
    'title' => 'tgrg weff',
  ],
  
  
]))->html();

O::get('Form', new Fields([
  [
    'title' => 'bbbbb2b',
    'type' => 'headerToggle'
  ],
  [
    'title' => 'Simple title',
    'name' => 'title2',
  ],
  [
    'title' => 'asd',
    'name' => 'dddddddddd',
    'type' => 'fieldSet',
    'fields' => [
      [
        'title' => 'Fgreg',
        'name' => 'fff'
      ],
      [
        'title' => 'Element 2',
        'name' => 'ffg',
        'type' => 'select',
        'options' => [1]
      ],
    ]
  ],
  [
    'title' => 'vwevewv',
    'type' => 'headerToggle'
  ],
  [
    'title' => 'FSFS',
    'name' => 'titlef2',
  ],
  
]))->html();

?>
</div>

<script>
  Ngn.Form.factory(document.getElement('form'));
</script>
