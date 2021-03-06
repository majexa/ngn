Ngn.Tree = new Class({
  Implements: [Events, Options],

  options: {
    types: {},
    forest: false,
    animateScroll: true,
    height: 18,
    expandTo: true
  },

  initialize: function(options) {
    this.setOptions(options);
    Object.append(this, {
      types: this.options.types,
      forest: this.options.forest,
      animateScroll: this.options.animateScroll,
      dfltType: this.options.dfltType,
      height: this.options.height,
      container: $(options.container),
      UID: ++Ngn.Tree.uid,
      key: {},
      expanded: []
    });
    this.defaults = {
      name: '',
      cls: '',
      openIcon: 'ngn-tree-empty-icon',
      closeIcon: 'ngn-tree-empty-icon',
      loadable: false,
      hidden: false
    };
    this.dfltState = {
      open: false
    };
    this.$index = [];
    this.updateOpenState();
    if (this.options.expandTo) this.initExpandTo();
    this.DOMidPrefix = 'ngn-tree-';
    this.wrapper = new Element('div').addClass('ngn-tree-wrapper').inject(this.container);
    this.events();
    this.initScroll();
    this.initSelection();
    this.initHover();
    this.addEvent('drawChildren', function(parent) {
      var nodes = parent._toggle || [];
      for (var i = 0, l = nodes.length; i < l; i++) {
        nodes[i].drawToggle();
      }
      parent._toggle = [];
    });
    var id = this.options.id;
    this.id = id;
    if (id != null) Ngn.Tree.ids[id] = this;
    if (MooTools.version >= '1.2.2' && this.options.initialize) this.options.initialize.call(this);
    this.init();
  },

  init: function() {
  },

  bound: function() {
    Array.each(arguments, function(name) {
      this.bound[name] = this[name].bind(this);
    }, this);
  },

  events: function() {
    this.bound('mouse', 'mouseleave', 'mousedown', 'preventDefault', 'toggleClick', 'toggleDblclick', 'focus', 'blurOnClick', 'keyDown', 'keyUp');
    this.wrapper.addEvents({
      mousemove: this.bound.mouse,
      mouseover: this.bound.mouse,
      mouseout: this.bound.mouse,
      mouseleave: this.bound.mouseleave,
      mousedown: this.bound.mousedown,
      click: this.bound.toggleClick,
      dblclick: this.bound.toggleDblclick,
      selectstart: this.bound.preventDefault
    });

    this.container.addEvent('click', this.bound.focus);
    document.addEvent('click', this.bound.blurOnClick);

    document.addEvents({
      keydown: this.bound.keyDown,
      keyup: this.bound.keyUp
    });
  },

  blurOnClick: function(event) {
    var target = event.target;
    while (target) {
      if (target == this.container) return;
      target = target.parentNode;
    }
    this.blur();
  },

  focus: function() {
    if (Ngn.Tree.focus && Ngn.Tree.focus == this) return this;
    if (Ngn.Tree.focus) Ngn.Tree.focus.blur();
    Ngn.Tree.focus = this;
    this.focused = true;
    this.container.addClass('ngn-tree-focused');
    return this.fireEvent('focus');
  },

  blur: function() {
    Ngn.Tree.focus = null;
    if (!this.focused) return this;
    this.focused = false;
    this.container.removeClass('ngn-tree-focused');
    return this.fireEvent('blur');
  },

  $getIndex: function() {//return array of visible nodes.
    this.$index = [];
    var node = this.forest ? this.root.getFirst() : this.root;
    var previous = node;
    while (node) {
      if (!(previous.hidden && previous.contains(node))) {
        if (!node.hidden) this.$index.push(node);
        previous = node;
      }
      node = node._getNextVisible();
    }
  },

  preventDefault: function(event) {
    event.preventDefault();
  },

  mousedown: function(event) {
    if (event.rightClick) return;
    event.preventDefault();
    this.fireEvent('mousedown');
  },

  mouseleave: function() {
    this.mouse.coords = {x: null, y: null};
    this.mouse.target = false;
    this.mouse.node = false;
    if (this.hover) this.hover();
  },

  mouse: function(event) {
    this.mouse.coords = this.getCoords(event);
    var target = this.getTarget(event);
    this.mouse.target = target.target;
    this.mouse.node = target.node;
  },

  getTarget: function(event) {
    var target = event.target, node;
    while (!(/ngn-tree/).test(target.className)) {
      target = target.parentNode;
    }
    var test = target.className.match(/ngn-tree-(gadjet)-[^n]|ngn-tree-(icon)|ngn-tree-(name)|ngn-tree-(checkbox)/);
    if (!test) {
      var y = this.mouse.coords.y;
      if (y == -1 || !this.$index) {
        node = false;
      } else {
        node = this.$index[((y) / this.height).toInt()];
      }
      return {
        node: node,
        target: 'node'
      };
    }
    for (var i = 5; i > 0; i--) {
      if (test[i]) {
        var type = test[i];
        break;
      }
    }
    return {
      node: Ngn.Tree.Nodes[target.getAttribute('uid')],
      target: type
    };
  },

  getCoords: function(event) {
    var position = this.wrapper.getPosition();
    var x = event.page.x - position.x;
    var y = event.page.y - position.y;
    var wrapper = this.wrapper;
    if ((y - wrapper.scrollTop > wrapper.clientHeight) || (x - wrapper.scrollLeft > wrapper.clientWidth)) {//scroll line
      y = -1;
    }
    ;
    return {x: x, y: y};
  },

  keyDown: function(event) {
    this.key = event;
    this.key.state = 'down';
    if (this.focused) this.fireEvent('keydown', [event]);
  },

  keyUp: function(event) {
    this.key = {};
    this.key.state = 'up';
    if (this.focused) this.fireEvent('keyup', [event]);
  },

  toggleDblclick: function(event) {
    var target = this.mouse.target;
    if (!(target == 'name' || target == 'icon')) return;
    this.mouse.node.toggle();
  },

  toggleClick: function(event) {
    if (this.mouse.target != 'gadjet') return;
    this.mouse.node.toggle();
  },

  initScroll: function() {
    this.scroll = new Fx.Scroll(this.wrapper, {link: 'cancel'});
  },

  scrollTo: function(node) {
    var position = node.getVisiblePosition();
    var top = position * this.height;
    var up = (top < this.wrapper.scrollTop);
    var down = (top > (this.wrapper.scrollTop + this.wrapper.clientHeight - this.height));
    if (position == -1 || ( !up && !down )) {
      this.scroll.fireEvent('complete');
      return false;
    }
    if (this.animateScroll) {
      this.scroll.start(this.wrapper.scrollLeft, top - (down ? this.wrapper.clientHeight - this.height : this.height));
    } else {
      this.scroll.set(this.wrapper.scrollLeft, top - (down ? this.wrapper.clientHeight - this.height : this.height));
      this.scroll.fireEvent('complete');
    }
    return this;
  },

  updateOpenState: function() {
    this.addEvents({
      'drawChildren': function(parent) {
        var children = parent.children;
        for (var i = 0, l = children.length; i < l; i++) {
          children[i].updateOpenState();
        }
      },
      'drawRoot': function() {
        this.root.updateOpenState();
      }
    });
  },

  expandTo: function(node) {
    if (!node) return this;
    var path = [];
    while (!node.isRoot() && !(this.forest && node.getParent().isRoot())) {
      node = node.getParent();
      if (!node) break;
      path.unshift(node);
    }
    ;
    path.each(function(el) {
      el.toggle(true);
    });
    return this;
  },

  initExpandTo: function() {
    this.addEvent('loadChildren', function(parent) {
      if (!parent) return;
      var children = parent.children;
      for (var i = children.length; i--;) {
        var child = children[i];
        if (child.expandTo) this.expanded.push(child);
      }
    });
    function expand() {
      this.expanded.each(function(node) {
        this.expandTo(node);
      }, this);
      this.expanded = [];
    };
    this.addEvents({
      'load': expand.bind(this),
      'loadNode': expand.bind(this)
    });
  },

  isDisabledSelect: function(node) {
    return this.options.isDisabledSelect ? this.options.isDisabledSelect(node) : false;
  }

});

