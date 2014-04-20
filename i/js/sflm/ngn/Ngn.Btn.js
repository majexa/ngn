// @requires Ngn.frm

/**
 * opt: cls, title, prop, caption
 */
Ngn.btn = function(opt) {
  if (!opt) opt = {};
  if (!opt.cls) opt.cls = '';
  if (!opt.title && !opt.cls.contains('btn')) opt.cls = 'bordered ' + opt.cls;
  var a = new Element('a', $merge({
    'class': (opt.cls.contains('icon') ? '' : 'smIcons ') + opt.cls,
    html: opt.title || ''
  }, opt.prop || {}));
  if (opt.caption) {
    a.set('title', opt.caption);
    a.setTip(opt.caption);
  }
  new Element('i').inject(a, 'top');
  return a;
};

/**
 * Кнопка с заголовком
 */
Ngn.btn1 = function(title, cls, prop) {
  return Ngn.btn({
    title: title,
    cls: cls,
    prop: prop
  });
};

/**
 * Кнопка с всплывающей подсказкой
 */
Ngn.btn2 = function(caption, cls, prop) {
  return Ngn.btn({
    caption: caption,
    cls: cls,
    prop: prop
  });
};


Ngn.btns = function(btns, small, opt) {
  if (!opt) opt = {};
  var defaultOpt = { bordered: true };
  if (!opt.bordered) opt.bordered = true;
  var eCont = new Element('div', {'class': (small ? 'smIcons' : 'iconsSet') + (opt.bordered ? ' bordered' : '') });
  for (var i = 0; i < btns.length; i++) Ngn.btn($merge(defaultOpt, btns[i])).inject(eCont);
  return eCont;
};

Ngn.btn1Flag = function(defaultFirstState, state1, state2) {
  return Ngn.__btnFlag(Ngn.btn1, defaultFirstState, state1, state2);
};

Ngn.btn2Flag = function(defaultFirstState, state1, state2) {
  return Ngn.__btnFlag(Ngn.btn2, defaultFirstState, state1, state2);
};

Ngn.__btnFlag = function(btn, defaultFirstState, state1, state2) {
  var deflt = defaultFirstState ? state1 : state2;
  return Ngn._btnFlag(btn(deflt.title, deflt.cls), state1, state2);
};

Ngn._btnFlag = function(eA, state1, state2) {
  return eA.addEvent('click', function(e) {
    e.preventDefault();
    var flag = eA.hasClass(state1.cls);
    var newState = flag ? state2 : state1;
    var curState = flag ? state1 : state2;
    if (curState.confirm !== undefined) if (!Ngn.confirm(curState.confirm)) return;
    new Ngn.Request({
      url: curState.url,
      onComplete: function() {
        eA.removeClass(curState.cls);
        eA.addClass(newState.cls);
        eA.set('title', newState.title);
        Ngn.addTips(eA);
      }
    }).send();
  });
};

Ngn.Btn = new Class({
  Implements: [Options],

  options: {
    usePushed: false,
    fileUpload: false
  },

  pushed: false,

  /**
   * @param Element Элемент кнопки
   * @param function/object Функция вызывающаяся при нажатии, либо объект {action: function},
   *                        содержащий эту ф-ю. В объекте может так же находится дополнительная информация, как-то
   *                        id (уникальный идентификатор кнопки) и confirm (флаг - подтверждать нажатие или нет)
   * @param options
   */
  initialize: function(el, action, options) {
    this.setOptions(options);
    this.setAction(action);
    this.el = el;
    this.initVirtualElement(this.el);
    this.toggleDisabled(true);
    var up = function() {
      if (!this.enable) return;
      if (!this.options.usePushed) this.el.removeClass('pushed');
    }.bind(this);
    var down = function() {
      if (!this.enable) return;
      if (!this.options.usePushed) this.el.addClass('pushed');
    }.bind(this);
    this.el.addEvent('mousedown', down);
    this.el.addEvent('tap', down);
    this.el.addEvent('mouseup', up);
    //this.el.addEvent('mouseout', up); // WTF?
    this.el.addEvent('click', function(e) {
      e.preventDefault();
      if (!this.enable) return;
      this.runAction();
    }.bind(this));
    if (this.options.fileUpload) new Ngn.Btn.FileUpload(this, this.options.fileUpload);
    this.init();
  },

  setAction: function(action) {
    if (!action) action = function() {
    };
    if (typeof(action) == 'function') this.action = { action: action.bind(this) };
    else {
      if (action.classAction) {
        // do nothing. action is class
      } else {
        if (action.args) {
          action.action = action.action.pass(action.args, this);
        } else {
          action.action = action.action.bind(this);
        }
      }
      this.action = action;
    }
  },

  runAction: function() {
    if (!this.pushed && this.action.confirm) {
      var opt = {
        id: this.action.id,
        onOkClose: function() {
          this._action();
        }.bind(this)
      };
      if (typeof(this.action.confirm) == 'string') opt.message = this.action.confirm;
      new Ngn.Dialog.Confirm.Mem(opt);
    } else {
      this._action();
    }
  },

  _action: function() {
    this.action.action();
    if (this.options.usePushed) this.togglePushed(!this.pushed);
  },

  init: function() {
  },

  togglePushed: function(pushed) {
    this.pushed = pushed;
    this.pushed ? this.el.addClass('pushed') : this.el.removeClass('pushed');
  },

  toggleDisabled: function(enable) {
    this.enable = enable;
    enable ? this.el.removeClass('nonActive') : this.el.addClass('nonActive');
  }

});

Ngn.Btn.Action = new Class({
  action: function() {}
});

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
