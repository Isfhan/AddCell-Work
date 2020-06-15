{extends file="parent:frontend/index/index.tpl"}

{block name='frontend_index_body_inline'}

    {$smarty.block.parent}

    {if $AdcellTracking}
        <script type="text/javascript" src="https://t.adcell.com/js/trad.js"></script>
        <script>
          Adcell.Tracking.track()
        </script>
    {/if}

    {if $Adcell.retargeting}
        {if $Adcell.typ == 'start'}
            <script type="text/javascript"
                    src="https://www.adcell.de/js/inlineretarget.js?method=track&pid={$Adcell.pid}&type=startpage"
                    async></script>
        {elseif $Adcell.typ == 'category'}
            <script type="text/javascript"
                    src="https://www.adcell.de/js/inlineretarget.js?method=category&pid={$Adcell.pid}&categoryName={$Adcell.categoryName|escape}&categoryId={$Adcell.categoryId}&productIds={$Adcell.productIds}&productSeparator=;"
                    async></script>
        {elseif $Adcell.typ == 'product'}
            <script type="text/javascript"
                    src="https://www.adcell.de/js/inlineretarget.js?method=product&pid={$Adcell.pid}&productId={$Adcell.productId}&productName={$Adcell.productName|escape}&categoryId={$Adcell.categoryId}&productIds={$Adcell.productIds}&productSeparator=;"
                    async></script>
        {elseif $Adcell.typ == 'search'}
            <script type="text/javascript"
                    src="https://www.adcell.de/js/inlineretarget.js?method=search&pid={$Adcell.pid}&search={$Adcell.search|escape}&productIds={$Adcell.productIds}&productSeparator=;"
                    async></script>
        {elseif $Adcell.typ == 'cart' || $Adcell.typ == 'confirm'}
            {foreach $AdcellEvents as $Adcell}
                <script type="text/javascript"
                        src="https://www.adcell.de/js/inlineretarget.js?method=basket&pid={$Adcell.pid}&productIds={$Adcell.productIds}&quantities={$Adcell.quantities}&basketProductCount={$Adcell.basketProductCount}&basketTotal={$Adcell.basketTotal}&productSeparator=;"
                        async></script>
            {/foreach}
        {elseif $Adcell.typ == 'finish'}
            {foreach $AdcellEvents as $Adcell}
                <script type="text/javascript"
                        src="https://www.adcell.de/js/inlineretarget.js?method=checkout&pid={$Adcell.pid}&basketTotal={$Adcell.basketTotal}&basketProductCount={$Adcell.basketProductCount}&productIds={$Adcell.productIds}&quantities={$Adcell.quantities}&productSeparator=;"
                        async></script>
                <script type="text/javascript"
                        src="https://t.adcell.com/t/track.js?eventid={$Adcell.eventid}&pid={$Adcell.pid}&referenz={$Adcell.basketId}&betrag={$Adcell.basketTotalNetto}"></script>
                <noscript>
                    <img src="https://t.adcell.com/t/track?pid={$Adcell.pid}&eventid={$Adcell.eventid}&referenz={$Adcell.basketId}&betrag={$Adcell.basketTotalNetto}"
                         border="0"
                         width="1"
                         height="1">
                </noscript>
            {/foreach}
        {/if}
    {/if}
{/block}
