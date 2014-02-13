Ngn.Form.El.DdTagsMultiselectDropdown = new Class({
  Extends: Ngn.Form.El.Dd,

  title: 'Выбрать',

  init: function() {
    this.name = Ngn.frm.getPureName(this.eRow.getElement('input').get('name'));
    this.eFieldWrapper = this.eRow.getElement('.field-wrapper');
    this.eChbxs = this.eFieldWrapper.getFirst();
    this.eChbxs.addClass('multiselect-dropdown');
    this.eOption = new Element('option');
    this.toggle();
    this.updateCount();

    new Element('select').grab(this.eOption).inject(this.eFieldWrapper, 'top');
    this.eSelectOverride = new Element('div', {'class': 'select-override'}).inject(this.eFieldWrapper, 'top');
    this.eOkBtn = (new Element(
      'div', {
        'class': 'iconsSet',
        html: '<div class="image-button ok"><a id="time_ok" href="javascript:void(0)" class="btn" tabindex="0"><span><i></i>OK</span></a></div>',
        events: {
          click: function(e){
            e.preventDefault();
            this.toggle();
          }
        }
      }
    )).inject(this.eChbxs, 'bottom');

    this.eChbxs.getElements('input').addEvent('click', function(e) {
      this.updateCount();
    }.bind(this));

    this.eSelectOverride.addEvent('click', function(e) {
      e.preventDefault();
      this.toggle();
    }.bind(this));
    this.eOkBtn.cloneEvents(this.eSelectOverride);
  },

  toggle: function() {
    this.eChbxs.style.left = (this.eChbxs.style.left == '-1111111111px') ? '' : '-1111111111px'
  },

  updateCount: function() {
    this.eOption.set('text',this.title+' ('+this.eChbxs.getElements('input:checked').length+')');
  }
});
