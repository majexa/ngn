Ngn.UlMenu = new Class({
  Implements: [Options],

  options: {
    containerClass: 'bcont',
    containerTag: 'div'
  },

  initialize: function(element, options) {
    this.setOptions(options);
    this.element = document.id(element);
    this.element.getChildren('li > ul').each(function(eUl) {
      eUl.setStyle('display', 'none');
    });
    this.element.getElements('li').each(function(eLi) {
      var eA = eLi.getElement('a');
      if (Ngn.Storage.bget('ulMenu' + eLi.get('id'))) {
        eLi.addClass('opened');
        var eUl = eA.getNext();
        if (eUl) {
          eUl.setStyle('display', 'block');
        }
      }
      if (eLi.hasClass('active')) {
        var ePar = eLi.getParent();
        while (true) {
          //if (!ePar) break;
          if (ePar.get('class') == this.options.containerClass && ePar.get('tag') == this.options.containerTag) break;
          if (ePar.get('tag') == 'ul') ePar.setStyle('display', 'block');
          ePar = ePar.getParent();
        }
      }
      eA.addEvent('click', function() {
        var eUl = eA.getNext();
        if (!eUl) return;
        if (eUl.getStyle('display') == 'none') {
          eUl.setStyle('display', 'block');
          eLi.addClass('opened');
          Ngn.Storage.set('ulMenu' + eLi.get('id'), true);
        } else {
          eUl.setStyle('display', 'none');
          eLi.removeClass('opened');
          Ngn.Storage.remove('ulMenu' + eLi.get('id'), false);
        }
        return false;
      });
    }.bind(this));
  }

});
