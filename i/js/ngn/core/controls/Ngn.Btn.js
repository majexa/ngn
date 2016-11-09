// @requires Ngn.Frm

Ngn.Btn = new Class({
  Implements: [Options],

  options: {
    usePushed: false,
    request: false,
    fileUpload: false
  },

  pushed: false,

  initialize: function(el, action, options) {
    //if (options.request) this.request = options.request;
    this.setOptions(options);
    this.setAction(action);
    this.el = el;
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
    this.el.addEvent('mouseout', up);
    this.el.addEvent('click', function(e) {
      e.stopPropagation();
      e.preventDefault();
      if (!this.enable) return;
      //if (this.request) this.toggleDisabled(false);
      this.runAction();
    }.bind(this));
    //if (this.request) {
    //  this.request.addEvent('complete', function() {
    //    this.toggleDisabled(true);
    //  }.bind(this));
    //}
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
    if (this.request) this.request.send();
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

/**
 * Создаёт и возвращает html-элемент кнопки
 *
 * @param opt
 * @param opt.cls CSS-класс
 * @param opt.title Заголовок кнопки
 * @param opt.caption Значение тега "title"
 * @returns {HTMLElement}
 */
Ngn.Btn.btn = function(opt) {
  if (!opt) opt = {};
  if (!opt.cls) opt.cls = '';
  if (!opt.title && !opt.cls.contains('btn')) opt.cls = 'bordered ' + opt.cls;
  var a = new Element('a', Object.merge({
    'class': (opt.cls.contains('icon') ? '' : 'smIcons ') + opt.cls,
    html: opt.title || ''
  }, opt.prop || {}));
  if (opt.caption) {
    a.set('title', opt.caption);
    //Ngn.Element.setTip(a, opt.caption);
  }
  new Element('i').inject(a, 'top');
  return a;
};

/**
 * Кнопка с заголовком
 */
Ngn.Btn.btn1 = function(title, cls, prop) {
  return Ngn.Btn.btn({
    title: title,
    cls: cls,
    prop: prop
  });
};

/**
 * Кнопка с всплывающей подсказкой
 */
Ngn.Btn.btn2 = function(caption, cls, prop) {
  return Ngn.Btn.btn({
    caption: caption,
    cls: cls,
    prop: prop
  });
};

Ngn.Btn.flag1 = function(defaultFirstState, state1, state2) {
  return Ngn.Btn.__flag(Ngn.Btn.tn1, defaultFirstState, state1, state2);
};

Ngn.Btn.flag2 = function(defaultFirstState, state1, state2) {
  return Ngn.Btn.__flag(Ngn.Btn.btn2, defaultFirstState, state1, state2);
};

Ngn.Btn.__flag = function(btn, defaultFirstState, state1, state2) {
  var deflt = defaultFirstState ? state1 : state2;
  return Ngn.Btn._flag(Ngn.Btn.btn2(deflt.title, deflt.cls), state1, state2);
};

Ngn.Btn._flag = function(eA, state1, state2) {
  return eA.addEvent('click', function(e) {
    e.preventDefault();
    var flag = eA.hasClass(state1.cls);
    var newState = flag ? state2 : state1;
    var curState = flag ? state1 : state2;
    if (curState.confirm !== undefined) if (!confirm(curState.confirm)) return;
    new Ngn.Request({
      url: curState.url,
      onComplete: function() {
        eA.removeClass(curState.cls);
        eA.addClass(newState.cls);
        eA.set('title', newState.title);
        //Ngn.addTips(eA);
      }
    }).send();
  });
};

Ngn.Btn.Action = new Class({
  action: function() {}
});

Ngn.Btn.addAction = function(selector, action, parent) {
  var esBtn = (parent ? parent : document).getElements(selector);
  if (!esBtn) return;
  esBtn.each(function(eBtn) {
    action = action.pass(eBtn);
    eBtn.addEvent('click', function(e) {
      e.preventDefault();
      action(e);
    });
  });
};

Ngn.Btn.opacity = function(eBtn, outOp, overOp) {
  var fx = new Fx.Morph(eBtn, { duration: 'short', link: 'cancel' });
  if (!outOp != undefined) outOp = 0.4;
  if (!overOp != undefined) overOp = 1;
  eBtn.setStyle('opacity', outOp);
  eBtn.addEvent('mouseover', function() {
    fx.start({'opacity': [outOp, overOp]});
  });
  eBtn.addEvent('mouseout', function() {
    fx.start({'opacity': [overOp, outOp]});
  });
  return eBtn;
};

Ngn.Btn.addAjaxAction = function(eBtn, action, onComplete) {
  if (!eBtn) return;
  onComplete = onComplete ? onComplete : Function.from();
  eBtn.addEvent('click', function(e) {
    e.preventDefault();
    if (eBtn.hasClass('confirm') && !Ngn.confirm()) return;
    if (eBtn.hasClass('loading')) return;
    if (eBtn.retrieve('disabled')) return;
    eBtn.addClass('loading');
    new Ngn.Request({
      url: eBtn.get('href').replace(action, 'ajax_' + action),
      onComplete: function() {
        onComplete();
        eBtn.removeClass('loading');
      }
    }).send();
  });
};
