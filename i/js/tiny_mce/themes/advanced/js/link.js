Ngn.Dialog.Tiny = new Class({
  Extends: Ngn.Dialog.Form,

  initialize: function(options) {
    this.parent(options);
    this.editor = tinyMCE.activeEditor;
    this.addEvent('submit', this.updateTiny.bind(this));
  },

  updateTiny: function() {
  }

});

Ngn.Dialog.Tiny.Link = new Class({
  Extends: Ngn.Dialog.Tiny,

  options: {
    dialogClass: 'dialog fieldFullWidth',
    id: 'tinyLink',
    width: 250,
    top: 20,
    url: '/' + Ngn.sflmFrontend + '/tinyLink'
  },

  formInit: function() {
    this.parent();
    this.eLink = this.message.getElement('input[name=link]');
    //this.eLink.addEvent('change', this.checkPrefix(this.eLink));
    var eA;
    if (eA = this.editor.dom.getParent(this.editor.selection.getNode(), 'A')) {
      this.eLink.set('value', this.editor.dom.getAttrib(eA, 'href'));
    }
  },

  checkPrefix: function(n) {
    if (n.value && Validator.isEmail(n) && !/^\s*mailto:/i.test(n.value) && confirm('asd'))
      n.value = 'mailto:' + n.value;
    if (/^\s*www\./i.test(n.value) && confirm('asdsad'))
      n.value = 'http://' + n.value;
  },

  updateTiny: function() {
    var ed = this.editor;
    var eA = ed.dom.getParent(ed.selection.getNode(), 'A');
    // Remove element if there is no href
    if (!this.eLink.get('value')) {
      if (eA) {
        ed.execCommand("mceBeginUndoLevel");
        var b = ed.selection.getBookmark();
        ed.dom.remove(eA, 1);
        ed.selection.moveToBookmark(b);
        ed.execCommand("mceEndUndoLevel");
        return;
      }
    }
    ed.execCommand("mceBeginUndoLevel");
    // Create new anchor elements
    if (eA == null) {
      ed.getDoc().execCommand("unlink", false, null);
      ed.execCommand("CreateLink", false, "#mce_temp_url#", {skip_undo: 1});
      tinymce.each(ed.dom.select("a"), function(n) {
        if (ed.dom.getAttrib(n, 'href') == '#mce_temp_url#') {
          eA = n;
          ed.dom.setAttrib(eA, 'href', this.eLink.get('value'));
        }
      }.bind(this));
    } else {
      ed.dom.setAttrib(eA, 'href', this.eLink.get('value'));
    }
    if (eA) {
      // Don't move caret if selection was image
      if (eA.childNodes.length != 1 || eA.firstChild.nodeName != 'IMG') {
        ed.focus();
        ed.selection.select(eA);
        ed.selection.collapse(0);
      }
    }
  }

});
