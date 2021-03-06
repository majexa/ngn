(function() {

  var $ = document.id;

  this.NewSlider = new Class({

    Implements: [Events, Options],

    Binds: ['clickedElement', 'draggedKnob', 'scrolledElement', 'clickedElementKeyUp'],

    options: {
      /*
       onTick: Function.from()(intPosition),
       onChange: Function.from()(intStep),
       onComplete: Function.from()(strStep),*/
      onTick: function(position) {
        if (this.options.snap)
          position = this.toPosition(this.step);
        this.knob.setStyle(this.property, position);
        $(document.body).getElements('.knob').setProperty('aria-valuenow', this.step);
      },
      onMoveTick: function(position) {
        $(document.body).getElements('.knob').setProperty('aria-valuenow', this.step);
      },
      initialStep: 0,
      snap: false,
      offset: 0,
      range: false,
      wheel: false,
      steps: 100,
      mode: 'horizontal'
    },

    initialize: function(element, knob, options) {
      this.setOptions(options);
      this.element = document.id(element);
      this.knob = document.id(knob);
      this.previousChange = this.previousEnd = this.step = -1;
      var offset, limit = {}, modifiers = {
        'x': false,
        'y': false
      };
      switch (this.options.mode) {
        case 'vertical':
          this.axis = 'y';
          this.property = 'top';
          offset = 'offsetHeight';
          break;
        case 'horizontal':
          this.axis = 'x';
          this.property = 'left';
          offset = 'offsetWidth';
      }
      this.full = this.element.measure(function() {
        this.half = this.knob[offset] / 2;
        return this.element[offset] - this.knob[offset] + (this.options.offset * 2);
      }.bind(this));

      this.min = $chk(this.options.range[0]) ? this.options.range[0] : 0;
      this.max = $chk(this.options.range[1]) ? this.options.range[1] : this.options.steps;
      this.range = this.max - this.min;
      this.steps = this.options.steps || this.full;
      this.stepSize = Math.abs(this.range) / this.steps;
      this.stepWidth = this.stepSize * this.full / Math.abs(this.range);

      this.knob.setStyle('position', 'relative').setStyle(this.property, this.options.initialStep ? this.toPosition(this.options.initialStep) : -this.options.offset);

      modifiers[this.axis] = this.property;
      limit[this.axis] = [-this.options.offset, this.full - this.options.offset];

      // *********** everything for drag and drop ***********************************
      var dragOptions = {
        preventDefault: true,
        snap: 0,
        limit: limit,
        modifiers: modifiers,
        onDrag: (function() {
          this.draggedKnob();
        }).bind(this),
        onStart: (function() {
          this.draggedKnob();
        }).bind(this),
        onBeforeStart: (function() {
          this.isDragging = true;
        }).bind(this),
        onCancel: function() {
          this.isDragging = false;
        }.bind(this),
        onComplete: function() {
          this.isDragging = false;
          this.draggedKnob();
          this.end();
        }.bind(this)
      };
      if (this.options.snap) {
        dragOptions.grid = Math.ceil(this.stepWidth);
        dragOptions.limit[this.axis][1] = this.full;
      }
      this.drag = new Drag(this.knob, dragOptions);
      this.attach();

      $(document.body).getElements('.knob').setProperty('role', 'slider');
      $(document.body).getElements('.knob').setProperty('aria-valuemin', this.min);
      $(document.body).getElements('.knob').setProperty('aria-valuemax', this.max);
      $(document.body).getElements('.knob').setProperty('aria-valuenow', this.options.initialStep);
      //$(document.body).getElements('.knob').setProperty('aria-valuetext', this.options.initialStep);
      //$(document.body).getElements('.knob').setProperty('aria-live', 'assertive');
      $(document.body).getElements('.knob').setProperty('tabindex', '0');
      this.lastValueNow = this.knob.getProperty('aria-valuenow');

    },
    attach: function() {
      var self = this;
      this.element.addEvent('mousedown', function(e) {
        self.clickedElement(e);
      });
      this.element.addEvent('touchstart', function(e) {
        self.clickedElement(e);
      });
      if (this.options.wheel)
        this.element.addEvent('mousewheel', this.scrolledElement);

      this.knob.addEvent('keydown', this.clickedElementKeyUp.bindWithEvent(this));

      //VoiceOver compatibility
      this.knob.addEvent('focus', function() {
        var addCount = function() {
          if (this.knob.getProperty('aria-valuenow') != this.currentValueNow) {
            step = this.lastValueNow.toInt() + (this.knob.getProperty('aria-valuenow').toInt() - this.lastValueNow.toInt())
            if (!((this.range > 0) ^ (step < this.min))) {
              step = this.min;
              this.fireEvent('moveTick', this.step);
            }
            if (!((this.range > 0) ^ (step > this.max))) {
              step = this.max;
              this.fireEvent('moveTick', this.step);
            }

            this.step = Math.round(step);
            this.checkStep();
            position = this.toPosition(this.step);
            if (this.options.snap)
              position = this.toPosition(this.step);
            this.knob.setStyle(this.property, position);
            this.lastValueNow = this.knob.getProperty('aria-valuenow')
          }
        }.bind(this);
        var timer = addCount.periodical(500, this);

        this.knob.addEvents({
          'touchend': function() {
            //clearInterval(timer);
            removeEvents('touchend', 'mouseup', 'mousemove', 'touchmove', 'blur');
          }.bind(this),
          'mouseup': function() {
            clearInterval(timer);
            removeEvents('touchend', 'mouseup', 'mousemove', 'touchmove', 'blur');
          }.bind(this),
          'mousemove': function() {
            clearInterval(timer);
            removeEvents('touchend', 'mouseup', 'mousemove', 'touchmove', 'blur');
          }.bind(this),
          'touchmove': function() {
            //clearInterval(timer);
            removeEvents('touchend', 'mouseup', 'mousemove', 'touchmove', 'blur');
          }.bind(this),
          'blur': function() {
            clearInterval(timer);
            removeEvents('touchend', 'mouseup', 'mousemove', 'touchmove', 'blur');
          }.bind(this)
        });
      }.bind(this));
      // ------------------------- Ende Bearbeitet ---------------------------------

      this.drag.attach();
      return this;
    },
    detach: function() {
      this.element.removeEvent('mousedown', this.clickedElement);

      this.element.removeEvent('touchstart', this.clickedElement);
      this.element.removeEvent('mousewheel', this.scrolledElement);
      this.drag.detach();
      return this;
    },
    set: function(step) {
      if (!((this.range > 0) ^ (step < this.min)))
        step = this.min;
      if (!((this.range > 0) ^ (step > this.max)))
        step = this.max;

      this.step = Math.round(step);
      this.checkStep();
      this.fireEvent('tick', this.toPosition(this.step));
      this.end();
      return this;
    },
    clickedElementKeyUp: function(event) {

      var keyCode;

      if (window.event) {
        var e = window.event;
        keyCode = e.keyCode;
      } else {
        keyCode = event.code;
      }

      //alert(keyCode);

      switch (keyCode) {
        case 37:
          // left arrow
          event.stop();
          this.set(this.step - this.stepSize);
          //alert('step afterwards: ' + this.step);
          break;
        case 39:
          // right arrow
          event.stop();
          this.set(this.step + this.stepSize);
          //alert('step afterwards: ' + this.step);
          break;
        case 38:
          // up arrow
          event.stop();
          this.set(this.step + this.stepSize);
          //alert('step afterwards: ' + this.step);
          break;

        case 40:
          // down arrow
          event.stop();
          this.set(this.step - this.stepSize);
          //alert('step afterwards: ' + this.step);
          break;
        case 33:
          // Bild hoch
          event.stop();
          this.set(this.step + 10 * this.stepSize);
          break;
        case 34:
          // Bild runter
          event.stop();
          this.set(this.step - 10 * this.stepSize);
          break;
        case 36:
          // Pos 1
          event.stop();
          this.set(this.min);
          break;
        case 35:
          // Ende
          event.stop();
          this.set(this.max);
          break;
      }
    },
    // ------------------------- Ende Bearbeitet ---------------------------------

    clickedElement: function(event) {
      this.knob.focus();
      if (this.isDragging || event.target == this.knob)
        return;

      var dir = this.range < 0 ? -1 : 1;
      var position = event.page[this.axis] - this.element.getPosition()[this.axis] - this.half;
      position = position.limit(-this.options.offset, this.full - this.options.offset);

      this.step = Math.round(this.min + dir * this.toStep(position));
      this.checkStep();
      this.fireEvent('tick', position);
      this.end();
    },
    scrolledElement: function(event) {
      var mode = (this.options.mode == 'horizontal') ? (event.wheel < 0) : (event.wheel > 0);
      this.set(mode ? this.step - this.stepSize : this.step + this.stepSize);
      event.stop();
    },
    draggedKnob: function() {
      var dir = this.range < 0 ? -1 : 1;
      var position = this.drag.value.now[this.axis];
      position = position.limit(-this.options.offset, this.full - this.options.offset);
      this.step = Math.round(this.min + dir * this.toStep(position));
      this.checkStep();

    },
    checkStep: function() {
      if (this.previousChange != this.step) {
        this.previousChange = this.step;
        this.fireEvent('change', this.step);
        this.fireEvent('moveTick', this.step);
      }
    },
    end: function() {
      if (this.previousEnd !== this.step) {
        this.previousEnd = this.step;
        this.fireEvent('complete', this.step + '');
      }
    },
    toStep: function(position) {
      var step = (position + this.options.offset) * this.stepSize / this.full * this.steps;
      return this.options.steps ? Math.round(step -= step % this.stepSize) : step;
    },
    toPosition: function(step) {
      return (this.full * Math.abs(this.min - step)) / (this.steps * this.stepSize) - this.options.offset;
    }
  });

})();

Ngn.Form.El.Slider = new Class({
  Extends: Ngn.Form.El,

  init: function() {
    var eInput = this.eRow.getElement('input');
    var slider = new NewSlider(this.eRow.getElement('.slider'), this.eRow.getElement('.knob'), {
      steps: Ngn.Form.elOptions[this.name].steps,
      initialStep: eInput.get('value'),
      range: Ngn.Form.elOptions[this.name].range,
      onChange: function(value) {
        eInput.set('value', value);
        this.fireFormElEvent('change', value);
      }.bind(this),
      onComplete: function(value) {
        this.fireFormElEvent('changed');
      }.bind(this)
    });
  }

});