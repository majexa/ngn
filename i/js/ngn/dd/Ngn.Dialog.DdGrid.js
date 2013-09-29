Ngn.Dialog.DdGrid = new Class({
  Extends: Ngn.Grid.Dialog.Request,

  options: {
    title: 'Редактирование записей',
    gridClass: Ngn.DdGrid
  },

  initialize: function(options) {
    this.options.gridOpts.basePath = '/admin/ddItems/' + options.strName;
    this.parent(options);
  }

});