Ngn.cp.LongJob = new Class({
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
      if (r.status == 'progress') this.startRequestCycle();
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
      url: this.options.url + '?a=ajax_' + this.options.action + 'Delete',
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
    this.completed ? this.delete(this.startRequestCycle.bind(this)) : this.startRequestCycle();
  },

  startRequestCycle: function() {
    this.started = true;
    if (!this.el) this.build();
    this.elCont.set('html', this.options.title + '...');
    this.el.addClass('hLoader');
    var action = function() {
      new Ngn.Request.JSON({
        url: this.options.url + '?a=json_' + this.options.action,
        onComplete: function(r) {
          if (r.status == 'complete') {
            if (this.timer) clearInterval(this.timer);
            this.complete(r);
          } else {
            this.elCont.set('html', 'Готово на ' + r.percentage + '%');
          }
        }.bind(this)
      }).send();
    }.bind(this);
    action();
    this.timer = action.periodical(this.options.period);
  },

  status: false,

  status: function(callback) {
    new Ngn.Request.JSON({
      url: this.options.url + '?a=json_' + this.options.action + 'Status',
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

Ngn.cp.LongJob.status = function() {

};