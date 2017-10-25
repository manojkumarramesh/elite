{*
$Id: product_details.tpl,v 1.7.2.2 2011/06/28 09:49:21 aim Exp $
vim: set ts=2 sw=2 sts=2 et:
*}
{literal}
    <style type="text/css">
        .overlays {
            background-color: #000000;
            opacity: 0.3;
            z-index: 1000;
        }
        li { 
            list-style-type: none;
        }
        .tax, .selling, .vpc, .sku, .hide, #button-next-provider, #button-next-admin, #image_save_msg, .condition-new, .manufacturer {
            display: none;
        }
        #product, #upc_ean_gtin_product_id, #mpn_model_number, #brand_manufacturer_name {
            background-image: url("../skin/ability/custom/images/icons/star.png");
            background-repeat: no-repeat;
            background-position: right top;
        }
        .ui-widget-content a {
            color: blue;
            text-decoration: none;
        }
        .mandatory {
            color: #880000;
        }
        .jquery-lightbox-overlay {
            opacity: 0.2;
        }
        #list_price, #price, #lowest_possible_price, #vendor_cost, #map_price, #whole_sale_price {
            text-align: right;
        }
    </style>
{/literal}

{if $usertype eq 'A'}
    {literal}
        <style type="text/css">
            .ui-autocomplete {
                width: 517px;
            }
        </style>
    {/literal}
{else}
    {literal}
        <style type="text/css">
            .ui-autocomplete {
                width: 570px;
            }
        </style>
    {/literal}
{/if}

{if $product.productid eq ''}
    {literal}
        <style type="text/css">
            #div-two {
                display: none;
            }
        </style>
    {/literal}
{/if}

{if $product.saleid eq '0'}
    {literal}
        <style type="text/css">
            .lowest {
                display: none;
            }
        </style>
    {/literal}
{/if}

{if $product.shipping_id eq '2'}
    {literal}
        <style type="text/css">
            .fixed-price {
                display: none;
            }
        </style>
    {/literal}
{elseif $product.shipping_id eq '3'}
    {literal}
        <style type="text/css">
            .real-time {
                display: none;
            }
        </style>
    {/literal}
{else}
    {literal}
        <style type="text/css">
            .fixed-price, .real-time {
                display: none;
            }
        </style>
    {/literal}
{/if}

{capture name=dialog}

{include file="check_clean_url.tpl"}
{include file="main/product_details_js.tpl"}
{include file="check_required_fields_js.tpl"}

{if $usertype eq "A"}
    {load_defer file="custom/js/custom.js" type="js"}
{/if}

<br />
{if $productid_prev ne ""}<a href="product_modify.php?productid={$productid_prev}">&lt;&lt;&nbsp;Product : {$productid_prev}</a>{/if}
{if $productid_next ne ""}{if $productid_prev ne ""} | {/if}<a href="product_modify.php?productid={$productid_next}">Product : {$productid_next}&nbsp;&gt;&gt;</a>{/if}
<br /><br />

<form action="product_modify.php" method="post" name="modifyform" onsubmit="javascript: return checkRequired(requiredFields){if $config.SEO.clean_urls_enabled eq "Y"} &amp;&amp;checkCleanUrl(document.modifyform.clean_url)&amp;&amp;checkRequiredFields(){/if}">

<input type="hidden" id="productid" name="productid" value="{$product.productid}" />
<input type="hidden" name="section" value="main" />
<input type="hidden" name="mode" value="{if $is_pconf}pconf{else}product_modify{/if}" />
<input type="hidden" name="geid" value="{$geid}" />

<div id="div-one">

<table cellpadding="4" cellspacing="0" width="100%" class="product-details-table">
    
{*
<tr>
{if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
<td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_product_owner}</td>
</tr>
*}

