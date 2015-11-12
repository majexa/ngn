Ngn.EmptyError = new Class({
  Extends: Error,

  initialize: function(v) {
    this.message = '"' + v + '" can not be empty';
  }

});
