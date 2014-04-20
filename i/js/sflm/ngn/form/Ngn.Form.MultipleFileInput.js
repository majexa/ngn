Object.append(Element.NativeEvents, {
  dragenter: 2, dragleave: 2, dragover: 2, dragend: 2, drop: 2
});

Ngn.Form.MultipleFileInput = new Class({
  Implements: [Options, Events],
  
  initialize: function(eInput, eContainer, options) {
    this.eInput = document.id(eInput);
    this.eContainer = document.id(eContainer);
    this.setOptions(options);
    var drop = this.drop = document.id(this.options.drop);
    var name = this.eInput.get('name');
    this.eInput.set('multiple', true);
    this.inputEvents = {
      change: function() {
        Array.each(this.eInput.files, this.add, this);
      }.bind(this)
    };
    this.dragEvents = drop && (typeof document.body.draggable != 'undefined') ? {
      dragenter: this.fireEvent.bind(this, 'dragenter'),
      dragleave: this.fireEvent.bind(this, 'dragleave'),
      dragend: this.fireEvent.bind(this, 'dragend'),
      dragover: function(event){
        event.preventDefault();
        this.fireEvent('dragover', event);
      }.bind(this),
      drop: function(event){
        event.preventDefault();
        var dataTransfer = event.event.dataTransfer;
        if (dataTransfer) Array.each(dataTransfer.files, this.add, this);
        this.fireEvent('drop', event);
      }.bind(this)
    } : null;
    this.attach();
  },
  
  attach: function(){
    this.eInput.addEvents(this.inputEvents);
    if (this.dragEvents) this.drop.addEvents(this.dragEvents);
  },

  detach: function(){
    this.eInput.removeEvents(this.inputEvents);
    if (this.dragEvents) this.drop.removeEvents(this.dragEvents);
  },
  
  _files: [],

  add: function(file) {
    this._files.push(file);
    this.fireEvent('change', file);
    this.eContainer.set('html', 'Добавлено: '+this._files.length+' шт.');
    return this;
  },

  getFiles: function(){
    return this._files;
  }

});

// заменяет обычный input multiple-input'ом
Ngn.Form.MultipleFileInput.Adv = new Class({
  Extends: Ngn.Form.MultipleFileInput,

  options: {
    itemClass: 'uploadItem'/*,
    onAdd: function(file){},
    onRemove: function(file){},
    onEmpty: function(){},
    onDragenter: function(event){},
    onDragleave: function(event){},
    onDragover: function(event){},
    onDrop: function(event){}*/
  },
  
  _files: [],

  add: function(file) {
    this._files.push(file);
    var self = this;
    new Element('li', {
      'class': this.options.itemClass
    }).grab(new Element('span', {
      text: file.name
    })).grab(new Element('a', {
      text: 'x',
      href: '#',
      events: {
        click: function(e){
          e.preventDefault();
          self.remove(file);
        }
      }
    })).inject(this.eConrainer);
    this.fireEvent('add', file);
    return this;
  },

  remove: function(file){
    var index = this._files.indexOf(file);
    if (index == -1) return this;
    this._files.splice(index, 1);
    this.eContainer.childNodes[index].destroy();
    this.fireEvent('remove', file);
    if (!this._files.length) this.fireEvent('empty');
    return this;
  },

  getFiles: function(){
    return this._files;
  }

});