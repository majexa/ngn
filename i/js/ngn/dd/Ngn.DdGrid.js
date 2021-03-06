Ngn.DdGrid = new Class({
  Extends: Ngn.Grid,

  options: {
    reloadOnDelete: false
  },

  fieldNames: [],

  initInterface: function(data, fromAjax) {
    console.trace(data);

    this.fieldNames = data.fieldNames;
    this.parent(data, fromAjax);
  },

  selectChange: function() {
    new Ngn.Request.Loading({
      url: this.getLink() + '?a=ajax_updateField&field=' + el.get('name') + '&value=' + el.get('value') + '&' + this.options.idParam + '=' + itemId,
      onComplete: function() {
        this.reload(itemId);
      }.bind(this)
    }).send();
  },

  initItems: function() {
    this.options.eItems.getElements('select').each(function(el) {
      var itemId = el.getParent('.item').get('data-id');
      el.addEvent('change', function() {
        this.selectChange(el, itemId);
      }.bind(this));
    }.bind(this));


    /*
     this.options.eItems.getElements('.iconFlag').each(function(el) {
     var itemId = el.getParent('.item').get('data-id');
     var field = this.fieldNames[el.getParent('.' + this.options.valueContainerClass).get('data-n')];
     var title = el.get('title');
     if (title.test('/')) {
     var r = title.split('/');
     var titleOn = r[0];
     var titleOff = r[1];
     } else {
     var titleOn = title + ' (включить)';
     var titleOff = title + ' (выключить)';
     }
     new Ngn.SwitcherLink(el, {
     classOn: 'flagOn',
     classOff: 'flagOff',
     titleOn: titleOn,
     titleOff: titleOff,
     linkOn: this.getLink() + '?a=ajax_changeState&field=' + field + '&state=1&' + this.options.idParam + '=' + itemId,
     linkOff: this.getLink() + '?a=ajax_changeState&field=' + field + '&state=0&' + this.options.idParam + '=' + itemId,
     onComplete: function(enabled) {
     this.loading(itemId, false);
     this.fireEvent('reloadComplete', itemId);
     }.bind(this),
     onClick: function() {
     this.loading(itemId, true);
     }.bind(this)
     });
     }.bind(this));
     */
    this.parent();
  },

  initMenu: function() {
    this.parent();
    var timeoutId = 0;
    var eSearch = new Element('input').inject(this.eMenu.getElement('.clear'), 'before');
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
      url: this.options.basePath + '/json_search?word=' + word,
      onComplete: function(r) {
        this.initInterface(r, true);
        Ngn.Request.Iface.loading(false);
      }.bind(this)
    }).send();
  }

});