<tr> 
    {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
    <td class="FormButton" width="10%" nowrap="nowrap">{$lng.lbl_provider}:</td>
    <td class="ProductDetails" width="90%">
        {if $usertype eq "A" and $new_product eq 1}
        <select name="provider" id="provider" class="InputWidth">
            <option value="0">--- Select ---</option>
            {section name=prov loop=$providers}
            <option value="{$providers[prov].id}"{if $product.provider eq $providers[prov].id} selected="selected"{/if}>{$providers[prov].login} ({$providers[prov].title} {$providers[prov].lastname} {$providers[prov].firstname})</option>
        {/section}
        </select>
            {if $top_message.fillerror ne "" and $product.provider eq ""}<font class="Star">&lt;&lt;</font>{/if}
        {else}
            {assign var="pro" value="1"}
            <span id="provider" class="{$provider_info.id}">{$provider_info.title} {$provider_info.lastname} {$provider_info.firstname} ({$provider_info.login})</span>
        {/if}
    </td>
</tr>

{*
<tr> 
{if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
<td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_classification}</td>
</tr>
*}

{* style="display: none;" 
<tr style="display: nones;"> 
{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[categoryid]" /></td>{/if}
<td class="FormButton" nowrap="nowrap">$lng.lbl_main_category</td>
<td class="ProductDetails">{include file="main/category_selector.tpl" field="categoryid" extra=' class="InputWidth"' categoryid=$product.categoryid|default:$default_categoryid}
{if $top_message.fillerror ne "" and $product.categoryid eq ""}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>
*}

{* style="display: none;" 
<tr style="display: nones;">
{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[categoryids]" /></td>{/if}
<td class="FormButton" nowrap="nowrap">{$lng.lbl_additional_categories}:</td>
<td class="ProductDetails">
<select name="categoryids[]" class="InputWidth" multiple="multiple" size="8">
{foreach from=$allcategories item=c key=catid}
<option value="{$catid}"{if $product.add_categoryids[$catid]} selected="selected"{/if}>{$c}</option>
{/foreach}
</select>
</td>
</tr>
*}

<tr> 
    <td colspan="3"> 
        <input type="hidden" name="categoryid" id="categoryid" value="{$product.categoryid}" />
    </td>
</tr>

{include file="main/select_category.tpl"}

{*
<tr>
    <td class="FormButton" nowrap="nowrap">Selected Category : </td>
    <td class="ProductDetails">
        {foreach from=$allcategories item=c key=catid}
            {if $catid eq $product.categoryid}
                {$c|escape}
            {/if}
        {/foreach}
    </td>
</tr>
*}

{* style="display: none;" *}
<tr style="display: none;"> 
{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[forsale]" /></td>{/if}
<td class="FormButton" nowrap="nowrap">{$lng.lbl_availability}:</td>
<td class="ProductDetails">
<select name="forsale" id="forsale">
<option value="Y"{if $product.forsale eq "Y" or $product.forsale eq ""} selected="selected"{/if}>{$lng.lbl_avail_for_sale}</option>
<option value="H"{if $product.forsale eq "H"} selected="selected"{/if}>{$lng.lbl_hidden}</option>
<option value="N"{if $product.forsale ne "Y" and $product.forsale ne "" and $product.forsale ne "H" and ($product.forsale ne "B" or not $active_modules.Product_Configurator)} selected="selected"{/if}>{$lng.lbl_disabled}</option>
{if $active_modules.Product_Configurator and not $is_pconf}
<option value="B"{if $product.forsale eq "B"} selected="selected"{/if}>{$lng.lbl_bundled}</option>
{/if}
</select>
</td>
</tr>

{if $product.internal_url}
<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_product_url}:</td>
  <td class="ProductDetails"><a id="internal_url" href="{$product.internal_url}" target="_blank">{$product.internal_url}</a></td>
</tr>
{/if}

<tr>
  <td colspan="3" valign="middle" align="center">&nbsp;</td>
</tr>

{if $product.productid eq ''}

<tr>
  <td colspan="3" valign="middle" align="center">
    <button type="button" class="button main-button" id="button-next-{if $pro eq '1'}provider{else}admin{/if}" title="Next">Next</button>
  </td>
</tr>

{/if}

</table>

</div>
  


  
<div id="div-two">
  
<table cellpadding="4" cellspacing="0" width="100%" class="product-details-table">

{if $product.productid eq ''}

<tr>
  <td colspan="3" valign="middle" align="center">
    <button type="button" class="button main-button" id="button-back-{if $pro eq '1'}provider{else}admin{/if}" title="Change category">Change category</button>
  </td>
</tr>

{/if}

{*
<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2"><br />{include file="main/subheader.tpl" title=$lng.lbl_details}</td>
</tr>
*}

<tr class="elect">
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_upc_ean_gtin_product_id}</td>
    <td class="ProductDetails">
        <input type="text" name="upc_ean_gtin_product_id" id="upc_ean_gtin_product_id" size="20" maxlength="11" value="{$product.upc_ean_gtin_product_id}" class="InputWidth" autocomplete="off" />
    </td>
</tr>

<tr class="elect"> 
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_mpn_model_number}</td>
    <td class="ProductDetails">
        <input type="text" name="mpn_model_number" id="mpn_model_number" size="20" maxlength="11" value="{$product.mpn_model_number}" class="InputWidth" autocomplete="off" />
    </td>
</tr>

<tr> 
    {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[product]" /></td>{/if}
    <td class="FormButton" nowrap="nowrap"><span
class="products_title">{$lng.lbl_product_title_name}</span> <span
class="mandatory">*</span> :</td>
    <td class="ProductDetails"> 
        <input type="text" name="product" id="product" size="45" class="InputWidth" value="{$product.product|escape}" {if $config.SEO.clean_urls_enabled eq "Y"} onchange="javascript: if (this.form.clean_url.value == '') copy_clean_url(this, this.form.clean_url); fetchvpc(this.value);" {/if} autocomplete="off" />
        {if $top_message.fillerror ne "" and $product.product eq ""}<font class="Star">&lt;&lt;</font>{/if}
        {* <br/><i><strong> Example </strong>: <span id="ex-product"></span></i> *}
    </td>
</tr>

{include file="main/clean_url_field.tpl" clean_url=$product.clean_url clean_urls_history=$product.clean_urls_history clean_url_fill_error=$top_message.clean_url_fill_error tooltip_id='clean_url_tooltip_link'}

<tr class="product-condition"> 
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_condition} <span class="mandatory">*</span> :</td>
  <td class="ProductDetails">
    <select name="condition" id="condition" class="InputWidth" {if $product.productid neq ''}  {/if}>
      <option value="0">--- Select ---</option>
      {foreach from=$product_condition key=k item=c}
      <option value="{$k}" {if $product.condition eq $k} selected="selected" {/if} >{$c}</option>
      {/foreach}
    </select>
  </td>
</tr>

{if $product.condition eq '2'}
    {assign var="sty" value="display: table-row;"}
{/if}

<tr class="condition-new" style="{$sty}"> 
  <td class="FormButton" nowrap="nowrap">If Used</td>
  <td class="ProductDetails">
    <select name="ifused" class="InputWidth">
      <option value="0">--- Select ---</option>
      {foreach from=$product_condition_new key=k item=c}
      <option value="{$k}" {if $product.ifused eq $k} selected="selected" {/if} >{$c}</option>
      {/foreach}
    </select>
  </td>
</tr>

<tr> 
    <td class="FormButton" nowrap="nowrap">Brand / Manufacturer Name</td>
    <td class="ProductDetails">
        <input type="text" name="brand_manufacturer_name" id="brand_manufacturer_name" size="20" maxlength="32" value="{$product.brand_manufacturer_name}" class="InputWidth" autocomplete="off" />
    </td>
</tr>

{if $active_modules.Manufacturers ne "" and not $is_pconf}
<tr class="manufacturer">
    {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[manufacturer]" /></td>{/if}
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_manufacturer}:</td>
    <td class="ProductDetails">
        <select name="manufacturerid" id="manufacturerid">
            <option value=''{if $product.manufacturerid eq ''} selected="selected"{/if}>{$lng.lbl_no_manufacturer}</option>
            {foreach from=$manufacturers item=v}
            <option value='{$v.manufacturerid}'{if $v.manufacturerid eq $product.manufacturerid} selected="selected"{/if}>{$v.manufacturer}</option>
            {/foreach}
        </select>
    </td>
</tr>
{/if}

<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[productcode]" disabled="disabled"/></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_bvira_product_code} :</td>
  <td class="ProductDetails">
    <input type="text" name="productcode" id="productcode" size="20" maxlength="32" value="{$product.productcode|escape}" class="InputWidth" readonly="readonly" />
  </td>
</tr>

<tr class="vpc"> 
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_vendor_product_code} :</td>
    <td class="ProductDetails">
        <input type="text" name="vendorproductcode" id="vendorproductcode" size="20" maxlength="32" value="{$product.vendorproductcode|escape}" class="InputWidth" readonly="readonly" />
    </td>
