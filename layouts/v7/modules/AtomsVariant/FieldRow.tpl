{strip}
{foreach from=$OPTIONS item=LABEL key=NAME}
<tr>
    <td class=" fieldLabel"><label>{$LABEL}</label></td>
    <td class=" fieldValue">
        <div class="col-lg-6 col-md-6 col-sm-12">
            <select class="select2 inputElement col-lg-12 col-md-12 col-lg-12 variant_field" data-fieldname="{$NAME}" name="{$NAME}">
                <option value="">{vtranslate('LBL_SELECT_OPTION','Vtiger')}</option>
                {foreach from=$OPTIONS_VALUES key=ID item=VALUE}
                    <option value="{$VALUE}">{$VALUE}</option>
                {/foreach}
            </select>
        </div>
    </td>
</tr>
{/foreach}
{/strip}
