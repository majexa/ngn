Ngn.ItemsEdit = new Class({

  baseUrl: function() {
    return window.location.pathname;
  },

  reloadItem: function(id) {
    window.location.reload();
  }

});
