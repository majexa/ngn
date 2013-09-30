Ngn.LongJob = new Class({
  Implements: [Options],

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
      c(r);
      if (r.status == 'progress') this.statusCycle();
      else if (r.status == 'complete') {
        this.build();
        this.complete(r);
      }
    }.bind(this));
  },

  build: function() {
    this.el = Elements.from('<div class="item dgray"><div class="icon-button md-closer"></div><div class="cont"></div></div>')[0].inject(document.getElement('.longJobs'));
    this.btnClose = this.el.getElement('.icon-button');
    // this.btnClose.setStyle('display', 'none');
    this.elCont = this.el.getElement('.cont');
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
    this.elCont.set('html', this.options.title + '...');
    this.el.addClass('hLoader');
    var checkStatus = function() {
      this.status(function(r) {
        if (r.status == 'complete') {
          if (this.timer) clearInterval(this.timer);
          this.complete(r);
        } else {
          this.elCont.set('html', 'Готово на ' + r.percentage + '%');
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
    this.elCont.set('html', this.options.completeText(r));
  }

});

Ngn.LongJob.status = function() {

};