Ngn.Tree.uid = 0;
if (!Ngn.Tree.ids) Ngn.Tree.ids = {};
if (!Ngn.Tree.id) Ngn.Tree.id = function(id) {
  return Ngn.Tree.ids[id];
};

Array.implement({

  inject: function(added, current, where) {//inject added after or before current;
    var pos = this.indexOf(current) + (where == 'before' ? 0 : 1);
    for (var i = this.length - 1; i >= pos; i--) {
      this[i + 1] = this[i];
    }
    this[pos] = added;
    return this;
  }

});


/*
 ---

 name: Ngn.Tree.Node
 description: Ngn.Tree.Node
 license: MIT-Style License (http://mifjs.net/license.txt)
 copyright: Anton Samoylov (http://mifjs.net)
 authors: Anton Samoylov (http://mifjs.net)
 requires: Ngn.Tree
 provides: Ngn.Tree.Node

 ...
 */

Ngn.Tree.Node = new Class({

  Implements: [Events],

  initialize: function(structure, options) {
    Object.append(this, structure);
    this.children = [];
    this.type = options.type || this.tree.dfltType;
    this.property = options.property || {};
    this.data = options.data;
    this.state = Object.append(Object.clone(this.tree.dfltState), options.state);
    this.$calculate();
    this.UID = Ngn.Tree.Node.UID++;
    Ngn.Tree.Nodes[this.UID] = this;
    var id = this.id;
    if (id != null && id != undefined) Ngn.Tree.ids[id] = this;
    this.tree.fireEvent('nodeCreate', [this]);
    this._property = ['id', 'name', 'cls', 'openIcon', 'closeIcon', 'openIconUrl', 'closeIconUrl', 'hidden'];
  },

  $calculate: function() {
    Object.append(this, Object.clone(this.tree.defaults));
    this.type = Array.from(this.type);
    this.type.each(function(type) {
      var props = this.tree.types[type];
      if (props) Object.append(this, props);
    }, this);
    Object.append(this, this.property);
    return this;
  },

  getDOM: function(what) {
    var node = $(this.tree.DOMidPrefix + this.UID);
    if (what == 'node') return node;
    var wrapper = node.getFirst();
    if (what == 'wrapper') return wrapper;
    if (what == 'children') return wrapper.getNext();
    return wrapper.getElement('.ngn-tree-' + what);
  },

  getGadjetType: function() {
    return (this.loadable && !this.isLoaded()) ? 'plus' : (this.hasVisibleChildren() ? (this.isOpen() ? 'minus' : 'plus') : 'none');
  },

  toggle: function(state) {
    if (this.state.open == state || this.$loading || this.$toggling) return this;
    var parent = this.getParent();

    function toggle(type) {
      this.state.open = !this.state.open;
      if (type == 'drawed') {
        this.drawToggle();
      } else {
        parent._toggle = (parent._toggle || [])[this.state.open ? 'include' : 'erase'](this);
      }
      this.fireEvent('toggle', [this.state.open]);
      this.tree.fireEvent('toggle', [this, this.state.open]);
      return this;
    }

    if (parent && !parent.$draw) {
      return toggle.apply(this, []);
    }
    if (this.loadable && !this.state.loaded) {
      if (!this.load_event) {
        this.load_event = true;
        this.addEvent('load', function() {
          this.toggle();
        }.bind(this));
      }
      return this.load();
    }
    if (!this.hasChildren()) return this;
    return toggle.apply(this, ['drawed']);
  },

  drawToggle: function() {
    this.tree.$getIndex();
    Ngn.Tree.Draw.update(this);
  },

  recursive: function(fn, args) {
    args = Array.from(args);
    if (fn.apply(this, args) !== false) {
      this.children.each(function(node) {
        if (node.recursive(fn, args) === false) {
          return false;
        }
      });
    }
    return this;
  },

  isOpen: function() {
    return this.state.open;
  },

  isLoaded: function() {
    return this.state.loaded;
  },

  isLast: function() {
    if (this.parentNode == null || this.parentNode.children.getLast() == this) return true;
    return false;
  },

  isFirst: function() {
    if (this.parentNode == null || this.parentNode.children[0] == this) return true;
    return false;
  },

  isRoot: function() {
    return this.parentNode == null ? true : false;
  },

  getChildren: function() {
    return this.children;
  },

  hasChildren: function() {
    return this.children.length ? true : false;
  },

  index: function() {
    if (this.isRoot()) return 0;
    return this.parentNode.children.indexOf(this);
  },

  getNext: function() {
    if (this.isLast()) return null;
    return this.parentNode.children[this.index() + 1];
  },

  getPrevious: function() {
    if (this.isFirst()) return null;
    return this.parentNode.children[this.index() - 1];
  },

  getFirst: function() {
    if (!this.hasChildren()) return null;
    return this.children[0];
  },

  getLast: function() {
    if (!this.hasChildren()) return null;
    return this.children.getLast();
  },

  getParent: function() {
    return this.parentNode;
  },

  _getNextVisible: function() {
    var current = this;
    if (current.isRoot()) {
      if (!current.isOpen() || !current.hasChildren(true)) return false;
      return current.getFirst(true);
    } else {
      if (current.isOpen() && current.getFirst(true)) {
        return current.getFirst(true);
      } else {
        var parent = current;
        do {
          current = parent.getNext(true);
          if (current) return current;
          parent = parent.parentNode;
        } while (parent);
        return false;
      }
    }
  },

  getPreviousVisible: function() {
    var index = this.tree.$index.indexOf(this);
    return index == 0 ? null : this.tree.$index[index - 1];
  },

  getNextVisible: function() {
    var index = this.tree.$index.indexOf(this);
    return index < this.tree.$index.length - 1 ? this.tree.$index[index + 1] : null;
  },

  getVisiblePosition: function() {
    return this.tree.$index.indexOf(this);
  },

  hasVisibleChildren: function() {
    if (!this.hasChildren()) return false;
    if (this.isOpen()) {
      var next = this.getNextVisible();
      if (!next) return false;
      if (next.parentNode != this) return false;
      return true;
    } else {
      var child = this.getFirst();
      while (child) {
        if (!child.hidden) return true;
        child = child.getNext();
      }
      return false;
    }
  },

  isLastVisible: function() {
    var next = this.getNext();
    while (next) {
      if (!next.hidden) return false;
      next = next.getNext();
    }
    ;
    return true;
  },

  contains: function(node) {
    while (node) {
      if (node == this) return true;
      node = node.parentNode;
    }
    ;
    return false;
  },

  addType: function(type) {
    return this.processType(type, 'add');
  },

  removeType: function(type) {
    return this.processType(type, 'remove');
  },

  setType: function(type) {
    return this.processType(type, 'set');
  },

  processType: function(type, action) {
    switch (action) {
      case 'add':
        this.type.include(type);
        break;
      case 'remove':
        this.type.erase(type);
        break;
      case 'set':
        this.type = type;
        break;
    }
    var current = {};
    this._property.each(function(p) {
      current[p] = this[p];
    }, this);
    this.$calculate();
    this._property.each(function(p) {
      this.updateProperty(p, current[p], this[p]);
    }, this);
    return this;
  },

  set: function(obj) {
    this.tree.fireEvent('beforeSet', [this, obj]);
    var property = obj.property || obj || {};
    for (var p in property) {
      var nv = property[p];
      var cv = this[p];
      this.updateProperty(p, cv, nv);
      this[p] = this.property[p] = nv;
    }
    this.tree.fireEvent('set', [this, obj]);
    return this;
  },

  updateProperty: function(p, cv, nv) {
    if (nv == cv) return this;
    if (p == 'id') {
      delete Ngn.Tree.ids[cv];
      if (nv) Ngn.Tree.ids[nv] = this;
      return this;
    }
    if (!Ngn.Tree.Draw.isUpdatable(this)) return this;
    switch (p) {
      case 'name':
        this.getDOM('name').set('html', nv);
        return this;
      case 'cls':
        this.getDOM('wrapper').removeClass(cv).addClass(nv);
        return this;
      case 'openIcon':
      case 'closeIcon':
        this.getDOM('icon').removeClass(cv).addClass(nv);
        return this;
      case 'openIconUrl':
      case 'closeIconUrl':
        var icon = this.getDOM('icon');
        icon.setStyle('background-image', 'none');
        if (nv) icon.setStyle('background-image', 'url(' + nv + ')');
        return this;
      case 'hidden':
        this.getDOM('node').setStyle('display', nv ? 'none' : 'block');
        var _previous = this.getPreviousVisible();
        var _next = this.getNextVisible();
        var parent = this.getParent();
        this[p] = this.property[p] = nv;
        this.tree.$getIndex();
        var previous = this.getPreviousVisible();
        var next = this.getNextVisible();
        [_previous, _next, previous, next, parent, this].each(function(node) {
          Ngn.Tree.Draw.update(node);
        });
        return this;
    }
    return this;
  },

  updateOpenState: function() {
    if (this.state.open) {
      this.state.open = false;
      this.toggle();
    }
  }

});