</tr>


<tr class="sku"> 
    {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[vendorsku]" disabled="disabled"/></td>{/if}
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_vendor_sku} :</td>
    <td class="ProductDetails">
        <input type="text" name="vendorsku" id="vendorsku" size="20" maxlength="32" value="{$product.vendorsku|escape}" class="InputWidth" />
    </td>
</tr>

<tr class="hide">
    {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[keywords]" /></td>{/if}
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_keywords}:</td>
    <td class="ProductDetails">
        <input type="text" id="keywords" name="keywords" class="InputWidth" value="{$product.keywords|escape:"html"}" />
    </td>
</tr>

{include file="main/image_area.tpl"}

{*
{if $active_modules.Egoods ne ""}
{include file="modules/Egoods/egoods.tpl"}
{/if}
*}

<tr style="display: none;"> 
{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[descr]" /></td>{/if}
<td colspan="2" class="FormButton">
<div{if $active_modules.HTML_Editor and not $html_editor_disabled} class="description"{/if}>{$lng.lbl_short_description}* :</div>
<div class="description-data">
{include file="main/textarea.tpl" name="descr" cols=45 rows=8 data=$product.descr width="100%" btn_rows=4}
{if $top_message.fillerror ne "" and ($product.descr eq "" or $product.xss_descr eq "Y")}<font class="Star">&lt;&lt;</font>{/if}
</div>
</td>
</tr>

<tr> 
{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[fulldescr]" /></td>{/if}
    <td colspan="2" class="FormButton">
        <div{if $active_modules.HTML_Editor and not $html_editor_disabled} class="description"{/if}>{*$lng.lbl_det_description*} Description <span class="mandatory">*</span> : </div>
        <div class="description-data">
            {include file="main/textarea.tpl" name="fulldescr" cols=45 rows=12 class="InputWidth" data=$product.fulldescr width="100%" btn_rows=4}
            {if $product.xss_fulldescr eq "Y"}<font class="Star">&lt;&lt;</font>{/if}
            <br/>
            <a rel="#desc_link_tooltip" href="#desc_link_tooltip" id="desc_link" style="float: right;" class="NeedHelpLink">Need help?</a>
            <br/>
            <span style="display:none;" id="desc_link_tooltip">
            testing
            </span>
        </div>
    </td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2">{$lng.txt_html_tags_in_description}</td>
</tr>

<tr>
  {if $geid ne ''}<td class="TableSubHead">&nbsp;</td>{/if}
  <td class="FormButton" nowrap="nowrap" colspan="2"><h2>Prices</h2></td>
</tr>

<tr> 
    <td class="FormButton" nowrap="nowrap">Currency :</td>
    <td class="ProductDetails">
        <input type="hidden" id="currency" name="currency" value="{$product.currency}" size="18" readonly="readonly" />
        <span class="curren">{$product.currency}</span>
    </td>
</tr>

<tr> 
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[list_price]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_list_price} <span class="mandatory">*</span> :</td>
  <td class="ProductDetails">
      <input type="text" id="list_price" name="list_price" size="18" value="{$product.list_price|formatprice|default:$zero}" />
      <input type="hidden" id="vendor_list_price" name="vendor_list_price" size="18" value="{$product.vendor_list_price|formatprice|default:$zero}" />
      <span class="curren">{$product.currency}</span>
  </td>
</tr>

<tr>
  {if $geid ne ''}<td width="15" class="TableSubHead">{if $product.is_variants eq 'Y'}&nbsp;{else}<input type="checkbox" value="Y" name="fields[price]" />{/if}</td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_sale_price} :</td>
  <td class="ProductDetails">
    {if $product.is_variants eq 'Y'}
        <b>{$lng.lbl_note}:</b> {$lng.txt_pvariant_edit_note|substitute:"href":$variant_href}
    {else}
        <input type="text" id="price" name="price" size="18" value="{$product.price|formatprice|default:$zero}" />
        <input type="hidden" id="vendor_price" name="vendor_price" size="18" value="{$product.vendor_price|formatprice|default:$zero}" />
        {if $top_message.fillerror ne "" and $product.price eq ""}<font class="Star">&lt;&lt;</font>{/if}
    {/if}
    <span class="curren">{$product.currency}</span>
    &nbsp;
    <a rel="#price_link_tooltip" href="#price_link_tooltip" id="price_link" class="NeedHelpLink" style="border-style: none;">
        <img src="../skin/common_files/images/dingbats_help.gif" />
    </a>
    <br/>
    <span style="display:none;" id="price_link_tooltip">{$lng.lbl_sale_price_help}</span>
  </td>
