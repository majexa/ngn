Ngn.frm.maxLength = function(eForm, defaultMaxLength) {
  eForm.getElements('textarea').each(function(eInput){
    var eLabel = eInput.getParent('.element').getElement('.label');
    var maxlength = eInput.get('maxlength');
    if (!eLabel || !maxlength) return;
    var init = function() {
      eRemained.set('html',
       ' (осталось ' + (maxlength-eInput.get('value').length) + ' знаков из ' + maxlength + ')'
      );
    };
    if (maxlength >= defaultMaxLength) return;
    var eRemained = new Element('small', {
      'class': 'remained gray'
    }).inject(eLabel, 'bottom');
    eInput.addEvent('keyup', init);
    init();
  });
};