Ngn.Tree.Node.UID = 0;
Ngn.Tree.Nodes = {};

/*
 ---

 name: Ngn.Tree.Draw
 description: convert javascript tree object to html
 license: MIT-Style License (http://mifjs.net/license.txt)
 copyright: Anton Samoylov (http://mifjs.net)
 authors: Anton Samoylov (http://mifjs.net)
 requires: Ngn.Tree
 provides: Ngn.Tree.Draw

 ...
 */

Ngn.Tree.Draw = {

  getHTML: function(node, html) {
    var prefix = node.tree.DOMidPrefix;
    var checkbox;
    if (node.state.checked != undefined) {
      if (!node.hasCheckbox) node.state.checked = 'nochecked';
      checkbox = '<span class="ngn-tree-checkbox ngn-tree-node-' + node.state.checked + '" uid="' + node.UID + '">' + Ngn.Tree.Draw.zeroSpace + '</span>';
    } else {
      checkbox = '';
    }
    html = html || [];
    if (node.tree.isDisabledSelect(node))
      node.cls += (node.cls ? ' disabled' : 'disabled');
    html.push('<div class="ngn-tree-node ', (node.isLast() ? 'ngn-tree-node-last' : ''), '"' + (node.hidden ? ' style="display:none"' : '') + ' id="', prefix, node.UID, '">', '<span class="ngn-tree-node-wrapper ', node.cls, (node.state.selected ? ' ngn-tree-node-selected' : ''), '" uid="', node.UID, '">', '<span class="ngn-tree-gadjet ngn-tree-gadjet-', node.getGadjetType(), '" uid="', node.UID, '">', Ngn.Tree.Draw.zeroSpace, '</span>', checkbox, '<span class="ngn-tree-icon ', (node.closeIconUrl ? '" style="background-image: url(' + node.closeIconUrl + ')" ' : node.closeIcon + '"'), ' uid="', node.UID, '">', Ngn.Tree.Draw.zeroSpace, '</span>', '<span class="ngn-tree-name" uid="', node.UID, '">', node.name, '</span>', '</span>', '<div class="ngn-tree-children" style="display:none"></div>', '</div>');
    return html;
  },

  children: function(parent, container) {
    parent.open = true;
    parent.$draw = true;
    var html = [];
    var children = parent.children;
    for (var i = 0, l = children.length; i < l; i++) {
      this.getHTML(children[i], html);
    }
    container = container || parent.getDOM('children');
    container.set('html', html.join(''));
    parent.tree.fireEvent('drawChildren', [parent]);
  },

  root: function(tree) {
    var domRoot = this.node(tree.root);
    domRoot.inject(tree.wrapper);
    tree.$draw = true;
    tree.fireEvent('drawRoot');
  },

  forestRoot: function(tree) {
    var container = new Element('div').addClass('ngn-tree-children-root').inject(tree.wrapper);
    Ngn.Tree.Draw.children(tree.root, container);
  },

  node: function(node) {
    return new Element('div').set('html', this.getHTML(node).join('')).getFirst();
  },

  isUpdatable: function(node) {
    if ((!node || !node.tree) || (node.getParent() && !node.getParent().$draw) || (node.isRoot() && (!node.tree.$draw || node.tree.forest))) return false;
    return true;
  },

  update: function(node) {
    if (!this.isUpdatable(node)) return null;
    if (!node.hasChildren()) node.state.open = false;

    node.getDOM('gadjet').className = 'ngn-tree-gadjet ngn-tree-gadjet-' + node.getGadjetType();
    if (node.closeIconUrl) {
      node.getDOM('icon').setStyle('background-image', 'url(' + (node.isOpen() ? node.openIconUrl : node.closeIconUrl) + ')');
    } else {
      node.getDOM('icon').className = 'ngn-tree-icon ' + node[node.isOpen() ? 'openIcon' : 'closeIcon'];
    }
    node.getDOM('node')[(node.isLastVisible() ? 'add' : 'remove') + 'Class']('ngn-tree-node-last');
    if (node.$loading) return null;
    var children = node.getDOM('children');
    if (node.isOpen()) {
      if (!node.$draw) Ngn.Tree.Draw.children(node);
      children.style.display = 'block';
    } else {
      children.style.display = 'none';
    }
    node.tree.fireEvent('updateNode', node);
    return node;
  },

  inject: function(node, element) {
    if (!this.isUpdatable(node)) return;
    element = element || node.getDOM('node') || this.node(node);
    var previous = node.getPrevious();
    if (previous) {
      element.inject(previous.getDOM('node'), 'after');
      return;
    }
    var container;
    if (node.tree.forest && node.parentNode.isRoot()) {
      container = node.tree.wrapper.getElement('.ngn-tree-children-root');
    } else if (node.tree.root == node) {
      container = node.tree.wrapper;
    } else {
      container = node.parentNode.getDOM('children');
    }
    element.inject(container, 'top');
  }

};

Ngn.Tree.Draw.zeroSpace = Browser.ie ? '&shy;' : '&#8203';


/*
 ---

 name: Ngn.Tree.Selection
 description: tree nodes selection
 license: MIT-Style License (http://mifjs.net/license.txt)
 copyright: Anton Samoylov (http://mifjs.net)
 authors: Anton Samoylov (http://mifjs.net)
 requires: Ngn.Tree
 provides: Ngn.Tree.Selection

 ...
 */

