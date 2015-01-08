[
  ['~thenUrl', 'default/testUsers/dialogAuth'],
  ['~click', '.tab-menu li:nth-child(2) a'],
  ['~fill', 'form#formUserRegPhone', { phone: '+71111111111', code: '123' }],
  ['~click', '#auth_ok'],
  ['~fill', 'form#formUserReg', { pass: '123' }],
  ['~click', '#auth_ok'],
  ['checkText', '.container', 'success']
]