</tr>

<tr class="lowest"> 
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_lowest_possible_price} (Bottom line price) :</td>
  <td class="ProductDetails">
      <input type="text" id="lowest_possible_price" name="lowest_possible_price" size="18" value="{$product.lowest_possible_price|formatprice|default:$zero}" />
      <input type="hidden" id="vendor_lowest_possible_price" name="vendor_lowest_possible_price" size="18" value="{$product.vendor_lowest_possible_price|formatprice|default:$zero}" />
      <span class="curren">{$product.currency}</span>
  </td>
</tr>

<tr> 
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_vendor_cost} :</td>
  <td class="ProductDetails">
      <input type="text" id="vendor_cost" name="vendor_cost" size="18" value="{$product.vendor_cost|formatprice|default:$zero}" />
      <input type="hidden" id="vendor_vendor_cost" name="vendor_vendor_cost" size="18" value="{$product.vendor_vendor_cost|formatprice|default:$zero}" />
      <span class="curren">{$product.currency}</span>
    &nbsp;
    <a rel="#vendor_cost_link_tooltip" href="#vendor_cost_link_tooltip" id="vendor_cost_link" class="NeedHelpLink" style="border-style: none;">
        <img src="../skin/common_files/images/dingbats_help.gif" />
    </a>
    <br/>
    <span style="display:none;" id="vendor_cost_link_tooltip">{$lng.lbl_vendor_cost_help}</span>
  </td>