Ngn.Tree.implement({

  initSelection: function() {
    this.defaults.selectClass = '';
    this.wrapper.addEvent('mousedown', function() {
      this.attachSelect();
    }.bind(this));
  },

  attachSelect: function(event) {
    if (!['icon', 'name', 'node'].contains(this.mouse.target)) return;
    var node = this.mouse.node;
    if (!node) return;
    if (this.isDisabledSelect(node)) return;
    this.select(node);
  },

  select: function(node, userSelect) {
    if (!node) return this;
    if (!userSelect != undefined) userSelect = true;
    var current = this.selected;
    if (current == node) return this;
    if (current) {
      current.select(false);
      this.fireEvent('unSelect', [current]).fireEvent('selectChange', [current, false]);
    }
    this.selected = node;
    node.select(true);
    this.fireEvent('select', [node]).fireEvent('selectChange', [node, true]);
    if (userSelect) this.fireEvent('userSelect', [node]);
    return this;
  },

  unselect: function() {
    var current = this.selected;
    if (!current) return this;
    this.selected = false;
    current.select(false);
    this.fireEvent('unSelect', [current]).fireEvent('selectChange', [current, false]);
    return this;
  },

  getSelected: function() {
    return this.selected;
  },

  isSelected: function(node) {
    return node.isSelected();
  }

});

Ngn.Tree.Node.implement({

  select: function(state) {
    this.state.selected = state;
    if (!Ngn.Tree.Draw.isUpdatable(this)) return;
    var wrapper = this.getDOM('wrapper');
    wrapper[(state ? 'add' : 'remove') + 'Class'](this.selectClass || 'ngn-tree-node-selected');
  },

  isSelected: function() {
    return this.state.selected;
  }

});


/*
 ---

 name: Ngn.Tree.Hover
 description: hover(mouseover/mouseout) events/effects
 license: MIT-Style License (http://mifjs.net/license.txt)
 copyright: Anton Samoylov (http://mifjs.net)
 authors: Anton Samoylov (http://mifjs.net)
 requires: Ngn.Tree
 provides: Ngn.Tree.Hover

 ...
 */

Ngn.Tree.implement({

  initHover: function() {
    this.defaults.hoverClass = '';
    this.wrapper.addEvent('mousemove', this.hover.bind(this));
    this.wrapper.addEvent('mouseout', this.hover.bind(this));
    this.defaultHoverState = {
      gadjet: false,
      checkbox: false,
      icon: false,
      name: false,
      node: false
    };
    this.hoverState = Object.clone(this.defaultHoverState);
  },

  hover: function() {
    var cnode = this.mouse.node;
    var ctarget = this.mouse.target;
    Array.each(this.hoverState, function(node, target, state) {
      if (node == cnode && (target == 'node' || target == ctarget)) return;
      if (node) {
        Ngn.Tree.Hover.out(node, target);
        state[target] = false;
        this.fireEvent('hover', [node, target, 'out']);
      }
      if (cnode && (target == 'node' || target == ctarget)) {
        Ngn.Tree.Hover.over(cnode, target);
        state[target] = cnode;
        this.fireEvent('hover', [cnode, target, 'over']);
      } else {
        state[target] = false;
      }
    }, this);
  },

  updateHover: function() {
    this.hoverState = Object.clone(this.defaultHoverState);
    this.hover();
  }

});

Ngn.Tree.Hover = {

  over: function(node, target) {
    var wrapper = node.getDOM('wrapper');
    wrapper.addClass((node.hoverClass || 'ngn-tree-hover') + '-' + target);
    if (node.state.selected) wrapper.addClass((node.hoverClass || 'ngn-tree-hover') + '-selected-' + target);
  },

  out: function(node, target) {
    var wrapper = node.getDOM('wrapper');
    wrapper.removeClass((node.hoverClass || 'ngn-tree-hover') + '-' + target).removeClass((node.hoverClass || 'ngn-tree-hover') + '-selected-' + target);
  }

};


/*
 ---

 name: Ngn.Tree.Load
 description: load tree from json
 license: MIT-Style License (http://mifjs.net/license.txt)
 copyright: Anton Samoylov (http://mifjs.net)
 authors: Anton Samoylov (http://mifjs.net)
 requires: Ngn.Tree
 provides: Ngn.Tree.Load

 ...
 */

Ngn.Tree.Load = {

  children: function(children, parent, tree) {
    var i, l;
    var subChildrens = [];
    for (i = children.length; i--;) {
      var child = children[i];
      var node = new Ngn.Tree.Node({
        tree: tree,
        parentNode: parent || undefined
      }, child);
      if (tree.forest || parent != undefined) {
        parent.children.unshift(node);
      } else {
        tree.root = node;
      }
      var subChildren = child.children;
      if (subChildren && subChildren.length) {
        subChildrens.push({children: subChildren, parent: node});
      }
    }
    for (i = 0, l = subChildrens.length; i < l; i++) {
      var sub = subChildrens[i];
      arguments.callee(sub.children, sub.parent, tree);
    }
    if (parent) parent.state.loaded = true;
    tree.fireEvent('loadChildren', parent);
  }

};

Ngn.Tree.implement({

  reload: function(options) {
    Ngn.Tree.Node.UID = 0;
    Ngn.Tree.Nodes = {};
    this.load(options);
  },

  load: function(options) {
    var tree = this;
    this.loadOptions = this.loadOptions || Function.from({});
    function success(json) {
      var childrenRoot = tree.wrapper.getElement('.ngn-tree-children-root');
      if (childrenRoot) childrenRoot.destroy();
      if (json.error) throw new Error(json.error.message); //new Ngn.Dialog.Error({error: json.error});
      if (json.tree === undefined) throw new Error('json has no tree');
      var parent = null;
      if (tree.forest) {
        tree.root = new Ngn.Tree.Node({
          tree: tree,
          parentNode: null
        }, {});
        parent = tree.root;
      }
      Ngn.Tree.Load.children(json.tree, parent, tree);
      Ngn.Tree.Draw[tree.forest ? 'forestRoot' : 'root'](tree);
      tree.$getIndex();
      if (!options.json) tree.fireEvent('load', [json.tree]);
      return tree;
    }
    options = Object.append(Object.append({
      isSuccess: Function.from(true),
      secure: true,
      onSuccess: success,
      method: 'get'
    }, this.loadOptions()), options);
    if (options.json) return success(options.json);
    new Ngn.Request.JSON(options).send();
    return this;
  }

});

Ngn.Tree.Node.implement({

  load: function(options) {
    this.$loading = true;
    options = options || {};
    this.addType('loader');
    var self = this;
    function success(json) {
      Ngn.Tree.Load.children(json, self, self.tree);
      delete self.$loading;
      self.state.loaded = true;
      self.removeType('loader');
      Ngn.Tree.Draw.update(self);
      self.fireEvent('load');
      self.tree.fireEvent('loadNode', self);
      return self;
    }
    options = Object.append(Object.append(Object.append({
      isSuccess: Function.from(true),
      secure: true,
      onSuccess: success,
      method: 'get'
    }, this.tree.loadOptions(this)), this.loadOptions), options);
    if (options.json) return success(options.json);
    new Ngn.Request.JSON(options).send();
    return this;
  }

});


/*
 ---

 name: Ngn.Tree.KeyNav
 description: Ngn.Tree.KeyNav
 license: MIT-Style License (http://mifjs.net/license.txt)
 copyright: Anton Samoylov (http://mifjs.net)
 authors: Anton Samoylov (http://mifjs.net)
 requires: Ngn.Tree
 provides: Ngn.Tree.KeyNav

 ...
 */

