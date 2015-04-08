Ngn.Btn.AddAuthorized = function(eParent, opt) {
  if (!opt) opt = {};
  var add = function() {
    new Ngn.Dialog.RequestForm({
      title: opt.dialogTitle || 'Добавление',
      width: 300,
      url: opt.basePath + '/?a=json_new',
      onOkClose: function() {
        window.location.reload();
      }
    });
  };
  Ngn.Btn.btn1(opt.btnTitle || 'Добавить', 'add').inject(eParent).addEvent('click', function() {
    if (Ngn.authorized) {
      add();
    } else {
      new Ngn.Dialog.Auth({
        reloadOnAuth: false,
        onAuthComplete: add
      });
    }
  });
};