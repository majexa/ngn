// singletone
Ngn.Dialog.Queue = new Class({
  Implements: [Options, Events],

  options: {
    onComplete: Function.from()
  },
  
  current: 0,
  shade: null,

  /**
   * @param array Массив формата: [ [ Ngn.Dialog..., { ... } ], ... ]
   * @param options
   */
  initialize: function(queue, options) {
    if (Ngn.Dialog.queue != undefined) new Error('You can create only one queue instance');
    Ngn.Dialog.queue = this;
    this.queue = queue;
    this.setOptions(options);
    this.openCurrent();
  },
  
  add: function(item) {
    this.queue[this.current+1] = item;
  },
  
  getNextOptions: function() {
    return (this.queue[this.current+1] != undefined) ?
      this.queue[this.current+1][1] : false;
  },
  
  openCurrent: function() {
    this.queue[this.current][1] = Object.merge(this.getDialogOptions(), this.queue[this.current][1]);
    new this.queue[this.current][0](this.queue[this.current][1]);
    if (this.current == 0) {
      this.shade = new Ngn.Dialog.Shade();
      this.shade.openShade();
    }
  },
  
  getDialogOptions: function() {
    return {
      force: false, // тень отдельным объектом включается
      onCancelClose: function() {
        this.shade.closeShade();
      }.bind(this),
      onOkClose: function() {
        //c('onOkClose added by queue');
        console.debug('queue length: '+this.queue.length);
        if (this.current == this.queue.length-1) {
          this.shade.closeShade();
          this.fireEvent('complete');
          return;
        }
        this.current++;
        console.debug('openCurrent');
        this.openCurrent();
      }.bind(this)
    };
  }
  
});
