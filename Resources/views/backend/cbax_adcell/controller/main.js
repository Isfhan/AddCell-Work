// {namespace name="backend/cbax_adcell/controller/main"}
//{block name="backend/cbax_adcell/controller/main"}
Ext.define('Shopware.apps.CbaxAdcell.controller.Main', {
    extend: 'Enlight.app.Controller',

    init: function() {
        var me = this;
        me.mainWindow = me.getView('list.Window').create({ }).show();
    }
});
//{/block}