Ngn.Tree.KeyNav = new Class({

  initialize: function(tree) {
    this.tree = tree;
    this.bound = {
      action: this.action.bind(this),
      attach: this.attach.bind(this),
      detach: this.detach.bind(this)
    };
    tree.addEvents({
      'focus': this.bound.attach,
      'blur': this.bound.detach
    });
  },

  attach: function() {
    //var event = Browser.ie || Browser.Engine.webkit ? 'keydown' : 'keypress';
    var event = 'keydown';
    document.addEvent(event, this.bound.action);
  },

  detach: function() {
    //var event = Browser.ie || Browser.Engine.webkit ? 'keydown' : 'keypress';
    var event = 'keydown';
    document.removeEvent(event, this.bound.action);
  },

  action: function(event) {
    if (!['down', 'left', 'right', 'up', 'pgup', 'pgdown', 'end', 'home'].contains(event.key)) return;
    var tree = this.tree;
    if (!tree.selected) {
      tree.select(tree.forest ? tree.root.getFirst() : tree.root);
    } else {
      var current = tree.selected;
      switch (event.key) {
        case 'down':
          this.goForward(current);
          event.stop();
          break;
        case 'up':
          this.goBack(current);
          event.stop();
          break;
        case 'left':
          this.goLeft(current);
          event.stop();
          break;
        case 'right':
          this.goRight(current);
          event.stop();
          break;
        case 'home':
          this.goStart(current);
          event.stop();
          break;
        case 'end':
          this.goEnd(current);
          event.stop();
          break;
        case 'pgup':
          this.goPageUp(current);
          event.stop();
          break;
        case 'pgdown':
          this.goPageDown(current);
          event.stop();
          break;
      }
    }
    tree.scrollTo(tree.selected);
  },

  goForward: function(current) {
    var forward = current.getNextVisible();
    if (forward) this.tree.select(forward);
  },

  goBack: function(current) {
    var back = current.getPreviousVisible();
    if (back) this.tree.select(back);
  },

  goLeft: function(current) {
    if (current.isRoot()) {
      if (current.isOpen()) {
        current.toggle();
      } else {
        return false;
      }
    } else {
      if (current.hasChildren(true) && current.isOpen()) {
        current.toggle();
      } else {
        if (current.tree.forest && current.getParent().isRoot()) return false;
        return this.tree.select(current.getParent());
      }
    }
    return true;
  },

  goRight: function(current) {
    if (!current.hasChildren(true) && !current.loadable) {
      return false;
    } else if (!current.isOpen()) {
      return current.toggle();
    } else {
      return this.tree.select(current.getFirst(true));
    }
  },

  goStart: function() {
    this.tree.select(this.tree.$index[0]);
  },

  goEnd: function() {
    this.tree.select(this.tree.$index.getLast());
  },

  goPageDown: function(current) {
    var tree = this.tree;
    var count = (tree.container.clientHeight / tree.height).toInt() - 1;
    var newIndex = Math.min(tree.$index.indexOf(current) + count, tree.$index.length - 1);
    tree.select(tree.$index[newIndex]);
  },

  goPageUp: function(current) {
    var tree = this.tree;
    var count = (tree.container.clientHeight / tree.height).toInt() - 1;
    var newIndex = Math.max(tree.$index.indexOf(current) - count, 0);
    tree.select(tree.$index[newIndex]);
  }

});

// @todo ������������ �-�
//Event.Keys.exntend({
//  'pgdown': 34,
//  'pgup': 33,
//  'home': 36,
//  'end': 35
//});


/*
 ---

 name: Ngn.Tree.Sort
 description: Ngn.Tree.Sort
 license: MIT-Style License (http://mifjs.net/license.txt)
 copyright: Anton Samoylov (http://mifjs.net)
 authors: Anton Samoylov (http://mifjs.net)
 requires: Ngn.Tree
 provides: Ngn.Tree.Sort

 ...
 */

Ngn.Tree.implement({

  initSortable: function(sortFunction) {
    this.sortable = true;
    this.sortFunction = sortFunction || function(node1, node2) {
      if (node1.name > node2.name) {
        return 1;
      } else if (node1.name < node2.name) {
        return -1;
      } else {
        return 0;
      }
    };
    this.addEvent('loadChildren', function(parent) {
      if (parent) parent.sort();
    });
    this.addEvent('structureChange', function(from, to, where, type) {
      from.sort();
    });
    return this;
  }

});


Ngn.Tree.Node.implement({

  sort: function(sortFunction) {
    this.children.sort(sortFunction || this.tree.sortFunction);
    return this;
  }

});


/*
 ---

 name: Ngn.Tree.Transform
 description: implement move/copy/del/add actions
 license: MIT-Style License (http://mifjs.net/license.txt)
 copyright: Anton Samoylov (http://mifjs.net)
 authors: Anton Samoylov (http://mifjs.net)
 requires: Ngn.Tree
 provides: Ngn.Tree.Transform

 ...
 */

Ngn.Tree.Node.implement({

  inject: function(node, where, element) {//element - internal property
    where = where || 'inside';
    var parent = this.parentNode;

    function getPreviousVisible(node) {
      var previous = node;
      while (previous) {
        previous = previous.getPrevious();
        if (!previous) return null;
        if (!previous.hidden) return previous;
      }
      return null;
    }

    var previousVisible = getPreviousVisible(this);
    var type = element ? 'copy' : 'move';
    switch (where) {
      case 'after':
      case 'before':
        if (node['get' + (where == 'after' ? 'Next' : 'Previous')]() == this) return false;
        if (this.parentNode) {
          this.parentNode.children.erase(this);
        }
        this.parentNode = node.parentNode;
        this.parentNode.children.inject(this, node, where);
        break;
      case 'inside':
        if (node.tree && node.getLast() == this) return false;
        if (this.parentNode) {
          this.parentNode.children.erase(this);
        }
        if (node.tree) {
          if (!node.hasChildren()) {
            node.$draw = true;
            node.state.open = true;
          }
          node.children.push(this);
          this.parentNode = node;
        } else {
          node.root = this;
          this.parentNode = null;
          node.fireEvent('drawRoot');
        }
        break;
    }
    var tree = node.tree || node;
    if (this == this.tree.root) {
      this.tree.root = false;
    }
    if (this.tree != tree) {
      var oldTree = this.tree;
      this.recursive(function() {
        this.tree = tree;
      });
    }
    ;
    tree.fireEvent('structureChange', [this, node, where, type]);
    tree.$getIndex();
    if (oldTree)  oldTree.$getIndex();
    Ngn.Tree.Draw.inject(this, element);
    [node, this, parent, previousVisible, getPreviousVisible(this)].each(function(node) {
      Ngn.Tree.Draw.update(node);
    });
    return this;
  },

  copy: function(node, where) {
    if (this.copyDenied) return this;
    function copy(structure) {
      var node = structure.node;
      var tree = structure.tree;
      var options = Object.clone({
        property: node.property,
        type: node.type,
        state: node.state,
        data: node.data
      });
      options.state.open = false;
      var nodeCopy = new Ngn.Tree.Node({
        parentNode: structure.parentNode,
        children: [],
        tree: tree
      }, options);
      node.children.each(function(child) {
        var childCopy = copy({
          node: child,
          parentNode: nodeCopy,
          tree: tree
        });
        nodeCopy.children.push(childCopy);
      });
      return nodeCopy;
    };

    var nodeCopy = copy({
      node: this,
      parentNode: null,
      tree: node.tree
    });
    return nodeCopy.inject(node, where, Ngn.Tree.Draw.node(nodeCopy));
  },

  remove: function() {
    if (this.removeDenied) return;
    this.tree.fireEvent('remove', [this]);
    var parent = this.parentNode, previousVisible = this.getPreviousVisible();
    if (parent) {
      parent.children.erase(this);
    } else if (!this.tree.forest) {
      this.tree.root = null;
    }
    this.tree.selected = false;
    this.getDOM('node').destroy();
    this.tree.$getIndex();
    Ngn.Tree.Draw.update(parent);
    Ngn.Tree.Draw.update(previousVisible);
    this.recursive(function() {
      if (this.id) delete Ngn.Tree.ids[this.id];
    });
    this.tree.mouse.node = false;
    this.tree.updateHover();
  }

});


