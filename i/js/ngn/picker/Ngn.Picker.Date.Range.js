/*
---
name: Picker.Date.Range
description: Select a Range of Dates
authors: Arian Stolwijk
requires: [Picker, Picker.Date]
provides: Picker.Date.Range
...
*/

Ngn.Picker.Date.Range = new Class({

	Extends: Ngn.Picker.Date,

	options: {
		getStartEndDate: function(input){
			return input.get('value').split('-').map(function(date){
				var parsed = Date.parse(date);
				return Date.isValid(parsed) ? parsed : null;
			}).clean();
		},
		setStartEndDate: function(input, dates){
			if (!dates[0] && !dates[1]) {
        input.set('value', ['', '']);
        return;
			}
			input.set('value', dates.map(function(date){
				return date.format(this.options.format);
			}, this).join(' - '));
		},
		footer: true,
		columns: 3
	},

	getInputDate: function(input){
		if (!input) return;

		var dates = input.retrieve('datepicker:value');
		if (!dates) {
      this.date = this.startDate = this.endDate = new Date();
      return;
		}

		if (dates && dates.length) dates = dates.map(Date.parse);
		if (!dates || !dates.length || dates.some(function(date){
			return !Date.isValid(date);
		})){
			dates = this.options.getStartEndDate.call(this, input);
			if (!dates.length || !dates.every(function(date){
				return Date.isValid(date);
			})) dates = [this.date];
		}

		if (dates.length == 1) {
			this.date = this.startDate = this.endDate = dates[0];
    }
		else if (dates.length == 2){
			this.date = this.startDate = dates[0];
			this.endDate = dates[1];
		}
	},

	constructPicker: function(){
		this.parent();
		var footer = this.footer, self = this;
		if (!footer) return;

		var events = {
			click: function(){
				this.focus();
			},
			blur: function(){
				var date = Date.parse(this.get('value'));
				if (date.isValid) self[(this == startInput ? 'start' : 'end') + 'Date'] = date;
				self.updateRangeSelection();
			},
			keydown: function(event){
				if (event.key == 'enter') self.selectRange();
			}
		};

		var startInput = this.startInput = new Element('input', {events: events}).inject(footer);
		new Element('span', {text: ' - '}).inject(footer);
		var endInput = this.endInput = new Element('input', {events: events}).inject(footer);

		this.applyButton = new Element('button.apply', {
			text: Locale.get('DatePicker.apply_range'),
			events: {click: self.selectRange.pass([], self)}
		}).inject(footer);

		this.cancelButton = new Element('button.cancel', {
			text: Locale.get('DatePicker.cancel'),
			events: {click: self.close.pass(false, self)}
		}).inject(footer);

    this.resetButton = new Element('button.reset', {
      text: Locale.get('DatePicker.reset'),
      events: {click: self.reset.pass(false, self)}
    }).inject(footer);
	},

	reset: function() {
    this.startDate = false;
    this.endDate = false;
    //this.select(new Date()); // set visual picker element to current date
    this.selectRange(); // cleanup selection
		this.close();
	},

	renderDays: function(){
		this.parent.apply(this, arguments);
		this.updateRangeSelection();
	},

	select: function(date){
		if (this.startDate && (this.endDate == this.startDate || date > this.endDate) && date >= this.startDate) this.endDate = date;
		else {
			this.startDate = date;
			this.endDate = date;
		}
		this.updateRangeSelection();
	},

	selectRange: function(){
    var input = this.input;

    if (!this.startDate && !this.endDate) {
    	// reset
      input.store('datepicker:value', null);
      this.fireEvent('select', false, input);
      this.close();
      return this;
		}

    var dates = [this.startDate, this.endDate];
		this.date = this.startDate;
		this.options.setStartEndDate.call(this, input, dates);
		input.store('datepicker:value', dates.map(function(date){
			return date.strftime();
		})).fireEvent('change');
		this.fireEvent('select', dates, input);
		this.close();
		return this;
	},

	updateRangeSelection: function(){
		if (!this.startDate && !this.endDate) {
      start = end = new Date();
		} else {
      var start = this.startDate,
        end = this.endDate || start;
		}



		if (this.dateElements) {
			for (var i = this.dateElements.length; i--;) {
        var el = this.dateElements[i];
        if (el.time >= start && el.time <= end) el.element.addClass('selected');
        else el.element.removeClass('selected');
      }
    }

		var formattedFirst = start.format(this.options.format),
			formattedEnd = end.format(this.options.format);

		this.startInput.set('value', formattedFirst);
		this.endInput.set('value', formattedEnd);

		return this;
	}

});
