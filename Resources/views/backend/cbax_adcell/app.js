// {namespace name="backend/cbax_adcell/app"}
//{block name="backend/cbax_adcell/app"}
Ext.define('Shopware.apps.CbaxAdcell', {
    extend: 'Enlight.app.SubApplication',

    name: 'Shopware.apps.CbaxAdcell',

    loadPath: '{url action=load}',
    bulkLoad: true,

    controllers: ['Main'],

    views: [
        'list.Window',
        'list.List',

        'detail.Container',
        'detail.Window'
    ],

    models: ['Main'],
    stores: ['Main'],

    launch: function () {
        return this.getController('Main').mainWindow;
    }
});
//{/block}
