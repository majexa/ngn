Ngn.Dialog.Queue.Request.Form = new Class({
  Extends: Ngn.Dialog.Queue.Request,
  
  getDialogOptions: function() {
    return Object.merge(this.parent(), {
      onSubmitSuccess: function(r) {
        if ($defined(r.nextFormUrl))
          Ngn.Dialog.queue.add({ url: r.nextFormUrl });
      }
    });
  },
  
  add: function(item) {
    this.parent([Ngn.Dialog.RequestForm, item]);
  }
  
});
