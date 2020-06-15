// {namespace name="backend/cbax_adcell/detail/container"}
//{block name="backend/cbax_adcell/detail/container"}
Ext.define('Shopware.apps.CbaxAdcell.view.detail.Container', {
  extend: 'Shopware.model.Container',
  padding: 20,

  snippets: {
    eventId: '{s name="adcellEvent"}Event ID{/s}',
    provisionId: '{s name="adcellProvisionID"}Provision ID{/s}',
    addEventTitle: '{s name=adcellEditEvent}Add/Edit Event{/s}'
  },

  configure: function () {
    let me = this;
    return {
      controller: 'CbaxAdcell',
      fieldSets: [
        {
          title: me.snippets.addEventTitle,
          fields: {
            name: {
              fieldLabel: me.snippets.eventId,
              allowBlank: false
            },
            pid: {
              fieldLabel: me.snippets.provisionId,
              allowBlank: false
            }
          }
        }
      ]
    }
  }
})
//{/block}