</tr>

<tr> 
  <td class="FormButton" nowrap="nowrap">Wholesale Price :</td>
  <td class="ProductDetails">
    <input type="text" id="whole_sale_price" name="whole_sale_price" size="18" value="{$product.whole_sale_price|formatprice|default:$zero}" />
    <span class="curren">{$product.currency}</span>
  </td>
</tr>

<tr> 
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_map_price} :</td>
  <td class="ProductDetails">
    <input type="text" id="map_price" name="map_price" size="18" value="{$product.map_price|formatprice|default:$zero}" />
    <span class="curren">{$product.currency}</span>
  </td>
</tr>

<tr>
    <td class="FormButton" nowrap="nowrap">Number of Quantity</td>
    <td class="ProductDetails">
        <ul>
            <li>
                <input type="radio" name="name" class="radio1" value="1" {if $product.productid eq "" or $product.quantity_type eq "1"} checked="checked" {/if}> I have <input type="text" id="quantity_avail" value="{if $product.productid eq ""}{$product.avail|default:1000}{else}{$product.avail}{/if}" /> for sale
            </li>
            <li>
                <input type="radio" name="name" class="radio1" value="2" {if $product.quantity_type eq "2"} checked="checked" {/if} > This item is always in stock
            </li>
            <li>
                <input type="radio" name="name" class="radio1" value="3" {if $product.quantity_type eq "3"} checked="checked" {/if} > Quantity not applicable
            </li>
        </ul>
        <input type="hidden" id="quantity_type" name="quantity_type" value="{if $product.productid eq ""}1{else}{$product.quantity_type}{/if}" />
    </td>
</tr>

{* style="display: none;" *}
{if not $is_pconf}
<tr style="display: none;"> 
{if $geid ne ''}<td width="15" class="TableSubHead">{if $product.is_variants eq 'Y'}&nbsp;{else}<input type="checkbox" value="Y" name="fields[avail]" />{/if}</td>{/if}
<td class="FormButton" nowrap="nowrap">{$lng.lbl_quantity_in_stock}:</td>
<td class="ProductDetails">
{if $product.is_variants eq 'Y'}
<b>{$lng.lbl_note}:</b> {$lng.txt_pvariant_edit_note|substitute:"href":$variant_href}
{else}
<input type="text" name="avail" id="avail" size="18" value="{if $product.productid eq ""}{$product.avail|default:1000}{else}{$product.avail}{/if}" />
{if $top_message.fillerror ne "" and $product.avail eq ""}<font class="Star">&lt;&lt;</font>{/if}
{/if}
</td>
</tr>

