Ngn.Dialog.RequestFormBase = new Class({
  Extends: Ngn.Dialog,

  options: {
    okDestroy: false,
    jsonRequest: true,
    autoSave: false,
    getFormData: function() {
      return Ngn.frm.toObj(this.form.eForm);
    },
    onFormRequest: $empty,
    onSubmitSuccess: $empty
  },

  initialize: function(options) {
    options = options || {};
    options.ok = this.submit.bind(this);
    if (!$defined(options.submitUrl)) {
      if (!$defined(options.jsonSubmit))
        options.jsonSubmit = false;
      options.submitUrl = options.url;
    }
    this.parent(options);
    this.toggle('ok', false);
    //this.initFormResponse();
    this.iframeUpload = true;
    window.addEvent('keypress', function(e) {
      if (e.key != 'enter' || e.target.get('tag') == 'textarea') return;
      e.preventDefault();
      this.submit();
    }.bind(this));
  },

  form: null,
  response: null,

  urlResponse: function(r) {
    this.response = r;
    this.toggle('ok', true);
    this.loading(false);
    if (r.title) this.setTitle(r.title);
    if (r.submitTitle) this.setOkText(r.submitTitle);
    if (r.jsOptions) {
      if (r.jsOptions.onOkClose)
        this.addEvent('okClose', r.jsOptions.onOkClose);
    }
    this.setMessage(r.form, false);
    this.form = Ngn.Form.factory(this.message.getElement('form'), {
      ajaxSubmit: true,
      disableInit: true
    });
    this.form.options.dialog = this; // Важно создавать передавать объект Диалога в объект 
    // Формы после выполнения конструктура, иначе объект
    // Даилога не будет содержать созданого объекта Формы
    this.form.init();
    this.fireEvent('formResponse');

    this.form.addEvent('submit', function(r) {
      this.fireEvent('formRequest');
      this.loading(true);
    }.bind(this));

    this.form.addEvent('failed', function(r) {
      this.formResponse(r);
      this.loading(false);
    }.bind(this));

    this.form.addEvent('complete', function(r) {
      this.response = r;
      this.okClose();
      this.fireEvent('submitSuccess', r);
    }.bind(this));

    this.resizeByCols();
    if (this.options.autoSave) {
      new Ngn.frm.Saver(this.form.eForm, {
        url: this.options.submitUrl,
        jsonRequest: true
      });
    }
    this.initEvents();
    this.formInit();
    this.initPosition();
  },

  // abstract
  initEvents: function() {},

  resizeByCols: function() {
    var cols = this.form.eForm.getElements('.type_col');
    if (!cols.length) return;
    var maxY = 0;
    var ys = [];
    var x = 0;
    for (var i = 0; i < cols.length; i++) {
      ys[i] = cols[i].getSize().y;
      x += cols[i].getSize().x;
    }
    //for (var i=0; i<cols.length; i++) cols[i].setStyle('height', ys.max() + 'px');
    this.dialog.setStyle('width', (x + 12) + 'px');
  },

  formInit: function() {
  },

  submit: function() {
    this._submit();
  },

  finishClose: function() {
    this.parent();
    // если в последнем респонзе есть ссылка не следующую форму
    if (this.isOkClose && this.response.nextFormUrl) {
      var opt = {};
      if (this.options.nextFormOptions) opt = $merge(opt, this.options.nextFormOptions);
      opt.url = this.response.nextFormUrl;
      new Ngn.Dialog.RequestForm(opt);
      /*
       new Request.JSON({
       url: this.response.nextFormUrl,
       onComplete: function(r) {
       if (r.error) Ngn.Request.JSON.throwServerError(r.error);
       if (!r.form) throw new Error('Form does not exists in next form url "' + this.response.nextFormUrl + '"');
       var opt = { staticResponse: r };
       if (this.options.nextFormOptions) opt = $merge(opt, this.options.nextFormOptions);
       if (this.response.nextFormOptions) opt = $merge(opt, this.response.nextFormOptions);
       new Ngn.Dialog.RequestForm.Static(opt);
       }.bind(this)
       }).send();
       */
    }
  }

  // abstract
  //_submit: {}

});

Ngn.Dialog.Form = new Class({
  Extends: Ngn.Dialog.RequestFormBase,

  options: {
    onSubmit: $empty
  },

  _submit: function() {
    this.fireEvent('submit', this.options.getFormData.bind(this)());
    this.okClose();
  }

});

Ngn.Dialog.RequestForm = new Class({
  Extends: Ngn.Dialog.RequestFormBase,

  options: {
    autoSave: false,
    formEvents: false
  },

  _submit: function() {
    this.form.submit();
  },

  initEvents: function() {
    if (!this.options.formEvents) return;
    var obj = this;
    for (var i = 0; i < this.options.formEvents.length; i++) {
      var evnt = this.options.formEvents[i];
      this.message.getElement('[name=' + evnt.fieldName + ']').addEvent(evnt.fieldEvent, function() {
        obj.fireEvent(evnt.formEvent, this.get('value'));
      });
    }
  }

});

Ngn.Dialog.RequestForm.Static = new Class({
  Extends: Ngn.Dialog.RequestForm,

  // options: {
  //   staticResponse: {
  //     title: text
  //     submitTitle: text
  //     form: html
  //   }
  // }

  initFormResponse: function() {
    this.formResponse(Ngn.json.process(this.options.staticResponse));
  }

});