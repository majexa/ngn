Ngn.TreeEdit.Tags = new Class({
  Extends: Ngn.TreeEdit,
  
  options: {
    id: 'tags',
    actionUrl: '/admin/tags',
    folderOpenIcon: 'ngn-tree-tag-open-icon',
    folderCloseIcon: 'ngn-tree-tag-close-icon',
    pageIcon: 'ngn-tree-tag-close-icon'
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
