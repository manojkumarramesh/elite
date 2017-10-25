{*--------------------------------------------------------
COMMON CSS & JS
--------------------------------------------------------*}
<link rel="stylesheet" type="text/css" href="{$AltSkinDir}/custom/css/common.css" />
{if $config.Ability_Template.abi_menu_dialog_style eq "square"}
<link rel="stylesheet" type="text/css" href="{$AltSkinDir}/custom/css/menus_dialogs_square.css" />
{/if}
<script type="text/javascript" src="{$AltSkinDir}/custom/js/common.js"></script>
<script type="text/javascript" src="{$AltSkinDir}/custom/js/custom.js?version=15122012"></script>

{*--------------------------------------------------------
CUSTOM LAYOUT STYLES
--------------------------------------------------------*}
{include file="custom/custom_styles.tpl"}

{*-----CUSTOM BVIRA LAYOUT STYLES-----*}
{if ($main eq "catalog" && $current_category.category eq "") }
    {include file="custom/custom_bvira_styles.tpl"}
{/if}
{if $redirect eq 'provider'}
    {include file="provider/custom_bvira_styles.tpl"}
{/if}

{*--------------------------------------------------------
GRID SYSTEM
--------------------------------------------------------*}
<link rel="stylesheet" type="text/css" href="{$AltSkinDir}/custom/css/grid/grid.css" />
<!--[if IE 6]><link rel="stylesheet" type="text/css" href="{$AltSkinDir}/custom/css/grid/grid_ie6.css" /><![endif]-->
<!--[if IE 7]><link rel="stylesheet" type="text/css" href="{$AltSkinDir}/custom/css/grid/grid_ie.css" /><![endif]-->

{*--------------------------------------------------------
CATEGORY MENUS
--------------------------------------------------------*}
{include file="custom/addons/menus/provider_menus_common.tpl"}

{*--------------------------------------------------------
HOMEPAGE PROMOTIONS
--------------------------------------------------------*}
{*if $main eq "catalog" && $current_category.category eq ""*}
{if $main eq "catalog"}
{include file="custom/welcome/promotions/promotions_common.tpl"}
{/if}

{*--------------------------------------------------------
ADDONS
--------------------------------------------------------*}
{include file="custom/addons/provider_addons_common.tpl"}

{*--------------------------------------------------------
GRID/ROW SWITCHERS
--------------------------------------------------------*}
{include file="custom/provider_switchers.tpl"}

{*--------------------------------------------------------
THEMES
--------------------------------------------------------*}
{include file="custom/provider_themes.tpl"}

{*--------------------------------------------------------
BROWSER SPECIFIC IE FIXES
--------------------------------------------------------*}
<!--[if IE 6]>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/chrome-frame/1/CFInstall.min.js"></script>
{literal}
<script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
{/literal}
<script src="{$AltSkinDir}/custom/js/DD_belatedPNG_0.0.8a.js"></script>
{literal}
<script>
DD_belatedPNG.fix('body,#abi_header_container,#abi_logo_small h1,#abi_logo_medium h1,#abi_logo_large h1,#abi_logo_xlarge h1,#abi_main_container,#abi_menu_container,h3.menu_title,h2.dialog_title,#abi_footer_container,#abi_copyright_container,button.button,button.button:hover');
</script>
{/literal}
<link rel="stylesheet" href="{$AltSkinDir}/custom/css/ie_6.css" />
<![endif]-->
<!--[if IE 7]>
<link rel="stylesheet" href="{$AltSkinDir}/custom/css/ie_7.css" />
<![endif]-->
<!--[if IE 8]>
<link rel="stylesheet" href="{$AltSkinDir}/custom/css/ie_8.css" />
<![endif]-->
<!--[if IE]>
<link rel="stylesheet" href="{$AltSkinDir}/custom/css/ie.css" />
<![endif]-->

{*--------------------------------------------------------
FAVICON
--------------------------------------------------------*}
<link rel="shortcut icon" type="image/ico" href="{$AltSkinDir}/custom/images/favicon.ico" />

{*--------------------------------------------------------
REDIRECT OLD IE BROWSERS
--------------------------------------------------------*}
{if $config.Ability_Template.abi_ie_redirect eq "Y"}
<!--[if lte IE 7]>
{literal}
<script type="text/javascript"> window.location="upgrade.html"; </script>
{/literal}
<![endif]-->
{/if}

