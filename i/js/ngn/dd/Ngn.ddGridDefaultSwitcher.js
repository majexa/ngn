Ngn.ddGridDefaultSwitcher = {
  /**
   * @param Ngn.Items
   * @param items data row
   * @return {Object}
   */
  getOptions: function(items, row) {
    return {
      classOn: 'turnOn',
      classOff: 'turnOff',
      linkOn: items.getLink() + '?a=ajax_confirm&' + items.options.idParam + '=' + row.id,
      linkOff: items.getLink() + '?a=ajax_deconfirm&' + items.options.idParam + '=' + row.id,
      titleOn: 'Подтвердить как прочитанное',
      titleOff: 'Не подтверждать',
      onComplete: function(enabled) {
        items.fireEvent('reloadComplete', row.id);
      }
    };
  }

};