Ngn.Form.El.DdTagsMultiselectDropdown = new Class({
  Extends: Ngn.Form.El.Dd,

  title: 'Выбрать',

  init: function() {
    this.eFieldWrapper = this.eRow.getElement('.field-wrapper');
    this.eChbxs = this.eFieldWrapper.getFirst().addClass('multiselect-dropdown');
    this.eOption = new Element('option', {text: this.title});
    this.toggleDropdown();
    new Element('select').grab(this.eOption).inject(this.eFieldWrapper, 'top');
    this.eSelectOverride = new Element('div', {'class': 'select-override'}).addEvent('click', function(e) {
      e.stopPropagation();
      e.preventDefault();
      this.toggleDropdown();
    }.bind(this)).inject(this.eFieldWrapper, 'top');
    /*
    (new Element(
      'div', {
        'class': 'iconsSet',
        html: '<div class="image-button ok"><a id="time_ok" href="javascript:void(0)" class="btn" tabindex="0"><span><i></i>OK</span></a></div>'
      }
    )).cloneEvents(this.eSelectOverride).inject(this.eChbxs, 'bottom');
    */
    this.eChbxs.getElements('input').addEvent('click', function(e) {
      clearTimeout(this.delayId);
      this.startHide();
      this.updateTitle();
    }.bind(this));
    //window.addEvent('click', function() {
      //this.eChbxs.setStyle('left', '-1111111111px');
    //}.bind(this));
  },

  startHide: function() {
    this.delayId = (function() {
      this.eChbxs.setStyle('left', '-1111111111px');
    }).delay(3000, this);
  },

  toggleDropdown: function() {
    this.eChbxs.style.left = (this.eChbxs.style.left == '-1111111111px') ? '' : '-1111111111px'
  },

  updateTitle: function() {
    var titles = [];
    this.eChbxs.getElements('input:checked').each(function(item) {
      titles.push(item.getNext().get('text'));
    });
    this.eOption.set('text', titles.length > 0 ? titles.join(', '): this.title);
  }

});
