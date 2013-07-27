Ngn.toObj('Ngn.pb');

Ngn.pb.Interface = new Class({
  Implements: [Options],
  
  options: {
    wrapperSelector: '#blocks',
    //colSelector: '.col',
    //colBodySelector: '.blocksBody',
    handler: '.dragBox',
    controllerPath: '/admin/pageBlocks',
    simple: true,
    disableDeleteBtn: false,
    colBodySelector: '.blocksBody'
  },

  initialize: function(options) {
    this.setOptions(options);
    this.eWrapper = document.getElement(this.options.wrapperSelector);
    this.initCols();
    this.initBlocks();
    this.initSortables();
  },
  
  cols: [],

  allowGlobalBtn: false,
  
  initCols: function() {
    var i = 0;
    this.eWrapper.getElements('.col').each(function(eCol) {
      new Ngn.pb.Col(eCol, this);
      if (eCol.hasClass('ct_content')) this.allowGlobalBtn = true;
      // -------------------
      var eColBody = eCol.getElement(this.options.colBodySelector);
      if (!eColBody) return;
      if (!eCol.hasClass('blocksNotAllowed')) {
        this.cols[i] = eColBody;
        i++;
      }
    }.bind(this));
    Ngn.equalItemHeights(this.cols, true);
  },

  initSortables: function() {
    this.sortables = new Sortables(this.cols, {
      revert: true,
      clone: true,
      handle: this.options.handler
    });
    this.sortables.addEvent('start', function(el, clone){
      clone.setStyle('z-index', 9999);
      clone.addClass('move');
    });
    this.sortables.addEvent('stop', function(el, clone){
      el.removeClass('nonActive');
      clone.removeClass('move');
    });
    // Строка отвечающая за изменение порядка
    this.orderState = this.sortables.serialize().join(',');
    this.sortables.addEvent('complete', function(el, clone){
      var cols = this.sortables.serialize(false, function(eBlock, index){
        if (!eBlock.get('id')) return null;
        return {
          id: eBlock.get('id').replace('block_', ''),
          colN: eBlock.getParent('.col').get('id').replace('col', '')
        }
      }.bind(this));
      if (this.cols.length == 1) cols = [cols];
      if (this.orderState == this.sortables.serialize().join(',')) return;
      el.addClass('loading');
      new Request({
        url: this.options.controllerPath + '/ajax_updateBlocks',
        onComplete: function() {
          el.removeClass('loading');
          this.orderState = this.sortables.serialize().join(',');
        }.bind(this)
      }).POST({
        cols: cols
      });
    }.bind(this));
  },
  
  blocks: {},
  
  initBlocks: function() {
    this.eWrapper.getElements('.block').each(function(el) {
      var b = new Ngn.pb.BlockEdit(el, this);
      this.blocks[b.id] = b;
    }.bind(this));
  }

});

Ngn.pb.positionRight = function(el, eCol, padSelector) {
  var colW = eCol.getSizeWithoutPadding().x;
  var eFirstPageBlockEdit = eCol.getElement(padSelector);
  var eb1W = eFirstPageBlockEdit ? eFirstPageBlockEdit.getSize().x : 0;
  var selfW = el.getSize().x;
  el.setStyle('left', colW - eb1W - selfW - 6 - 5);
};

Ngn.pb.Col = new Class({

  initialize: function(eCol, interface) {
    this.interface = interface;
    this.eCol = eCol;
    this.eBtns = new Element('div', {
      'class': 'editBlock smIcons bordered blue'
    }).inject(eCol, 'top');
    if (eCol.hasClass('allowBlocks')) this.addBtnCreate();
  },

  addBtnCreate: function() {
    Ngn.btn1('Добавить блок в ' + this.eCol.get('data-n') + '-ю колонку', 'add').inject(this.eBtns).addEvent('click', function(e){
      e.preventDefault();

      // новый вариант
      new Ngn.Dialog.RequestForm({
        url: this.interface.options.controllerPath+'/json_newBlock'+
          (this.interface.options.simple ? 'Simple' : '')+
          '/'+this.eCol.get('data-n'),
        onComplete2: function() {
          c('!!!');
          window.location.reload(true);
        }
      });

      /*
      new Ngn.Dialog.Queue.Request.Form({
        url: this.interface.options.controllerPath+'/json_newBlock'+
          (this.interface.options.simple ? 'Simple' : '')+
          '/'+this.eCol.get('data-n')
      }, {
        onComplete: function() {
          window.location.reload(true);
        }
      });
      */
    }.bind(this));
  }

});

Ngn.pb.el = function(data) {

  //new Element('div', {

  //});
};

