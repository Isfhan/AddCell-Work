// {namespace name="backend/cbax_adcell/model/main"}
//{block name="backend/cbax_adcell/model/main"}
Ext.define('Shopware.apps.CbaxAdcell.model.Main', {
  extend: 'Shopware.data.Model',

  configure: function () {
    return {
      controller: 'CbaxAdcell',
      detail: 'Shopware.apps.CbaxAdcell.view.detail.Container'
    }
  },

  fields: [
    { name: 'id', type: 'int', useNull: true },
    { name: 'name', type: 'string', useNull: false },
    { name: 'pid', type: 'string', useNull: false }
  ]
})
//{/block}
