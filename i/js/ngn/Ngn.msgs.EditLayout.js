Ngn.msgs.EditLayout = new Class({
  
  initialize: function(id, elEdit, objMsg) {
    if (!elEdit) return;
    this.objMsg = objMsg;
    elEdit.getElements('a').each(function(btn, i) {
      btn.addEvent('click', function(e) {
        e.preventDefault();
        var method = 'this.objMsg.action_' + btn.getProperty('class');
        if (eval(method) != undefined) {
          eval(method+'(id)');
        }
      }.bind(this));
    }.bind(this));
  }
  
});
