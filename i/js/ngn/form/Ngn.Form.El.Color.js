/**
 * @requires MooRainbow
 */
Ngn.Form.El.Color = new Class({
  Extends: Ngn.Form.El,

  init: function() {
    var el = this.eRow;
    var eColor = el.getElement('div.color');
    var eInput = el.getElement('input').addClass('hexInput');
    c([eColor, eInput]);
    eInput.addEvent('change', function() {
      eColor.setStyle('background-color', eInput.value);
    });
    new MooRainbow(eInput, {
      eParent: eInput.getParent(),
      id: 'rainbow_' + eInput.get('name'),
      //styles: { // и так работает
      //  'z-index': this.options.dialog.dialog.getStyle('z-index').toInt() + 1
      //},
      imgPath: '/i/img/rainbow/small/',
      wheel: true,
      startColor: eInput.value ? new Color(eInput.value).rgb : [255, 255, 255],
      onChange: function(color) {
        eColor.setStyle('background-color', color.hex);
        eInput.value = color.hex;
        eInput.fireEvent('change', color);
      },
      onComplete: function(color) {
        eColor.setStyle('background-color', color.hex);
        eInput.value = color.hex;
        eInput.fireEvent('change', color);
      }
    });
  }

});