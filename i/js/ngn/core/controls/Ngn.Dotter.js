Ngn.Dotter = new Class({
  Implements: [Options,Events],

  options: {
    delay: 500,
    dot: '.',
    message: 'Загрузка',
    numDots: 10,
    property: 'text',
    reset: false/*,
    onDot: Function.from(),
    onStart: Function.from(),
    onStop: Function.from()
    */
  },

  initialize: function(container, options) {
    this.setOptions(options);
    this.container = document.id(container);
    this.dots = 0;
    this.running = false;
  },

  dot: function() {
    if(this.running) {
      var text = this.container.get(this.options.property);
      this.dots++;
      this.container.set(this.options.property,(this.dots % this.options.numDots != 0 ? text : this.options.message) + '' + this.options.dot);
    }
    return this;
  },

  load: function() {
    this.loaded = true;
    this.dots = 0;
    this.dotter = function(){ this.dot(); this.fireEvent('dot'); }.bind(this);
    this.periodical = this.dotter.periodical(this.options.delay);
    this.container.set(this.options.property,this.options.message + '' + this.options.dot);
    return this;
  },

  start: function() {
    if(!this.loaded || this.options.reset) this.load();
    this.running = true;
    this.fireEvent('start');
    return this;
  },

  stop: function() {
    this.running = this.loaded = false;
    clearTimeout(this.periodical);
    this.fireEvent('stop');
    return this;
  }

});