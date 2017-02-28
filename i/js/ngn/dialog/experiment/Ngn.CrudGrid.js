Ngn.CrudGrid = new Class({
  Extends: Ngn.Grid,

  options: {
    // modelName: 'modelName',
    crudDialogOptions: {},
    menu: [{
      title: 'Создать',
      cls: 'add',
      action: function (grid) {
        new Ngn.CrudDialogNew(Object.merge(grid.options.crudDialogOptions, {
          url: grid.options.basePath +
          grid.options.restBasePath + '/' +
          grid.options.basicBasePath,
          onOkClose: function () {
            console.log(grid);
            grid.reload();
          }
        }));
      }
    }],
//    requestOptions: {
//      headers: {
//        'Authorization': 'Bearer ' + 123
//      }
//    },
    basePath: null,
    restBasePath: '/api/v1',
    //basicBasePath: 'modelName',
    tools: {
      delete: 'Удалить',
      edit: 'Редактировать'
    },
    toolActions: {
      edit: function (row, opt) {
        window.aaa = new Ngn.CrudDialogEdit(Object.merge(this.options.crudDialogOptions, {
          title: 'Edit',
          url: this.options.basePath +
            this.options.restBasePath + '/' +
            this.options.basicBasePath + '/' + row.id,
          onOkClose: function () {
            this.reload(row.id);
          }.bind(this)
        }));
      }
    }
  },

  initialize: function (options) {
    this.parent(options);
    if (!this.options.basePath) {
      throw new Error('basePath does not defined');
    }
    if (!this.options.basicBasePath) {
      this.options.basicBasePath = this.options.modelName;
    }
  }

});