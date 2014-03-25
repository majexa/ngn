Ngn.Btn.FileUpload = new Class({
  Implements: [Options],

  options: {
    // url: '',
    mime: '',
    onRequest: function() {
    },
    onComplete: function() {
    }
  },

  initialize: function(btn, options) {
    this.btn = btn;
    this.setOptions(options);
    var eUploadWrapper = new Element('div', {styles: {
      position: 'relative',
      display: 'inline-block',
      overflow: 'hidden'
    }}).wraps(this.btn.el);
    var eEile = new Element('input', {type: 'file', accept: this.options.mime, events: {
      mouseover: function() {
        this.getParent().getElement('a').addClass('over');
      },
      mouseout: function() {
        this.getParent().getElement('a').removeClass('over');
      }
    }, styles: {
      position: 'absolute',
      cursor: 'pointer',
      width: this.btn.el.getSize().x + 'px',
      height: this.btn.el.getSize().y + 'px',
      top: '0px',
      left: '0px',
      'z-index': 2,
      'opacity': 0
    }}).inject(eUploadWrapper, 'bottom');
    eEile.addEvent('change', function() {
      req.append('file', this.files[0]);
      req.send();
    });
    this.options.onRequest = this.options.onRequest.bind(this);
    this.options.onComplete = this.options.onComplete.bind(this);
    var req = new Ngn.Request.File({
      url: this.options.url,
      formData: {
        name: 'bg'
      },
      onRequest: function() {
        this.btn.toggleDisabled(false);
        this.options.onRequest();
      }.bind(this),
      onProgress: function(event) {
        var loaded = event.loaded, total = event.total;
        var proc = parseInt(loaded / total * 100, 10).limit(0, 100);
        //c('Загружено ' + proc + '%');
        //if (proc == 100) c('Загрузка завершена');
      }.bind(this),
      onComplete: function(r) {
        this.btn.toggleDisabled(true);
        this.options.onComplete(r);
        eEile.set('value', '');
        req.clear();
      }.bind(this)
    });

  }

});
