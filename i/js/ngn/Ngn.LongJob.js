Ngn.LongJob = new Class({
  Implements: [Options, Events],

  options: {
    title: 'Ждите',
    completeText: function(r) {
      return 'Завершено';
    },
    period: 2000
    //action: ''
  },

  initialize: function(options) {
    this.setOptions(options);
    this.status(function(r) {
      if (!r) return;
      if (r.status == 'progress') this.statusCycle();
      else if (r.status == 'complete') {
        this.build();
        this.complete(r);
      }
    }.bind(this));
  },

  build: function() {
    var eParent = this.options.eParent || document.getElement('.longJobs');
    this.el = Elements.from('<div class="item dgray"><div class="cont"><div class="icon-button md-closer"></div><div class="result"></div></div></div>')[0].inject(eParent);
    this.btnClose = this.el.getElement('.icon-button');
    this.eResult = this.el.getElement('.cont .result');
    Ngn.opacityBtn(this.btnClose).addEvent('click', function() {
      this.delete(function() {
        this.started = false;
      }.bind(this));
      this.el.dispose();
      delete this.el;
      clearInterval(this.timer);
    }.bind(this));
  },

  delete: function(callback) {
    new Ngn.Request({
      url: this.options.url + '?a=ajax_ljDelete',
      onComplete: function() {
        if (callback) callback();
      }
    }).send();
  },

  started: false,

  start: function() {
    if (this.started) {
      alert('Операция "' + this.options.title + '" в процессе');
      return;
    }
    new Ngn.Request.JSON({
      url: this.options.url + '?a=json_ljStart'
    }).send();
    this.statusCycle();
    //this.completed ? this.delete(this.statusCycle.bind(this)) : this.statusCycle();
  },

  statusCycle: function() {
    this.started = true;
    if (!this.el) this.build();
    this.eResult.set('html', this.options.title + '...');
    this.el.addClass('hLoader');
    var checkStatus = function() {
      this.status(function(r) {
        if (r.status == 'complete') {
          if (this.timer) clearInterval(this.timer);
          this.complete(r);
          this.fireEvent('complete', r);
        } else {
          this.eResult.set('html', 'Готово на ' + r.percentage + '%');
        }
      }.bind(this));
    }.bind(this);
    this.timer = checkStatus.periodical(this.options.period);
  },

  status: function(callback) {
    new Ngn.Request.JSON({
      url: this.options.url + '?a=json_ljStatus',
      onComplete: function(r) {
        callback(r);
      }.bind(this)
    }).send();
  },

  completed: false,

  complete: function(r) {
    this.started = false;
    this.completed = true;
    this.el.removeClass('hLoader');
    this.btnClose.setStyle('display', 'block');
    this.eResult.set('html', this.options.completeText(r));
  }

});

Ngn.LongJob.status = function() {

};