Ngn.Form.Upload = new Class({
  Implements: [Options, Events],

  options: {
    dropMsg: 'Пожалуйста перетащите файлы сюда',
    onComplete: function(){
      //window.location.reload(true);
    },
    fileOptions: {}
  },

  initialize: function(eForm, eInput, options) {
    this.eForm = eForm;
    this.eInput = document.id(eInput);
    this.name = this.eInput.get('name');
    this.setOptions(options);
    if ('FormData' in window) {
      this.beforeInit();
      this.init();
      this.afterInit();
    }
    else throw new Error('FormData.window not exists');
  },
  
  beforeInit: function() {},
	
  init: function() {
    this.eFiles = new Element('div.uploadFiles').inject(this.eInput, 'after');
    this.eProgress = new Element('div.fileProgress')
      .setStyle('display', 'none').inject(this.eFiles, 'after');
    this.uploadReq = new Ngn.Request.File({
      url: this.options.url,
      onRequest: function() {
        this.eProgress.setStyles({display: 'block', width: 0});
        this.eFiles.set('html', 'Происходит загрузка');
      }.bind(this),
      onProgress: function(event) {
        var loaded = event.loaded, total = event.total;
        var proc = parseInt(loaded / total * 100, 10).limit(0, 100);
        this.eProgress.setStyle('width', proc + '%');
        if (proc == 100) this.eFiles.set('html', 'Загрузка завершена. Происходит добавление');
      }.bind(this),
      onComplete: function(r) {
        this.eProgress.setStyle('width', '100%');
        this.fireEvent('complete');
      }.bind(this)
    });
  },
  
  afterInit: function() {}
  
});

Ngn.Form.Upload.Multi = new Class({
  Extends: Ngn.Form.Upload,

  afterInit: function() {
    //this.eInput.set('name', this.eInput.get('name').replace('[]', ''));
    //c(this.eInput);
    this.inputFiles = new Ngn.Form.MultipleFileInput(this.eInput, this.eFiles,
      $merge({}, this.options.fileOptions)/*, {
      drop: drop,
      onDragenter: drop.addClass.pass('hover', drop),
      onDragleave: drop.removeClass.pass('hover', drop),
      onDrop: drop.removeClass.pass('hover', drop)
    }*/);
    /*
    drop = new Element('div.fileDroppable', {
      text: this.options.dropMsg
    }).inject(input, 'after'),
    */
  },
  
  send: function() {
    var n = 0;
    this.inputFiles.getFiles().each(function(file) {
      this.uploadReq.append(this.name + '['+n+']', file);
      n++;
    }.bind(this));
    this.uploadReq.send();
  }

});

Ngn.Form.Upload.Single = new Class({
  Extends: Ngn.Form.Upload,

  beforeInit: function() {
    this.eInput.addEvents({
      change: function() {
        this.file = this.eInput.files[0];
      }.bind(this)
    });
  },
  
  afterInit: function() {
    if (this.options.loadedFiles[this.eInput.get('name')])
      this.eFiles.set('html', 'Загружен: ' + this.options.loadedFiles[this.eInput.get('name')].name);
  },

  send: function() {
    if (!this.file) {
      this.fireEvent('complete');
      return;
    }
    this.uploadReq.append(this.eInput.get('name'), this.file);
    this.uploadReq.send();
  }

});
