Ngn.Grid.Search = new Class({

  initialize: function(grid) {
    this.grid = grid;
    this.init();
  },

  init: function() {
    var timeoutId = 0;
    var eSearch = new Element('input', {
      placeholder: Ngn.Locale.get('Core.search') //
        + '...'
    }).inject(this.grid.eMenu.getElement('.clear'), 'before');
    eSearch.addEvent('keyup', function() {
      clearTimeout(timeoutId);
      timeoutId = this.loadSearchResults.delay(1000, this, eSearch.get('value'));
    }.bind(this));
  },

  word: null,

  loadSearchResults: function(word) {
    if (this.word == word) return;
    this.word = word;
    Ngn.Request.Iface.loading(true);
    new Ngn.Request.JSON({
      url: this.grid.options.basePath + '/json_search?word=' + word,
      onComplete: function(r) {
        this.grid.initInterface(r, true);
        Ngn.Request.Iface.loading(false);
      }.bind(this)
    }).send();
  }

});