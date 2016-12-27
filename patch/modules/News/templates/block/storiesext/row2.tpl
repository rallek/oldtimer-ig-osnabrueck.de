{* hier will ich noch ein wenig Zwischenraum hinhaben *}
<div class="section">
	<div class="storiesext">		
		<div class="container">
			<div class="row">
				<div class="col-md-6">
					{if $modvars.News.picupload_enabled AND $pictures gt 0}
						{if $modvars.ZConfig.shorturls}
							<a href="{modurl modname='News' type='user' func='display' sid=$sid from=$from urltitle=$urltitle}"><img src="{$modvars.News.picupload_uploaddir}/pic_sid{$sid}-0-norm.jpg" width="100%" alt="{gt text='Picture %1$s for %2$s' tag1='0' tag2=$title}" /></a>
						{else}
							<a href="{modurl modname='News' type='user' func='display' sid=$sid}"><img src="{$modvars.News.picupload_uploaddir}/pic_sid{$sid}-0-norm.jpg" width="100%" alt="{gt text='Picture %1$s for %2$s' tag1='0' tag2=$title}" /></a>
						{/if}
					{/if}
				</div>

				<div class="col-md-6">
					{if $readperm}<h2><a href="{modurl modname='News' type='user' func='display' sid=$sid}">{/if}
					{$title|safehtml}{if $titlewrapped}{$titlewraptxt|safehtml}{/if}
					{if $readperm}</a></h2>{/if}

					{if $dispinfo}({if $dispuname}{gt text='by %s' tag1=$uname|profilelinkbyuname} 
					{if $dispdate} {gt text='on %s' tag1=$from|dateformat:$dateformat} {elseif $dispreads OR $dispcomments}{$dispsplitchar} {/if}{/if}
					{if $dispreads}{if $counter gt 0}{gt text='%s pageview' plural='%s pageviews' count=$counter tag1=$counter}{/if}{if $dispcomments}{$dispsplitchar} {/if}{/if}{/if}

					{if $disphometext}
					<div class="storiesext_hometext">
						{if $hometextwrapped}
							{$hometext|notifyfilters:'news.filter_hooks.articles.filter'|truncatehtml:$maxhometextlength:''|safehtml|paragraph}
							{if $readperm}<a href="{modurl modname='News' type='user' func='display' sid=$sid}">{/if}
							{$hometextwraptxt|safehtml}
							{if $readperm}</a>{/if}
						{else}
							{$hometext|notifyfilters:'news.filter_hooks.articles.filter'|safehtml|paragraph}
						{/if}
					
						{/if}
					</div>
					
				</div>
			</div>
		</div>
	</div>
</div>	