{* style="display: none;" *}
<tr style="display: none;"> 
{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[low_avail_limit]" /></td>{/if}
<td class="FormButton" nowrap="nowrap">{$lng.lbl_lowlimit_in_stock}:</td>
<td class="ProductDetails"> 
<input type="text" name="low_avail_limit" size="18" value="{if $product.productid eq ""}10{else}{ $product.low_avail_limit }{/if}" />
{if $top_message.fillerror ne "" and $product.low_avail_limit le 0}<font class="Star">&lt;&lt;</font>{/if}
</td>
</tr>
{/if}

<tr>
    <td class="FormButton" nowrap="nowrap" colspan="2"><h2>{$lng.lbl_min_order_amount}</h2></td>
</tr>

<tr>
    {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[min_amount]" /></td>{/if}
    <td class="FormButton" nowrap="nowrap">Buyer must atleast purchase :</td>
    <td class="ProductDetails">
        <input type="text" name="min_amount" size="18" value="{if $product.productid eq ""}1{else}{$product.min_amount}{/if}" />
    </td>
</tr>

{* style="display: none;" *}
{if $active_modules.RMA ne '' and not $is_pconf}
<tr style="display: none;"> 
{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[return_time]" /></td>{/if}
<td class="FormButton" nowrap="nowrap">{$lng.lbl_return_time}:</td>
<td class="ProductDetails"><input type="text" name="return_time" id="return_time" size="18" value="{$product.return_time}" /></td>
</tr>
{/if}


{if not $is_pconf}
<tr class="shipping">
    <td class="FormButton" nowrap="nowrap"><strong>Shipping</strong></td>
    <td class="ProductDetails">
        <input type="hidden" id="shipping_id" name="shipping_id" value="{$product.shipping_id}" size="18" readonly="readonly" />
        <span id="shippin">{$shipping_info}</span>
    </td>
</tr>

<tr class="selling">
    <td class="FormButton" nowrap="nowrap"><strong>Selling Module</strong></td>
    <td class="ProductDetails">
        <input type="hidden" id="saleid" name="saleid" value="{$product.saleid}" size="18" readonly="readonly" />
        <span id="sale_module">{$sale_info}</span>
    </td>
</tr>

<tr class="tax">
    <td class="FormButton" nowrap="nowrap"><strong>Tax Country</strong></td>
    <td class="ProductDetails">
        <input type="hidden" id="taxcountry" name="taxcountry" value="{$product.taxcountry}" size="18" readonly="readonly" />
        <span id="tax-country">{$product.taxcountry}</span>
    </td>
</tr>

<tr class="tax">
    <td class="FormButton" nowrap="nowrap"><strong>Tax State</strong></td>
    <td class="ProductDetails">
        <input type="hidden" id="taxstate" name="taxstate" value="{$product.taxstate}" size="18" readonly="readonly"/>
        <span id="tax-state">{$taxstate}</span>
    </td>
</tr>

<tr class="tax">
    <td class="FormButton" nowrap="nowrap"><strong>Tax %</strong></td>
    <td class="ProductDetails">
        <input type="hidden" id="taxpercent" name="taxpercent" value="{$product.taxpercent}" size="18" readonly="readonly" />
        <span id="tax-percent">{$product.taxpercent}</span>
    </td>
</tr>

<tr class="real-time"> 
    {if $geid ne ''}<td width="15" class="TableSubHead">{if $product.is_variants eq 'Y'}&nbsp;{else}<input type="checkbox" value="Y" name="fields[weight]" />{/if}</td>{/if}
    <td class="FormButton" nowrap="nowrap">{$lng.lbl_weight} (in lbs) <span class="mandatory">*</span> :</td>
    <td class="ProductDetails"> 
        <input type="text" id="weight" name="weight" size="18" value="{$product.weight|formatprice|default:$zero}" />
    </td>
</tr>

<tr class="fixed-price"> 
    {if $geid ne ''}<td width="15" class="TableSubHead">{if $product.is_variants eq 'Y'}&nbsp;{else}<input type="checkbox" value="Y" name="fields[weight]" />{/if}</td>{/if}
    <td class="FormButton" nowrap="nowrap">Fixed Price :</td>
    <td class="ProductDetails"> 
        <input type="text" name="fixedprice" size="18" value="{$product.fixedprice|formatprice|default:$zero}" style="display: none;" />
        <div id="multiple-fixedprice">{$multiple_fixedprice}</div>
    </td>
</tr>

{* style="display: none;" *}
<tr style="display: none;"> 
{if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[free_shipping]" /></td>{/if}
<td class="FormButton" nowrap="nowrap">{$lng.lbl_free_shipping}:</td>
<td class="ProductDetails">
<select name="free_shipping">
<option value='Y'{if $product.free_shipping eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
<option value='N'{if $product.free_shipping eq 'N'} selected="selected"{/if}>{$lng.lbl_no}</option>
</select>
</td>
</tr>

{* style="display: none;" *}
<tr style="display: none;">
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[shipping_freight]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_shipping_freight} ({$config.General.currency_symbol}):</td>
  <td class="ProductDetails">
  <input type="text" name="shipping_freight" size="18" value="{$product.shipping_freight|formatprice|default:$zero}" />
  </td>
</tr>

{* style="display: none;" *}
<tr style="display: none;">
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[small_item]" /></td>{/if}
  <td class="FormButton">{$lng.lbl_small_item}:</td>
  <td class="ProductDetails">
  <input type="checkbox" name="small_item" value="Y"{if $product.small_item ne "Y"} checked="checked"{/if} onclick="javascript: switchPDims(this);" />
  </td>
</tr>

<tr class="real-time">
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[dimensions]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_shipping_box_dimensions} ({$config.General.dimensions_symbol}) <span class="mandatory">*</span> :</td>
  <td class="ProductDetails">
  <table cellpadding="0" cellspacing="1" border="0" width="100%">
  <tr>
    <td colspan="2">{$lng.lbl_length}</td>
    <td colspan="2">{$lng.lbl_width}</td>
    <td colspan="3">{$lng.lbl_height}</td>
  </tr>
  <tr>
    <td><input type="text" id="length" name="length" size="6" value="{$product.length|default:$zero}"{if $product.small_item eq "Y"} disabled="disabled"{/if} /></td>
    <td>&nbsp;x&nbsp;</td>
    <td><input type="text" id="width" name="width" size="6" value="{$product.width|default:$zero}"{if $product.small_item eq "Y"} disabled="disabled"{/if} /></td>
    <td>&nbsp;x&nbsp;</td>
    <td><input type="text" id="height" name="height" size="6" value="{$product.height|default:$zero}"{if $product.small_item eq "Y"} disabled="disabled"{/if} /></td>
    <td align="center" width="100%">{if $new_product eq 1}&nbsp;{else}<a href="javascript:void(0);" onclick="javascript: popupOpen('unavailable_shipping.php?id={$product.productid}', '', {ldelim}width:550,height:500{rdelim});">{$lng.lbl_check_for_unavailable_shipping_methods}</a>{/if}</td>
  </tr>
  </table>
  </td>
</tr>

{* style="display: none;" *}
<tr style="display: none;">
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[separate_box]" /></td>{/if}
  <td class="FormButton">{$lng.lbl_ship_in_separate_box}:</td>
  <td class="ProductDetails">
  <input type="checkbox" name="separate_box" value="Y"{if $product.separate_box eq "Y"} checked="checked"{/if}{if $product.small_item eq "Y"} disabled="disabled"{/if} onclick="javascript: switchSSBox(this);" />
  </td>
</tr>

{* style="display: none;" *}
<tr style="display: none;">
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[items_per_box]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_items_per_box}:</td>
  <td class="ProductDetails">
  <input type="text" name="items_per_box" size="18" value="{$product.items_per_box|default:1}"{if $product.small_item eq "Y" or $product.separate_box ne "Y"} disabled="disabled"{/if} />
  </td>
</tr>

{/if} {* / not $is_pconf / *}

{* style="display: none;" *}
<tr class="hide">
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[title_tag]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_title_tag}:</td>
  <td class="ProductDetails"><textarea id="title_tag" name="title_tag" cols="45" rows="6" class="InputWidth">{$product.title_tag}</textarea></td>
</tr>

{* style="display: none;" *}
<tr class="hide">
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[meta_keywords]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_meta_keywords}:</td>
  <td class="ProductDetails"><textarea id="meta_keywords" name="meta_keywords" cols="45" rows="6" class="InputWidth">{$product.meta_keywords}</textarea></td>
