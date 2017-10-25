{literal}
<style type="text/css">
ul.grid_view li { width: {/literal}{$config.Ability_Template.abi_products_per_row}{literal}%; }
</style>
<script type="text/javascript">
$(document).ready(function(){if($.cookie('view_Layout')==null){$.cookie('view_Layout','{/literal}{$config.Ability_Template.abi_products_layout}{literal}',{expires:30});var view_Layout=$.cookie('view_Layout');if(view_Layout=='row'){$('ul.grid_view').addClass('row_view')}else{$('ul.row_view').addClass('grid_view')}};$('a.switch_layout').click(function(){$('a.switch_layout').toggleClass('switch_layout_button');$('ul.row_view').fadeOut('fast',function(){$(this).fadeIn('fast').toggleClass('grid_view')});$.cookie('view_Layout',$('ul.row_view').is('.grid_view')?'row':'grid',{expires:30})});var view_Layout=$.cookie('view_Layout');if(view_Layout=='grid'){$('ul.row_view').addClass('grid_view');$('a.switch_layout').addClass('switch_layout_button')}else{$('a.switch_layout').removeClass('switch_layout_button')}});
</script>
{/literal}
