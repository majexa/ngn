Ngn.Form.Upload = new Class({
  Implements: [Options, Events],

  options: {
    dropMsg: 'Пожалуйста перетащите файлы сюда',
    onComplete: function() {
      //window.location.reload(true);
    },
    fileOptions: {}
  },

  initialize: function(form, eInput, options) {
    this.form = form;
    this.eInput = document.id(eInput);
    this.eCaption = this.eInput.getParent('.element').getElement('.help');
    this.name = this.eInput.get('name');
    this.setOptions(options);
    if ('FormData' in window) {
      this.beforeInit();
      this.init();
      this.afterInit();
    } else throw new Error('FormData.window not exists');
  },

  beforeInit: function() {
  },

  inProgress: false,

  init: function() {
    //this.eCaption = new Element('div.uploadFiles').inject(this.eInput, 'after');
    this.eProgress = new Element('div.fileProgress').setStyle('display', 'none').inject(this.eCaption, 'after');
    this.requestFile = new Ngn.Request.File({
      url: this.options.url,
      onRequest: function() {
        this.inProgress = true;
        this.eProgress.setStyles({display: 'block', width: 0});
        this.eCaption.set('html', 'Происходит загрузка');
      }.bind(this),
      onProgress: function(event) {
        var loaded = event.loaded, total = event.total;
        var proc = parseInt(loaded / total * 100, 10).limit(0, 100);
        this.eProgress.setStyle('width', proc + '%');
        if (proc == 100) this.eCaption.set('html', 'Загрузка завершена');
      }.bind(this),
      onComplete: function(r) {
        this.inProgress = false;
        this.eProgress.setStyle('width', '100%');
        this.fireEvent('complete', {result: r});
      }.bind(this)
    });
  },

  afterInit: function() {
  }

});

Ngn.Form.Upload.Single = new Class({
  Extends: Ngn.Form.Upload,

  beforeInit: function() {
    this.eInput.addEvents(this.options.fileEvents);
    this.eInput.addEvents({
      change: function() {
        // the main place in file classes
        this.file = this.eInput.files[0];
        if (this.file.size > Ngn.fileSizeMax) {
          this.eInput.addClass('maxFileSizeExceeded');
        } else {
          this.eInput.removeClass('maxFileSizeExceeded');
        }
      }.bind(this)
    });
  },

  //afterInit: function() {
  //  if (this.options.loadedFiles[this.eInput.get('name')]) {
  //    this.eCaption.set('html', 'Загружен: ' + this.options.loadedFiles[this.eInput.get('name')].name);
  //  }
  //},

  send: function() {
    if (!this.file) {
      this.fireEvent('complete');
      return;
    }
    this.requestFile.append(this.eInput.get('name'), this.file);
    this.requestFile.send();
  }

});

Ngn.Form.Upload.Multi = new Class({
  Extends: Ngn.Form.Upload,

  afterInit: function() {
    this.inputFiles = new Ngn.Form.MultipleFileInput(this.eInput, this.eCaption);
    this.inputFiles.addEvents(this.options.fileEvents);
  },

  send: function() {
    var n = 0;
    this.inputFiles.getFiles().each(function(file) {
      this.requestFile.append(this.name, file);
      n++;
    }.bind(this));
    this.requestFile.send();
  }

});
