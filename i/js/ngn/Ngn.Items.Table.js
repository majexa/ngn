Ngn.Items.Table = new Class({
  Extends: Ngn.Items,

  options: {
    eItems: 'itemsTable',
    itemElementSelector: 'tbody tr',
    isSorting: true,
    handle: '.dragBox',
    onMoveComplete: $empty,
    basePath: window.location.pathname
  },

  initSorting: function() {
    if (!this.options.isSorting) return;
    var sortablesOptions = {};
    if (this.options.handle) {
      sortablesOptions.handle = this.options.handle;
      var dragBoxes = this.eItems.getElements(this.options.handle);
      dragBoxes.each(function(el) {
        el.addEvent('mouseover', function() {
          el.addClass('over');
        });
        el.addEvent('mouseout', function() {
          el.removeClass('over');
        });
      });
    }
    this.ST = new Sortables(this.eItemsTableBody, sortablesOptions);
    this.dragStarts = false;
    this.orderState = this.ST.serialize().join(',');
    this.ST.addEvent('complete', function(el, clone) {
      el.removeClass('move');
      // Если в процессе переноса или если положение не изменилось
      if (!this.dragStarts || this.orderState == this.ST.serialize().join(',')) return;
      el.addClass('loading');
      new Request({
        url: this.options.basePath + '?a=ajax_reorder',
        onComplete: function() {
          this.dragStarts = false;
          //this.orderState = this.ST.serialize(false, function(el) { return el.get('data-id') }).join(',');
          this.orderState = this.ST.serialize().join(',');
          el.removeClass('loading');
          this.fireEvent('moveComplete');
        }.bind(this)
      }).POST({
          'ids': this.ST.serialize(false, function(el) {
            return el.get('data-id');
          })
        });
    }.bind(this));
    this.ST.addEvent('start', function(el, clone) {
      this.dragStarts = true;
      el.addClass('move');
    }.bind(this));
    if (!this.options.handle) {
      // Подсвечивать строку только в том случае если нет специального бокса дял переноса
      this.eItemsTableBody.addEvents({
        'mousedown': function(e) {
          this.eItemsTableBody.set('styles', {'cursor': 'move'});
        }.bind(this),
        'mouseup': function(e) {
          this.eItemsTableBody.set('styles', {'cursor': 'auto'});
        }.bind(this)
      });
      this.eItemsTableBody.getElements('tr').each(function(el, i) {
        el.addEvents({
          'mousedown': function(e) {
            el.addClass('move');
          },
          'mouseup': function(e) {
            el.removeClass('move');
          }
        });
      });
    }
  },

  initItems: function() {
    this.parent();
    this.eItemsTableBody = this.eItems.getElement('tbody');
    Ngn.fixEmptyTds(this.eItemsTableBody);
    this.initSorting();
  }

});

Ngn.ColResizer = new Class({

  initialize: function(eHandler, n, grid) {
    var initW;
    //this.eSpacer = eSpacer;
    //c(grid.esTh);
    var eWidth = grid.esTh[n - 1];
    if (!eWidth) return;
    //c(eWidth.getSize().x);
    new Drag(eHandler, {
      modifiers: {
        x: 'left'
      },
      snap: 0,
      onStart: function() {
        //c(n-1);
        //c();
        eWidth.store('initW', eWidth.getSize().x);
        grid.initThSizes();

        //initW = eSpacer.getSize().x;
        //c(initW);
      },
      onDrag: function() {
        var offset = parseInt(eHandler.getStyle('left'));
        //var before = this.eSpacer.getSize().x;
        eWidth.setStyle('width', (eWidth.retrieve('initW') + offset) + 'px');
        //c([eWidth.retrieve('initW'), eWidth.getStyle('width'), offset, eWidth.retrieve('initW') + offset]);
        //c([before, offset, this.eSpacer.getSize().x]);
      }.bind(this)
    });
  }

});