Ngn.pb.BlockEdit = new Class({

  /**
   * @var block element
   * @var Ngn.pb.Interface
   */
  initialize: function(eBlock, interface) {
    this.interface = interface;
    this.eBlock = eBlock;
    //if () throw new Ngn.EmptyError('block id');
    var id = this.eBlock.get('id');
    if (!id) return;
    this.id = this.eBlock.get('id').replace('block_', '');
    this.initEditBlock();
  },
  
  getDialogOptions: function(type) {
    if (Ngn.pb.dialogOptions[type]) return Ngn.pb.dialogOptions[type];
    return {};
  },
  
  initEditBlock: function() {
    var el = this.eBlock;
    var type = el.get('class').replace(/.* pbt_(\w+) .*/, '$1');
    Elements.from(Ngn.tpls.editBlock)[0].inject(el, 'top');
    el.getElement('.actv').dispose();
    this.eEditBlock = el.getElement('.editBlock');
    var eDragBox = Elements.from('<div class="dragBox"></div>')[0].inject(el, 'top');
    // Выравниваем editBlock по правому краю
    (function(){
      Ngn.setToTopRight(this.eEditBlock, el, [6, 0]);
      Ngn.setToTopRight(eDragBox, el, [this.eEditBlock.getSize().x + 15, 5]);
    }.bind(this)).delay(100);
    var btnEdit = el.getElement('a[class~=edit]');
    if (btnEdit) {
      btnEdit.set('href', '#');
      btnEdit.set('title', 'Редактировать блок');
      btnEdit.addEvent('click', function(e){
        e.preventDefault();
        new Ngn.Dialog.RequestForm($merge({
          url: this.interface.options.controllerPath + '/json_editBlock/' + this.id,
          width: 400,
          onSubmitSuccess: function() {
            this.reload();
          }.bind(this)
        }, this.getDialogOptions(type))).show();
      }.bind(this));
    }
    var btnDelete = el.getElement('a[class~=delete]');
    if (btnDelete) {
      //if (this.interface.options.disableDeleteBtn) {
      //  btnDelete.dispose();
      //  return;
      //}
      btnDelete.addEvent('click', function(e){
        e.preventDefault();
        if (!confirm('Вы уверены?')) return;
        Ngn.loading(true);
        new Request({
          url: this.interface.options.controllerPath + '/ajax_deleteBlock',
          onComplete: function() {
            Ngn.loading(false);
            el.destroy();
          }
        }).get({
          blockId: this.id
        });
      }.bind(this));
    }
    if (this.interface.allowGlobalBtn) {
      Ngn.btn1Flag(parseInt(this.eBlock.get('data-global')), {
        title: 'Сделать локальным',
        cls: 'global',
        url: this.interface.options.controllerPath + '/ajax_setGlobal/' + this.id + '/' + 0
      }, {
        title: 'Сделать глобальным',
        cls: 'local',
        url: this.interface.options.controllerPath + '/ajax_setGlobal/' + this.id + '/' + 1
      }).inject(btnDelete, 'after');
    }
  },
  
  reload: function() {
    this.eBlock.addClass('loading');
    new Ngn.Request({
      url: this.interface.options.controllerPath + '/ajax_getBlock/' + this.id,
      onComplete: function(html) {
        this.eBlock.removeClass('loading');
        this.eBlock.getElement('.bcont').set('html', html);
      }.bind(this)
    }).get();
  },
  
  replaceEditBlockBtn: function(opts, func) {
    var eCur = this.eEditBlock.getElement('.'+opts.name);
    if (eCur) eCur.dispose();
    this.addEditBlockBtn(opts, func);
  },
  
  addEditBlockBtn: function(opts, func) {
    return;
    //throw new Error('watch to my trace');
    Ngn.btn({});
    c(opts);
    // cls, title, prop, caption
    Elements.from(Ngn.smBtn(opts))[0].inject(this.eEditBlock, 'top').addEvent('click', function(e) {
      e.preventDefault();
      func();
    });
  }

});

Ngn.pb.dialogOptions = {};

Ngn.pb.dialogOptions.text = {
  vResize: Ngn.Dialog.VResize.Wisiwig,
  width: 450
};

Ngn.pb.dialogOptions.textAndImage = {
  width: 450
};

/*
Ngn.pb.Text = new Class({
  initialize: function(eForm) {
    Ngn.dialogs.getValues().getLast().dialog.setStyle('width', '800px');
    //Ngn.dialogs.getValues().getLast().message.setStyle('height', '600px');
    Ngn.dialogs.getValues().getLast().screenСenter();
    eForm.getElement('.type_wisiwig').setStyle('padding', 0);
    eForm.getElement('.type_wisiwig > p.label').setStyle('display', 'none');
  }
});
*/
