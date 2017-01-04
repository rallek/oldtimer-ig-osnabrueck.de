{* hier will ich noch ein wenig Zwischenraum hinhaben *}
<div class="section">
			
		<div class="container">
			<div class="row bottom-space">
			<div class="col-xs-12">
			<div class="col-xs-12 white-space">
				<div class="col-md-6 col-sm-6 col-xs-4 no-padding">
					{if $modvars.News.picupload_enabled AND $pictures gt 0}
						{if $modvars.ZConfig.shorturls}
							<a href="{modurl modname='News' type='user' func='display' sid=$sid from=$from urltitle=$urltitle}"><img class="top-space" src="{$modvars.News.picupload_uploaddir}/pic_sid{$sid}-0-norm.jpg" width="100%" alt="{gt text='Picture %1$s for %2$s' tag1='0' tag2=$title}" /></a>
						{else}
							<a href="{modurl modname='News' type='user' func='display' sid=$sid}"><img src="{$modvars.News.picupload_uploaddir}/pic_sid{$sid}-0-norm.jpg" width="100%" alt="{gt text='Picture %1$s for %2$s' tag1='0' tag2=$title}" /></a>
						{/if}
					{/if}
				</div>

				<div class="col-md-6 col-sm-6 col-xs-8 ">
					{if $readperm}<h2 class="hidden-xs hidden-sm"><a href="{modurl modname='News' type='user' func='display' sid=$sid}">{/if}
					{$title|safehtml}{if $titlewrapped}{$titlewraptxt|safehtml}{/if}
					{if $readperm}</a></h2>{/if}
					{if $readperm}<h4 class="hidden-md hidden-lg"><a href="{modurl modname='News' type='user' func='display' sid=$sid}">{/if}
					{$title|safehtml}{if $titlewrapped}{$titlewraptxt|safehtml}{/if}
					{if $readperm}</a></h4>{/if}

					{if $dispinfo}({if $dispuname}{gt text='by %s' tag1=$uname|profilelinkbyuname} 
					{if $dispdate} {gt text='on %s' tag1=$from|dateformat:$dateformat} {elseif $dispreads OR $dispcomments}{$dispsplitchar} {/if}{/if}
					{if $dispreads}{if $counter gt 0}{gt text='%s pageview' plural='%s pageviews' count=$counter tag1=$counter}{/if}{if $dispcomments}{$dispsplitchar} {/if}{/if}){/if}

					{if $disphometext}
					<div class="storiesext_hometext hidden-xs hidden-sm">
						{if isset($hometextwrapped) && $hometextwrapped}
							{$hometext|notifyfilters:'news.filter_hooks.articles.filter'|truncatehtml:$maxhometextlength:''|safehtml|paragraph}
							{if $readperm}<a type="button" class="btn btn-primary" href="{modurl modname='News' type='user' func='display' sid=$sid}">{/if}
							{$hometextwraptxt|safehtml}
							{if $readperm}</a>{/if}
						{else}
							{$hometext|notifyfilters:'news.filter_hooks.articles.filter'|safehtml|paragraph}
						{/if}
					
						
					</div>
					<div class="storiesext_hometext hidden-xs hidden-md hidden-lg">
						{if isset($hometextwrapped) && $hometextwrapped}
							{$hometext|notifyfilters:'news.filter_hooks.articles.filter'|truncatehtml:'200':''|safehtml|paragraph}
							{if $readperm}<a type="button" class="btn btn-primary" href="{modurl modname='News' type='user' func='display' sid=$sid}">{/if}
							{$hometextwraptxt|safehtml}
							{if $readperm}</a>{/if}
						{else}
							{$hometext|notifyfilters:'news.filter_hooks.articles.filter'|safehtml|paragraph}
						{/if}
					
						
					</div>
					<div class="storiesext_hometext hidden-sm hidden-md hidden-lg">
						{if isset($hometextwrapped) && $hometextwrapped}
							{$hometext|notifyfilters:'news.filter_hooks.articles.filter'|truncatehtml:'80':''|safehtml|paragraph}
							{if $readperm}<a type="button" class="btn btn-link" href="{modurl modname='News' type='user' func='display' sid=$sid}">{/if}
							{$hometextwraptxt|safehtml}
							{if $readperm}</a>{/if}
						{else}
							{$hometext|notifyfilters:'news.filter_hooks.articles.filter'|safehtml|paragraph}
						{/if}
					
						
					</div>
					{/if}
				</div>
			</div>
			</div>
			</div>
		</div>
	
</div>	

