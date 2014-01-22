Ngn.DdGrid = new Class({
  Extends: Ngn.Grid,

  options: {
    reloadOnDelete: false
  },

  fieldNames: [],

  initInterface: function(data, fromAjax) {
    this.fieldNames = data.fieldNames;
    this.parent(data, fromAjax);
  },

  initItems: function() {
    this.options.eItems.getElements('select').each(function(el) {
      var itemId = el.getParent('.item').get('data-id');
      el.addEvent('change', function() {

        new Ngn.Request.Loading({
          url: this.getLink() + '?a=ajax_updateField&field=' + el.get('name') + '&value=' + el.get('value') + '&' + this.options.idParam + '=' + itemId,
          onComplete: function() {
            this.reload(itemId);
          }.bind(this)
        }).send();

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
  }

});