</tr>

{* style="display: none;" *}
<tr class="hide">
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[meta_description]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_meta_description}:</td>
  <td class="ProductDetails"><textarea id="meta_description" name="meta_description" cols="45" rows="6" class="InputWidth">{$product.meta_description}</textarea></td>
</tr>

{* style="display: none;" 
<tr style="display: none;"> 
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[membershipids]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_membership}:</td>
  <td class="ProductDetails">{include file="main/membership_selector.tpl" data=$product}</td>
</tr>
*}

{* style="display: none;" *}
<tr style="display: none;"> 
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[free_tax]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_tax_exempt}:</td>
  <td class="ProductDetails">
  <select name="free_tax"{if $taxes} onchange="javascript: ChangeTaxesBoxStatus(this);"{/if}>
    <option value='Y'{if $product.free_tax eq 'Y'} selected="selected"{/if}>{$lng.lbl_yes}</option>
    <option value='N'{if $product.free_tax eq 'N'} selected="selected"{/if}>{$lng.lbl_no}</option>
  </select> 
  </td>
</tr>

{if $taxes}
{* style="display: none;" *}
<tr style="display: none;"> 
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[taxes]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_apply_taxes}:</td>
  <td class="ProductDetails"> 
  <select name="taxes[]" multiple="multiple"{if $product.free_tax eq "Y"} disabled="disabled"{/if}>
  {section name=tax loop=$taxes}
  <option value="{$taxes[tax].taxid}"{if $taxes[tax].selected gt 0} selected="selected"{/if}>{$taxes[tax].tax_name}</option>
  {/section}
  </select>
  <br />{$lng.lbl_hold_ctrl_key}
  {if $is_admin_user}<br /><a href="{$catalogs.provider}/taxes.php" class="SmallNote" target="_blank">{$lng.lbl_click_here_to_manage_taxes}</a>{/if}
  </td>
</tr>
{/if}

{* style="display: none;" *}
<tr style="display: none;">
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[discount_avail]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_apply_global_discounts}:</td>
  <td class="ProductDetails">
  <input type="checkbox" name="discount_avail" value="Y"{if $product.productid eq "" or $product.discount_avail eq "Y"} checked="checked"{/if} />
  </td>
</tr>

{if $gcheckout_enabled}

{* style="display: none;" *}
<tr style="display: none;">
  {if $geid ne ''}<td width="15" class="TableSubHead"><input type="checkbox" value="Y" name="fields[valid_for_gcheckout]" /></td>{/if}
  <td class="FormButton" nowrap="nowrap">{$lng.lbl_gcheckout_product_valid}:</td>
  <td class="ProductDetails">
  <input type="hidden" name="valid_for_gcheckout" value="N" />
  <input type="checkbox" name="valid_for_gcheckout" value="Y"{if $product.productid eq "" or $product.valid_for_gcheckout eq "Y"} checked="checked"{/if} />
  </td>
</tr>

{/if}

