// {namespace name="backend/cbax_adcell/store/main"}
//{block name="backend/cbax_adcell/store/main"}
Ext.define('Shopware.apps.CbaxAdcell.store.Main', {
    extend:'Shopware.store.Listing',

    configure: function() {
        return {
            controller: 'CbaxAdcell'
        };
    },
    model: 'Shopware.apps.CbaxAdcell.model.Main'
});
//{/block}
