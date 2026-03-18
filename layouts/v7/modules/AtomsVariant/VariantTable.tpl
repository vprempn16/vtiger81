{strip}
<table class="table table-bordered">
    <thead>
        <tr>
            {foreach from=$HEADERS item=HEADER}
                <th>{vtranslate($HEADER, 'Vtiger')}</th>
            {/foreach}
                 <th>{vtranslate('Action', 'Vtiger')}</th>
        </tr>
    </thead>
    <tbody>
        {foreach from=$RECORDS item=ROW}
            <tr class="listViewVariantEntries" 
                data-id="{$ROW['productid']}" 
                data-info="{$DATA_INFO}" 
                data-url="{$DATA_URL}" 
                data-name="{$ROW.productname}"
                id="Products_popUpListView_row_{$ROW['productid']}"
                data-variantinfo="{$ROW.variantinfo}">

                {foreach from=$HEADERS item=ITEM key=FIELDNAME}
                    <td class="listViewEntryValue textOverflowEllipsis">{$ROW[$FIELDNAME]}</td>
                {/foreach}
                <td>
                    <button data-id="{$ROW['productid']}" data-variantinfo="{$ROW.variantinfo}"  data-info="{$DATA_INFO}" data-url="{$DATA_URL}" data-name="{$ROW.productname}" class="btn btn-primary listViewVariantEntries">
                        Select
                    </button>
                </td>
            </tr>
        {/foreach}
    </tbody>
</table>
{/strip}

