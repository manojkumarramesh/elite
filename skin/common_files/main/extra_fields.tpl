{strip}
<table width="80%" cellspacing="0" cellpadding="4" class="product-details-table">

    <tr>
        <td class="FormButton" nowrap="nowrap" colspan="2"><h2>Extra Fields</h2></td>
    </tr>

    {foreach from=$extra_fields_data key=k item=i}

        <tr>

            <td class="FormButton" nowrap="nowrap"><span id="label_efields_{$i.fieldid}">{$i.field}</span> : </td>

            {if $i.type eq 'textbox'}

                <td class="ProductDetails">
                    <input type="text" name="efields[{$i.fieldid}]" id="efields_{$i.fieldid}" value="{$i.value}" size="" />
                </td>
                
            {elseif $i.type eq 'variant'}

                <td class="ProductDetails">
                    <input type="hidden" name="efields[{$i.fieldid}]" id="efields_{$i.fieldid}" value="{$i.field}" />&nbsp;
                    <input type="hidden" name="variantz_field[]" id="variant_efields_{$i.fieldid}" value="{$i.field}" />&nbsp;
                    <span id="variant_edit_{$i.fieldid}">You can define values after saving product</span>
                </td>

            {elseif $i.type eq 'dropdown'}

                <td class="ProductDetails">
                    {assign var="variants" value=";"|explode:$i.value}
                    <select id="efields_{$i.fieldid}" name="efields[{$i.fieldid}]" {if $i.fieldid eq '31'} onchange="fetch_dropdown_level_1(this);" {/if}>
                        <option value="0">---SELECT---</option>
                        {foreach from=$variants key=ke item=it}
                            <option value="{$it|lower}">{$it}</option>
                        {/foreach}
                    </select>
                </td>

            {elseif $i.type eq 'dropdown_level_1'}

                <td class="ProductDetails">
                    <span id="dropdown_level_1"></span>
                </td>

            {/if}

        </tr>

    {/foreach}

</table>
{/strip}