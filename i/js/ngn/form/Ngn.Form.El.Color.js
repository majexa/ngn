Ngn.Form.El.Color = new Class({
  Extends: Ngn.Form.El,

  init: function() {
    var el = this.eRow;
    var eColor = el.getElement('div.color');
    var eInput = el.getElement('input').addClass('hexInput');
    eInput.addEvent('change', function() {
      eColor.setStyle('background-color', eInput.value);
    });
    new Ngn.Rainbow(eInput, {
      eParent: eInput.getParent(),
      eColor: eColor,
      id: 'rainbow_' + eInput.get('name'),
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