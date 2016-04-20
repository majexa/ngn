/**
 * Usage example:
 *
 * var dialog = new Ngn.Dialog.ImageSelect({
 *   images: [
 *     {
 *       url: 'http://google.com/logo.png',
 *       id: 1
 *     }
 *   ],
 *   onOkClose: function() {
 *     console.debug(this.selectedImageId);
 *   }
 * });
 *
 * @type {Class}
 */
Ngn.Dialog.ImageSelect = new Class({
  Extends: Ngn.Dialog,
  options: {
    images: [], // Array of objects like that: {url: '/path/to/image', id: 'imageId'}
    bindBuildMessageFunction: true
  },
  buildMessage: function() {
    var eContainer = new Element('div');
    new Element('img', {
      src: this.options.images[i].url,
      'data-id': this.options.images[i].id
    }).addEvent('click', function() {
        console.debug(this.get('data-id'));
      }).inject(eContainer);
    return eContainer;
  }
});
