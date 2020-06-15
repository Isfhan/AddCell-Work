// {namespace name="backend/cbax_adcell/list/window"}
//{block name="backend/cbax_adcell/list/window"}
Ext.define('Shopware.apps.CbaxAdcell.view.list.Window', {
    extend: 'Shopware.window.Listing',
    alias: 'widget.cbax-adcell-list-window',
    height: 450,
    title : '{s name=window_title}Adcell Event List{/s}',

    configure: function() {
        return {
            listingGrid: 'Shopware.apps.CbaxAdcell.view.list.List',
            listingStore: 'Shopware.apps.CbaxAdcell.store.Main'
        };
    }
});
//{/block}