Ngn.Tree.implement({

  move: function(from, to, where) {
    if (from.inject(to, where)) {
      this.fireEvent('move', [from, to, where]);
    }
    return this;
  },

  copy: function(from, to, where) {
    var copy = from.copy(to, where);
    if (copy) {
      this.fireEvent('copy', [from, to, where, copy]);
    }
    return this;
  },

  remove: function(node) {
    node.remove();
    return this;
  },

  add: function(node, current, where) {
    if (!(node instanceof Ngn.Tree.Node)) {
      node = new Ngn.Tree.Node({
        parentNode: null,
        tree: this
      }, node);
    }
    ;
    node.inject(current, where, Ngn.Tree.Draw.node(node));
    this.fireEvent('add', [node, current, where]);
    return this;
  }

});


/*
 ---

 name: Ngn.Tree.Drag
 description: implements drag and drop
 license: MIT-Style License (http://mifjs.net/license.txt)
 copyright: Anton Samoylov (http://mifjs.net)
 authors: Anton Samoylov (http://mifjs.net)
 requires: [Ngn.Tree, Ngn.Tree.Transform, more:/Drag.Move]
 provides: Ngn.Tree.Drag

 ...
 */

Ngn.Tree.Drag = new Class({

  Implements: [Events, Options],

  Extends: Drag,

  options: {
    group: 'tree',
    droppables: [],
    snap: 4,
    animate: true,
    open: 600,//time to open node
    scrollDelay: 100,
    scrollSpeed: 100,
    modifier: 'control',//copy
    startPlace: ['icon', 'name'],
    allowContainerDrop: true
  },

  initialize: function(tree, options) {
    tree.drag = this;
    this.setOptions(options);
    Object.append(this, {
      tree: tree,
      snap: this.options.snap,
      groups: [],
      droppables: [],
      action: this.options.action
    });

    this.addToGroups(this.options.group);

    this.setDroppables(this.options.droppables);

    Object.append(tree.defaults, {
      dropDenied: [],
      dragDisabled: false
    });
    tree.addEvent('drawRoot', function() {
      tree.root.dropDenied.combine(['before', 'after']);
    });

    this.pointer = new Element('div').addClass('ngn-tree-pointer').inject(tree.wrapper);

    this.current = Ngn.Tree.Drag.current;
    this.target = Ngn.Tree.Drag.target;
    this.where = Ngn.Tree.Drag.where;

    this.element = [this.current, this.target, this.where];
    this.document = tree.wrapper.getDocument();

    this.selection = (Browser.ie) ? 'selectstart' : 'mousedown';

    this.bound = {
      start: this.start.bind(this),
      check: this.check.bind(this),
      drag: this.drag.bind(this),
      stop: this.stop.bind(this),
      cancel: this.cancel.bind(this),
      eventStop: Function.from(false),
      leave: this.leave.bind(this),
      enter: this.enter.bind(this),
      keydown: this.keydown.bind(this)
    };
    this.attach();

    this.addEvent('start', function() {
      Ngn.Tree.Drag.dropZone = this;
      this.tree.unselect();
      document.addEvent('keydown', this.bound.keydown);
      this.setDroppables();
      this.droppables.each(function(item) {
        item.getElement().addEvents({mouseleave: this.bound.leave, mouseenter: this.bound.enter});
      }, this);
      Ngn.Tree.Drag.current.getDOM('name').addClass('ngn-tree-drag-current');
      this.addGhost();
    }, true);
    this.addEvent('complete', function() {
      document.removeEvent('keydown', this.bound.keydown);
      this.droppables.each(function(item) {
        item.getElement().removeEvent('mouseleave', this.bound.leave).removeEvent('mouseenter', this.bound.enter);
      }, this);
      Ngn.Tree.Drag.current.getDOM('name').removeClass('ngn-tree-drag-current');
      var dropZone = Ngn.Tree.Drag.dropZone;
      if (!dropZone || dropZone.where == 'notAllowed') {
        Ngn.Tree.Drag.startZone.onstop();
        Ngn.Tree.Drag.startZone.emptydrop();
        return;
      }
      if (dropZone.onstop) dropZone.onstop();
      dropZone.beforeDrop();
    });
  },

  getElement: function() {
    return this.tree.wrapper;
  },

  addToGroups: function(groups) {
    groups = Array.from(groups);
    this.groups.combine(groups);
    groups.each(function(group) {
      Ngn.Tree.Drag.groups[group] = (Ngn.Tree.Drag.groups[group] || []).include(this);
    }, this);
  },

  setDroppables: function(droppables) {
    this.droppables.combine(Array.from(droppables));
    this.groups.each(function(group) {
      this.droppables.combine(Ngn.Tree.Drag.groups[group]);
    }, this);
  },

  attach: function() {
    this.tree.wrapper.addEvent('mousedown', this.bound.start);
    return this;
  },

  detach: function() {
    this.tree.wrapper.removeEvent('mousedown', this.bound.start);
    return this;
  },

  dragTargetSelect: function() {
    function addDragTarget() {
      this.current.getDOM('name').addClass('ngn-tree-drag-current');
    }

    function removeDragTarget() {
      this.current.getDOM('name').removeClass('ngn-tree-drag-current');
    }

    this.addEvent('start', addDragTarget.bind(this));
    this.addEvent('beforeComplete', removeDragTarget.bind(this));
  },

  leave: function(event) {
    var dropZone = Ngn.Tree.Drag.dropZone;
    if (dropZone) {
      dropZone.where = 'notAllowed';
      Ngn.Tree.Drag.ghost.firstChild.className = 'ngn-tree-ghost-icon ngn-tree-ghost-' + dropZone.where;
      if (dropZone.onleave) dropZone.onleave();
      Ngn.Tree.Drag.dropZone = false;
    }

    var relatedZone = this.getZone(event.relatedTarget);
    if (relatedZone) this.enter(null, relatedZone);
  },

  onleave: function() {
    this.tree.unselect();
    this.clean();
    clearTimeout(this.scrolling);
    this.scrolling = null;
    this.target = false;
  },

  enter: function(event, zone) {
    if (event) zone = this.getZone(event.target);
    var dropZone = Ngn.Tree.Drag.dropZone;
    if (dropZone && dropZone.onleave) dropZone.onleave();
    Ngn.Tree.Drag.dropZone = zone;
    zone.current = Ngn.Tree.Drag.current;
    if (zone.onenter) zone.onenter();
  },

  onenter: function() {
    this.onleave();
  },

  getZone: function(target) {//private leave/enter
    if (!target) return false;
    var parent = $(target);
    do {
      for (var l = this.droppables.length; l--;) {
        var zone = this.droppables[l];
        if (parent == zone.getElement()) {
          return zone;
        }
      }
      parent = parent.getParent();
    } while (parent);
    return false;
  },

  keydown: function(event) {
    if (event.key == 'esc') {
      var zone = Ngn.Tree.Drag.dropZone;
      if (zone) zone.where = 'notAllowed';
      this.stop(event);
    }
  },

  autoScroll: function() {
    var y = this.y;
    if (y == -1) return;
    var wrapper = this.tree.wrapper;
    var top = y - wrapper.scrollTop;
    var bottom = wrapper.offsetHeight - top;
    var sign = 0;
    var delta;
    if (top < this.tree.height) {
      delta = top;
      sign = 1;
    } else if (bottom < this.tree.height) {
      delta = bottom;
      sign = -1;
    }
    if (sign && !this.scrolling) {
      this.scrolling = function(node) {
        if (y != this.y) {
          y = this.y;
          delta = (sign == 1 ? (y - wrapper.scrollTop) : (wrapper.offsetHeight - y + wrapper.scrollTop)) || 1;
        }
        wrapper.scrollTop = wrapper.scrollTop - sign * this.options.scrollSpeed / delta;
      }.periodical(this.options.scrollDelay, this, [sign]);
    }
    if (!sign) {
      clearTimeout(this.scrolling);
      this.scrolling = null;
    }
  },

  start: function(event) {//mousedown
    if (event.rightClick) return;
    if (this.options.preventDefault) event.preventDefault();
    this.fireEvent('beforeStart', this.element);

    var target = this.tree.mouse.target;
    if (!target) return;
    this.current = Array.from(this.options.startPlace).contains(target) ? this.tree.mouse.node : false;
    if (!this.current || this.current.dragDisabled) {
      return;
    }
    Ngn.Tree.Drag.current = this.current;
    Ngn.Tree.Drag.startZone = this;

    this.mouse = {start: event.page};
    this.document.addEvents({mousemove: this.bound.check, mouseup: this.bound.cancel});
    this.document.addEvent(this.selection, this.bound.eventStop);
  },

  drag: function(event) {
    Ngn.Tree.Drag.ghost.position({x: event.page.x + 20, y: event.page.y + 20});
    var dropZone = Ngn.Tree.Drag.dropZone;
    if (!dropZone || !dropZone.ondrag) return;
    Ngn.Tree.Drag.dropZone.ondrag(event);
  },

  ondrag: function(event) {
    this.autoScroll();
    if (!this.checkTarget()) return;
    this.clean();
    var where = this.where;
    var target = this.target;
    var ghostType = where;
    if (where == 'after' && target && (target.getNext()) || where == 'before' && target.getPrevious()) {
      ghostType = 'between';
    }
    Ngn.Tree.Drag.ghost.firstChild.className = 'ngn-tree-ghost-icon ngn-tree-ghost-' + ghostType;
    if (where == 'notAllowed') {
      this.tree.unselect();
      return;
    }
    if (target && target.tree) this.tree.select(target);
    if (where == 'inside') {
      if (target.tree && !target.isOpen() && !this.openTimer && (target.loadable || target.hasChildren())) {
        this.wrapper = target.getDOM('wrapper').setStyle('cursor', 'progress');
        this.openTimer = function() {
          target.toggle();
          this.clean();
        }.delay(this.options.open, this);
      }
    } else {
      var wrapper = this.tree.wrapper;
      var top = this.index * this.tree.height;
      if (where == 'after') top += this.tree.height;
      this.pointer.setStyles({
        left: wrapper.scrollLeft,
        top: top,
        width: wrapper.clientWidth
      });
    }
  },

  clean: function() {
    this.pointer.style.width = 0;
    if (this.openTimer) {
      clearTimeout(this.openTimer);
      this.openTimer = false;
      this.wrapper.style.cursor = 'inherit';
      this.wrapper = false;
    }
  },

  addGhost: function() {
    var wrapper = this.current.getDOM('wrapper');
    var ghost = new Element('span').addClass('ngn-tree-ghost');
    ghost.adopt(Ngn.Tree.Draw.node(this.current).getFirst()).inject(document.body).addClass('ngn-tree-ghost-notAllowed').setStyle('position', 'absolute');
    new Element('span').set('html', Ngn.Tree.Draw.zeroSpace).inject(ghost, 'top');
    ghost.getLast().getFirst().className = '';
    Ngn.Tree.Drag.ghost = ghost;
  },

  checkTarget: function() {
    this.y = this.tree.mouse.coords.y;
    var target = this.tree.mouse.node;
    if (!target) {
      if (this.options.allowContainerDrop && (this.tree.forest || !this.tree.root)) {
        this.target = this.tree.$index.getLast();
        this.index = this.tree.$index.length - 1;
        if (this.index == -1) {
          this.where = 'inside';
          this.target = this.tree.root || this.tree;
        } else {
          this.where = 'after';
        }
      } else {
        this.target = false;
        this.where = 'notAllowed';
      }
      this.fireEvent('drag');
      return true;
    }
    ;
    if ((this.current instanceof Ngn.Tree.Node) && this.current.contains(target)) {
      this.target = target;
      this.where = 'notAllowed';
      this.fireEvent('drag');
      return true;
    }
    ;
    this.index = Math.floor(this.y / this.tree.height);
    var delta = this.y - this.index * this.tree.height;
    var deny = target.dropDenied;
    if (this.tree.sortable) {
      deny.include('before').include('after');
    }
    ;
    var where;
    if (!deny.contains('inside') && delta > (this.tree.height / 4) && delta < (3 / 4 * this.tree.height)) {
      where = 'inside';
    } else {
      if (delta < this.tree.height / 2) {
        if (deny.contains('before')) {
          if (deny.contains('inside')) {
            where = deny.contains('after') ? 'notAllowed' : 'after';
          } else {
            where = 'inside';
          }
        } else {
          where = 'before';
        }
      } else {
        if (deny.contains('after')) {
          if (deny.contains('inside')) {
            where = deny.contains('before') ? 'notAllowed' : 'before';
          } else {
            where = 'inside';
          }
        } else {
          where = 'after';
        }
      }
    }
    ;
    if (this.where == where && this.target == target) return false;
    this.where = where;
    this.target = target;
    this.fireEvent('drag');
    return true;
  },

  emptydrop: function() {
    var current = this.current, target = this.target, where = this.where;
    var scroll = this.tree.scroll;
    var complete = function() {
      scroll.removeEvent('complete', complete);
      if (this.options.animate) {
        var wrapper = current.getDOM('wrapper');
        var position = wrapper.getPosition();
        Ngn.Tree.Drag.ghost.set('morph', {
          duration: 'short',
          onComplete: function() {
            Ngn.Tree.Drag.ghost.dispose();
            this.fireEvent('emptydrop', this.element);
          }.bind(this)
        });
        Ngn.Tree.Drag.ghost.morph({left: position.x, top: position.y});
        return;
      }
      ;
      Ngn.Tree.Drag.ghost.dispose();
      this.fireEvent('emptydrop', this.element);
      return;
    }.bind(this);
    scroll.addEvent('complete', complete);
    this.tree.select(this.current);
    this.tree.scrollTo(this.current);
  },

  beforeDrop: function() {
    if (this.options.beforeDrop) {
      this.options.beforeDrop.apply(this, [this.current, this.target, this.where]);
    } else {
      this.drop();
    }
  },

  drop: function() {
    var current = this.current, target = this.target, where = this.where;
    Ngn.Tree.Drag.ghost.dispose();
    var action = this.action || (this.tree.key[this.options.modifier] ? 'copy' : 'move');
    if (this.where == 'inside' && target.tree && !target.isOpen()) {
      if (target.tree) target.toggle();
      if (target.$loading) {
        var onLoad = function() {
          this.tree[action](current, target, where);
          this.tree.select(current).scrollTo(current);
          this.fireEvent('drop', [current, target, where]);
          target.removeEvent('load', onLoad);
        };
        target.addEvent('load', onLoad);
        return;
      }
      ;
    }
    ;
    if (!(current instanceof Ngn.Tree.Node )) {
      current = current.toNode(this.tree);
    }
    this.tree[action](current, target, where);
    this.tree.select(current).scrollTo(current);
    this.fireEvent('drop', [current, target, where]);
  },

  onstop: function() {
    this.clean();
    clearTimeout(this.scrolling);
  }
});

