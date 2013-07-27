Ngn.TreeEdit.Tags = new Class({
  Extends: Ngn.TreeEdit,
  
  options: {
    id: 'tags',
    enableStorage: false,
    actionUrl: '/admin/tags',
    folderOpenIcon: 'mif-tree-tag-open-icon',
    folderCloseIcon: 'mif-tree-tag-close-icon',
    pageIcon: 'mif-tree-tag-close-icon'
  },
  
  initUrl: function() {
    this.url = this.options.actionUrl + '/' + this.groupId;
  },
  
  initialize: function(container, groupId, options) {
    this.groupId = groupId;
    this.parent(container, options);
  },
  
  toggleButtons: function() {
    this.parent();
    this.toggleButton('add', true);
  }
  
});