{*
{if $active_modules.Extra_Fields ne ""}
{include file="modules/Extra_Fields/product_modify.tpl"}
{/if}
*}

{if $product.productid neq '' and $is_admin_user}

{* BCSE Begin - Drop Shipping mod *}
{if $active_modules.BCSE_Drop_Shipping}
{include file="modules/BCSE_Drop_Shipping/product_details.tpl"}
{/if}
{* BCSE End *}

{/if}

{*
{if $active_modules.Special_Offers}
{include file="modules/Special_Offers/product_modify.tpl"}
{/if}
*}

<tr> 
    <td colspan="2"> 
        <div id="extra_fields">{$extra_fields}</div>
    </td>
</tr>

<tr>
  {if $geid ne ''}<td class="TableSubHead">&nbsp;</td>{/if}
  <td colspan="2" align="center">
    <br /><br />
    <div id="sticky_content">
    <table width="100%">
      <tr>
        <td width="120" align="left" class="main-button">
          <input type="submit" class="big-main-button" value=" {$lng.lbl_apply_changes|strip_tags:false|escape} " />
        </td>
        <td width="100%" align="right">
          {if $product.productid gt 0}
            {*<input type="button" value="{$lng.lbl_preview|strip_tags:false|escape}" onclick="javascript: submitForm(this.form, 'details');" /> &nbsp;&nbsp;&nbsp;*}
            {*<input type="button" value="{$lng.lbl_clone|strip_tags:false|escape}" onclick="javascript: submitForm(this.form, 'clone');" />&nbsp;&nbsp;&nbsp;*}

            <input type="button" value="{$lng.lbl_preview|strip_tags:false|escape}" id="preview" /> &nbsp;&nbsp;&nbsp;
            <input type="button" value="{$lng.lbl_delete|strip_tags:false|escape}" onclick="javascript: submitForm(this.form, 'delete');" />&nbsp;&nbsp;&nbsp;
            <input type="button" value="{$lng.lbl_generate_html_links|strip_tags:false|escape}" onclick="javascript: submitForm(this.form, 'links');" />
          {/if}
        </td>
      </tr>
    </table>
    </div>
  </td>
</tr>

</table>
        
</div>
        
</form>

{/capture}
{include file="dialog.tpl" content=$smarty.capture.dialog extra='width="100%"'}

{if $new_product ne "1" and $geid eq ''}
  <br />
  {include file="main/clean_urls.tpl" resource_name="productid" resource_id=$productid clean_url_action="product_modify.php" clean_urls_history_mode="clean_urls_history" clean_urls_history=$product.clean_urls_history}
{/if}

{if $pro eq '1'}
    {literal}
    <script>
        $(document).ready(function() {

            var prov = $('#provider').attr('class');

            if(prov == '') {

                alert('Page Load Error');
                return false;
            }

            $.ajax({
                async: true,
                data: "action=fetch_product_type&provider_id="+prov,
                success: function(response) {

                    if(response) {

                        $('#producttype').html(response);
                    } else {

                        alert('Page Load Error');
                        return false;
                    }
                },
                type: "GET",
                url: "../ajax_call.php"
            });

        });
    </script>  
    {/literal}
{/if}

{if $product.productid neq ''}

    {literal}
    <script>
        $(document).ready(function() {

            var categoryid = $.trim($('#categoryid').val());

            if(categoryid != '') {

                $('#div-one').attr('class', 'jquery-lightbox-overlay jquery-lightbox');
                $('#div-two').attr('class', 'jquery-lightbox-overlay jquery-lightbox');

                $.ajax({
                    async: true,
                    data: "action=1012&categoryid="+categoryid,
                    dataType: "json",
                    success: function(response){

                        if(response.success == '1') {

                            //triggercategory(response.content);
                            setTimeout(function(){
                                triggercategory(response.content);
                            },3000);
                        } else {

                            alert('Page Load Error');
                            return false;
                        }
                    },
                    type: "GET",
                    url: "../ajax_call.php"
                });
            } else {

                alert('Page Load Error');
                return false;
            }

            setTimeout(function(){
                fetch_extra_fields();
            },9000);

            setTimeout(function(){
                fetch_extra_fields_values();
            },10000);

        });

    </script>
    {/literal}
{/if}

{literal}
    <script type="text/javascript">
            $(document).ready(function(){

                $('#desc_link, #price_link, #vendor_cost_link').cluetip({
                    local:true, 
                    hideLocal: false,
                    showTitle: false,
                    cluezIndex: 1100,
                    clueTipClass: 'default'
                });

                $('#preview').live('click', function(event) {

                    var internal_url = $('#internal_url').attr('href');
                    location.href = internal_url;
                });

            });
    </script>
{/literal}