Ngn.Tree.Drag.groups = {};


/*
 ---

 name: Ngn.Tree.Drag.Element
 description: dom element droppable
 license: MIT-Style License (http://mifjs.net/license.txt)
 copyright: Anton Samoylov (http://mifjs.net)
 authors: Anton Samoylov (http://mifjs.net)
 requires: Ngn.Tree.Drag
 provides: Ngn.Tree.Drag.Element

 ...
 */

Ngn.Tree.Drag.Element = new Class({

  Implements: [Options, Events],

  initialize: function(element, options) {

    this.element = $(element);

    this.setOptions(options);

  },

  getElement: function() {
    return this.element;
  },

  onleave: function() {
    this.where = 'notAllowed';
    Ngn.Tree.Drag.ghost.firstChild.className = 'ngn-tree-ghost-icon ngn-tree-ghost-' + this.where;
  },

  onenter: function() {
    this.where = 'inside';
    Ngn.Tree.Drag.ghost.firstChild.className = 'ngn-tree-ghost-icon ngn-tree-ghost-' + this.where;
  },

  beforeDrop: function() {
    if (this.options.beforeDrop) {
      this.options.beforeDrop.apply(this, [this.current, this.trarget, this.where]);
    } else {
      this.drop();
    }
  },

  drop: function() {
    Ngn.Tree.Drag.ghost.dispose();
    this.fireEvent('drop', Ngn.Tree.Drag.current);
  }


});


