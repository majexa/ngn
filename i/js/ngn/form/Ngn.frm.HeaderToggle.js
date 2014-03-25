Ngn.frm.HeaderToggle = new Class({
  Implements: [Options, Events],

  opened: false,

  initialize: function(eBtn, options) {
    this.setOptions(options);
    this.eBtn = eBtn;
    this.eHeader = this.eBtn.getParent();
    this.eToggle = this.eBtn.getParent().getParent();
    this.eHeader.inject(this.eToggle, 'before');
    var saved = Ngn.storage.get(eBtn.get('data-name'));
    if (saved == undefined) this.toggle(this.opened); else this.toggle(saved);
    this.eBtn.addEvent('click', function(e) {
      e.preventDefault();
      this.toggle(!this.opened);
      Ngn.storage.set(this.eBtn.get('data-name'), this.opened);
    }.bind(this));
  },

  toggle: function(opened) {
    opened ? this.eHeader.removeClass('headerToggleClosed') : this.eHeader.addClass('headerToggleClosed');
    if (this.eBtn.get('tag') == 'input') this.eBtn.set('value', '  ' + (opened ? '↑' : '↓') + '  ');
    this.eToggle.setStyle('display', opened ? 'block' : 'none');
    this.opened = opened;
    this.fireEvent('toggle', opened);
  }

});


Ngn.frm.headerToggleFx = function(btns) {
  btns.each(function(btn) {
    var eToggle = btn.getParent().getParent();
    btn.getParent().inject(eToggle, 'before');
    var setArrow = function(opened) {
      btn.set('value', '  ' + (opened ? '↑' : '↓') + '  ');
    };
    var fx = new Fx.Slide(eToggle, {
      duration: 300,
      transition: Fx.Transitions.Pow.easeOut,
      onComplete: function() {
        setArrow(opened);
        Ngn.storage.set(btn.get('data-name'), opened ? 1 : 0);
      }
    });
    var opened = true;
    var saved = Ngn.storage.get(btn.get('data-name'));
    if (!saved || saved == 0) {
      fx.hide();
      opened = false;
    }
    if (saved != undefined) setArrow(opened);
    btn.addEvent('click', function(e) {
      e.preventDefault();
      opened ? fx.slideOut() : fx.slideIn();
      opened = !opened;
    });
  });
};