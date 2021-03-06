//Ngn.Object.fromString('Ngn.DdTags.Dialog');
Ngn.DdTags = {};
Ngn.DdTags.Dialog = {};

Ngn.DdTags.dialog = function(row) {
  var opt = {
    data: {
      groupId: row.tagGroup.id
    }
  };
  return row.tagGroup.tree ? new Ngn.DdTags.Dialog.Tree(opt) : new Ngn.DdTags.Dialog.Flat(opt);
};

Ngn.DdTags.Dialog.Tree = new Class({
  Extends: Ngn.Dialog.TreeEdit,

  options: {
    id: 'editTags',
    title: 'Редактирование рубрик',
    basePath: '/admin/tags',
    menu: [{
      name: 'add',
      title: 'Создать'
    }, {
      name: 'rename',
      title: 'Переименовать'
    }, {
      name: 'delete',
      title: 'Удалить'
    }]
    //data: {
    //  groupId: null
    //}
  },

  buildMessage: function() {
    var html = '<div>\
  <div class="treeMenu iconsSet">\
    <small>\
    ';
    for (var i = 0; i < this.options.menu.length; i++) {
      html += '<a href="#" class="' + this.options.menu[i].name + ' dgray"><i></i>' + this.options.menu[i].title + '</a>'
    }
    html += '</small>\
    <div class="clear"><!-- --></div>\
  </div>\
  <div class="treeContainer"></div></div>\
</div>\
    ';
    return Elements.from(html)[0];
  },

  init: function() {
    this.eTreeContainer = this.message.getElement('.treeContainer');
    this.eTreeMenu = this.message.getElement('.treeMenu');
    var treeEditClass = this.getTreeEditClass();
    var treeEdit = new treeEditClass(this.eTreeContainer, this.options.data.groupId, {
        actionUrl: this.options.basePath,
        buttons: this.eTreeMenu,
        onUpdate: function() {
          //this.updateBlock();
        }.bind(this)
      }).init();
    treeEdit.addEvent('dataLoad', function() {
      //this.eTreeContainer.setStyle('height', (this.message.getSize().y - this.eTreeMenu.getSize().y) + 'px');
      this.eTreeContainer.setStyle('height', '200px');
      this.initReduceHeight(true);
      //treeEdit.toggleAll(true);
    }.bind(this));
  },

  getTreeEditClass: function() {
    return Ngn.TreeEdit.Tags;
  }

});

Ngn.DdTags.Dialog.Flat = new Class({
  Extends: Ngn.Grid.Dialog.Request,

  options: {
    basePath: '/admin/tags',
    gridOpts: {
      isSorting: true,
      menu: [{
        title: 'Добавить тэг',
        cls: 'add',
        action: function(grid) {
          var title = prompt('Введите название');
          if (title) {
            new Ngn.Request({
              url: grid.options.basePath + '?a=ajax_create',
              onComplete: function() {
                grid.reload();
              }.bind(this)
            }).post({
                title: title
              });
          }
        }
      }],
      toolActions: {
        edit: Ngn.Items.toolActions.inlineTextEdit
        //delete: Ngn.Items.toolActions.delete
      }
    }
  },

  initialize: function(opt) {
    console.debug(opt);
    console.trace('***');
    this.parent(opt);
  },

  setOptions: function(options) {
    this.parent(options);
    if (!this.options.basePath) throw new Error('this.options.basePath not defined');
    this.options.gridOpts.basePath = this.options.basePath + '/' + options.data.groupId;
    //this.options.gridOpts.url = this.options.basePath + '/json_list'
    //this.options.gridOpts.listAjaxAction = 'json_list';
  }

});