/*
 ---

 name: Ngn.Tree.Rename
 description: Ngn.Tree.Rename
 license: MIT-Style License (http://mifjs.net/license.txt)
 copyright: Anton Samoylov (http://mifjs.net)
 authors: Anton Samoylov (http://mifjs.net)
 requires: Ngn.Tree
 provides: Ngn.Tree.Rename

 ...
 */

Ngn.Tree.implement({

  attachRenameEvents: function() {
    this.wrapper.addEvents({
      click: function(event) {
        if ($(event.target).get('tag') == 'input') return;
        this.beforeRenameComplete();
      }.bind(this),
      keydown: function(event) {
        if (event.key == 'enter') {
          this.beforeRenameComplete();
        }
        if (event.key == 'esc') {
          this.renameCancel();
        }
      }.bind(this)
    });
  },

  disableEvents: function() {
    if (!this.eventStorage) this.eventStorage = new Element('div');
    this.eventStorage.cloneEvents(this.wrapper);
    this.wrapper.removeEvents();
  },

  enableEvents: function() {
    this.wrapper.removeEvents();
    this.wrapper.cloneEvents(this.eventStorage);
  },

  getInput: function() {
    if (!this.input) {
      this.input = new Element('input').addClass('ngn-tree-rename');
      this.input.addEvent('focus',function() {
        this.select();
      }).addEvent('click', function(event) {
        event.stop();
      });
      Ngn.Tree.Rename.autoExpand(this.input);
    }
    return this.input;
  },

  startRename: function(node) {
    this.focus();
    this.unselect();
    this.disableEvents();
    this.attachRenameEvents();
    var input = this.getInput();
    input.value = node.name;
    this.renameName = node.getDOM('name');
    this.renameNode = node;
    input.setStyle('width', this.renameName.offsetWidth + 15);
    input.replaces(this.renameName);
    input.focus();
  },

  finishRename: function() {
    this.renameName.replaces(this.getInput());
  },

  beforeRenameComplete: function() {
    if (this.options.beforeRename) {
      var newName = this.getInput().value;
      var node = this.renameNode;
      this.options.beforeRename.apply(this, [node, node.name, newName]);
    } else {
      this.renameComplete();
    }
  },

  renameComplete: function() {
    this.enableEvents();
    this.finishRename();
    var node = this.renameNode;
    var oldName = node.name;
    node.set({
      property: {
        name: this.getInput().value
      }
    });
    this.fireEvent('rename', [node, node.name, oldName]);
    this.select(node);
  },

  renameCancel: function() {
    this.enableEvents();
    this.finishRename();
    this.select(this.renameNode);
  }

});

Ngn.Tree.Node.implement({

  rename: function() {
    if (this.property.renameDenied) return;
    this.tree.startRename(this);
  }

});

Ngn.Tree.Rename = {

  autoExpand: function(input) {
    var span = new Element('span').addClass('ngn-tree-rename').setStyles({
      position: 'absolute',
      left: -2000,
      top: 0,
      padding: 0
    }).inject(document.body);
    input.addEvent('keydown', function(event) {
      (function() {
        input.setStyle('width', Math.max(20, span.set('html', input.value.replace(/\s/g, '&nbsp;')).offsetWidth + 15));
      }).delay(10);
    });
  }

};


/*
 ---

 name: Ngn.Tree.Checkbox
 description: Ngn.Tree.Checkbox
 license: MIT-Style License (http://mifjs.net/license.txt)
 copyright: Anton Samoylov (http://mifjs.net)
 authors: Anton Samoylov (http://mifjs.net)
 requires: Ngn.Tree
 provides: Ngn.Tree.Checkbox

 ...
 */

Ngn.Tree.implement({

  initCheckbox: function(type) {
    this.checkboxType = type || 'simple';
    this.dfltState.checked = 'unchecked';
    this.defaults.hasCheckbox = true;
    this.wrapper.addEvent('click', this.checkboxClick.bind(this));
    if (this.checkboxType == 'simple') return;
    this.addEvent('loadChildren', function(node) {
      if (!node) return;
      if (node.state.checked == 'checked') {
        node.recursive(function() {
          this.state.checked = 'checked';
        });
      } else {
        node.getFirst().setParentCheckbox(1);
      }
    });

  },

  checkboxClick: function(event) {
    if (this.mouse.target != 'checkbox') {
      return;
    }
    this.mouse.node['switch']();
  },

  getChecked: function(includePartially) {
    var checked = [];
    this.root.recursive(function() {
      var condition = includePartially ? this.state.checked !== 'unchecked' : this.state.checked == 'checked';
      if (this.hasCheckbox && condition) checked.push(this);
    });
    return checked;
  }

});

Ngn.Tree.Node.implement({

  'switch': function(state) {
    if (this.state.checked == state || !this.hasCheckbox) return this;
    var type = this.tree.checkboxType;
    var checked = (this.state.checked == 'checked') ? 'unchecked' : 'checked';
    if (type == 'simple') {
      this.setCheckboxState(checked);
      this.tree.fireEvent(checked == 'checked' ? 'check' : 'unCheck', this);
      this.tree.fireEvent('switch', [this, (checked == 'checked' ? true : false)]);
      return this;
    }
    ;
    this.recursive(function() {
      this.setCheckboxState(checked);
    });
    this.setParentCheckbox();
    this.tree.fireEvent(checked == 'checked' ? 'check' : 'unCheck', this);
    this.tree.fireEvent('switch', [this, (checked == 'checked' ? true : false)]);
    return this;
  },

  setCheckboxState: function(state) {
    if (!this.hasCheckbox) return;
    var oldState = this.state.checked;
    this.state.checked = state;
    if ((!this.parentNode && this.tree.$draw) || (this.parentNode && this.parentNode.$draw)) {
      this.getDOM('checkbox').removeClass('ngn-tree-node-' + oldState).addClass('ngn-tree-node-' + state);
    }
  },

  setParentCheckbox: function(s) {
    if (!this.hasCheckbox || !this.parentNode || (this.tree.forest && !this.parentNode.parentNode)) return;
    var parent = this.parentNode;
    var state = '';
    var children = parent.children;
    for (var i = children.length; i--; i > 0) {
      var child = children[i];
      if (!child.hasCheckbox) continue;
      var childState = child.state.checked;
      if (childState == 'partially') {
        state = 'partially';
        break;
      } else if (childState == 'checked') {
        if (state == 'unchecked') {
          state = 'partially';
          break;
        }
        state = 'checked';
      } else {
        if (state == 'checked') {
          state = 'partially';
          break;
        } else {
          state = 'unchecked';
        }
      }
    }
    if (parent.state.checked == state || (s && state == 'partially' && parent.state.checked == 'checked')) {
      return;
    }
    parent.setCheckboxState(state);
    parent.setParentCheckbox(s);
  }

});