Ngn.Items.toolActions = {

  switcher: {
    init: function(items, cls, row) {
      row.tools[cls].on = !!parseInt(row.tools[cls].on);
      if (!Ngn.Items.toolActions.switcher.switchers[cls]) throw new Error('Ngn.Items.switcher[' + cls + '] not defined');
      var switcher = Ngn.Items.toolActions.switcher.switchers[cls];
      if (switcher.initRowEl) switcher.initRowEl(row);
      var switcherOpts = switcher.getOptions(items, row);
      var el = new Element('a', {
        'href': '#',
        'class': 'iconBtn ' + (row.tools[cls].on ? switcherOpts.classOn : switcherOpts.classOff),
        'html': '<i></i>',
        'title': row.tools[cls].on ? switcherOpts.titleOn : switcherOpts.titleOff
      }).inject(new Element('td').inject(row.eTools));
      var switcher = new Ngn.SwitcherLink(el, switcherOpts);
      //switcher.addEvent('click', items.loading.pass([row.id, true], items));
      switcher.addEvent('click', function() {
        items.loading(row.id, true);
      });
      switcher.addEvent('complete', items.loading.pass([row.id, false], items));
    },
    switchers: {
      active: {
        /**
         * @param Ngn.Items
         * @param items data row
         * @return {Object}
         */
        getOptions: function(items, row) {
          return {
            classOn: 'activate',
            classOff: 'deactivate',
            linkOn: items.getLink() + '?a=ajax_activate&' + items.options.idParam + '=' + row.id,
            linkOff: items.getLink() + '?a=ajax_deactivate&' + items.options.idParam + '=' + row.id,
            onComplete: function(enabled) {
              items.fireEvent('reloadComplete', row.id);
              enabled ? items.esItems[row.id].removeClass('nonActive') : items.esItems[row.id].addClass('nonActive');
            }
          };
        },
        initRowEl: function(row) {
          if (!parseInt(row.active)) row.el.addClass('nonActive');
        }
      }
    }
  },

  /*
  'delete': {
    init: function(items, cls, row) {
      items.createToolBtn(cls, row, function() {
        if (!Ngn.confirm()) return;
        new Ngn.Request({
          url: items.options.basePath + '?a=ajax_delete'
        }).post(items.idP(row.id));
      });
    }
  },
  */

  inlineTextEdit: {
    init: function(items, cls, row) {
      Ngn.Items.toolActions.inlineTextEdit.initTd(items, cls, row, row.tools[cls].elN + 1);
    },
    initTd: function(items, cls, row, n) {
      var data = Object.values(row.data);
      var eTd = row.el.getChildren('td')[n];
      if (!eTd) throw new Ngn.EmptyError('eTd');
      eTd.set('html', '');
      eTd.store('eInput', new Element('input', {
        value: data[n - 1],
        styles: {
          display: 'none',
          'border': '0px',
          'width': '100%'
        }
      }).inject(eTd));
      var f = function() {
        var r = {};
        r[items.options.idParam] = row.id;
        r[row.tools[cls].paramName] = eTd.retrieve('eInput').get('value');
        if (eTd.retrieve('eText').get('html') != r[row.tools[cls].paramName]) {
          eTd.retrieve('eText').set('html', r[row.tools[cls].paramName]);
          new Ngn.Request.JSON({
            url: items.options.basePath + '?a=' + row.tools[cls].action,
            onComplete: function() {
              items.reload();
            }
          }).post(r);
        }
        Ngn.Items.toolActions.inlineTextEdit.switchInput(eTd);
      };
      eTd.retrieve('eInput').addEvent('blur', f).addEvent('keypress', function(e) {
        if (e.key != 'enter') return;
        e.preventDefault();
        f();
      });
      eTd.store('eText', new Element('div', {
        html: data[n - 1]
      }).inject(eTd));
      eTd.store('edit', false);
      items.createToolBtn(cls, row, Ngn.Items.toolActions.inlineTextEdit.switchInput.pass(eTd));
    },
    switchInput: function(eTd) {
      if (eTd.retrieve('edit')) {
        eTd.retrieve('eInput').setStyle('display', 'none');
        eTd.retrieve('eText').setStyle('display', 'block');
        eTd.store('edit', false);
      } else {
        eTd.retrieve('eInput').setStyle('display', 'block');
        eTd.retrieve('eInput').focus();
        eTd.retrieve('eText').setStyle('display', 'none');
        eTd.store('edit', true);
      }
    }
  }

};