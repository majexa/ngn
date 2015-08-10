//Ngn.toObj('Ngn.DdTags.Dialog');
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
    basePath: '/admin/tags'
    //data: {
    //  groupId: null
    //}
  },

  buildMessage: function() {
    return Elements.from('\
<div>\
  <div class="treeMenu iconsSet">\
    <small>\
      <a href="#" class="add dgray"><i></i>Создать</a>\
      <a href="#" class="rename dgray"><i></i>Переименовать</a>\
      <a href="#" class="delete dgray"><i></i>Удалить</a>\
    </small>\
    <div class="clear"><!-- --></div>\
  </div>\
  <div class="treeContainer"></div></div>\
</div>\
    ')[0];
  },

  init: function() {
    this.eTreeContainer = this.message.getElement('.treeContainer');
    this.eTreeMenu = this.message.getElement('.treeMenu');
    var tree = new Ngn.TreeEdit.Tags(
      this.eTreeContainer,
      this.options.data.groupId,
      {
        actionUrl: this.options.basePath,
        buttons: this.eTreeMenu,
        onUpdate: function() {
          //this.updateBlock();
        }.bind(this)
      }
    ).init();
    tree.addEvent('dataLoad', function() {
      //this.eTreeContainer.setStyle('height', (this.message.getSize().y - this.eTreeMenu.getSize().y) + 'px');
      this.eTreeContainer.setStyle('height', '200px');
      this.initReduceHeight(true);
      //tree.toggleAll(true);
    }.bind(this));
  }

});

Ngn.DdTags.Dialog.Flat = new Class({
  Extends: Ngn.Grid.Dialog.Request,

  options: {
    basePath: '/admin/tags',
    gridOpts: {
      isSorting: true,
      menu: [
        {
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
        }
      ],
      toolActions: {
        edit: Ngn.Items.toolActions.inlineTextEdit
        //delete: Ngn.Items.toolActions.delete
      }
    }
  },

  initialize: function(opt) {
    c(opt);
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