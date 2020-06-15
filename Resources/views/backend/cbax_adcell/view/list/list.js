// {namespace name="backend/cbax_adcell/list/list"}
//{block name="backend/cbax_adcell/list/list"}
Ext.define('Shopware.apps.CbaxAdcell.view.list.List', {
  extend: 'Shopware.grid.Panel',
  alias: 'widget.cbax-adcell-listing-grid',
  region: 'center',

  snippets: {
    eventId: '{s name="adcellEvent"}Event ID{/s}',
    provisionId: '{s name="adcellProvisionID"}Provision ID{/s}',
    assignArticle: '{s name="adcellAssignArticle"}Assign Article{/s}',
  },

  configure: function () {
    var me = this

    return {
      detailWindow: 'Shopware.apps.CbaxAdcell.view.detail.Window',
      columns: {
        name: { header: me.snippets.eventId },
        pid: { header: me.snippets.provisionId },
      },
    }
  }
});
//{/block}
