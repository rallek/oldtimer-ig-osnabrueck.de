{nocache}{include file='user/menu.tpl'}{/nocache}
{insert name='getstatusmsg'}

{section name='newsview' loop=$newsitems}
<div class="bottom-space">
<div class="white-space">
    {$newsitems[newsview]}
    {if $smarty.section.newsview.last neq true}
   
    {/if}
</div>
</div>
{/section}

{if $newsitems}
{pager modname='News' func='view' display='page' rowcount=$pager.numitems limit=$pager.itemsperpage posvar='page' maxpages='10'}
{/if}