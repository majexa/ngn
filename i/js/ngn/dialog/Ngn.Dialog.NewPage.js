Ngn.Dialog.NewPageBase = new Class({
  Extends: Ngn.Dialog.RequestForm,
  
  options: {
    controllerPath: '/admin/pages'
  },
  
  getOpt: function() {
    return {
      getFormData: function() {
        var data = Ngn.frm.toObj(this.form.eForm);
        data.parentId = this.options.pageId;
        return data;
      }
    };
  }
  
});


Ngn.Dialog.NewPage = new Class({
  Extends: Ngn.Dialog.NewPageBase,

  initialize: function(_opts) {
    this.parent($merge(_opts, this.getOpt(), {
      title: 'Ручное создание нового раздела',
      url: this.options.controllerPath + '/' + _opts.pageId + '/json_newPage'
    }));
  },
  
  formResponse: function(r) {
    this.parent(r);
    Ngn.frm.initTranslateField('titlei', 'namei');
    this.message.getElement('input[name=title]').focus();
  }
  
});

Ngn.Dialog.NewModulePage = new Class({
  Extends: Ngn.Dialog.NewPageBase,
  
  actionName: 'newModulePage',
  
  initialize: function(_opts) {
    this.parent($merge(_opts, this.getOpt(), {
      title: 'Создание нового раздела',
      url: this.options.controllerPath + '/' + _opts.pageId + '/json_' + this.actionName
    }));
  },
  
  formResponse: function(r) {
    this.parent(r);
    this.message.getElement('input[name=title]').focus();
    this.afterFormResponse();
  },
  
  afterFormResponse: function() {
    Ngn.frm.initTranslateField('titlei', 'namei');
    Ngn.frm.initCopySelectValue('modulei', 'namei');
    Ngn.frm.initCopySelectTitle('modulei', 'titlei');
  }
  
});

Ngn.Dialog.NewModulePageSimple = new Class({
  Extends: Ngn.Dialog.NewModulePage,
  
  actionName: 'newModulePageSimple',
  
  afterFormResponse: function() {}
  
});
