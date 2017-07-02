
			
		<div class="container">
			<div class="row">
			<div class="col-xs-12">
			<div class="col-xs-12 white-space">
				{if $readperm}<a href="{modurl modname='News' type='user' func='display' sid=$sid}"><h3>{/if}
				{if $itemnewimage}{img modname='core' set=$newimageset src=$newimagesrc __alt='New'}{/if}
				{$title|safehtml}{if $titlewrapped}{$titlewraptxt|safehtml}{/if}
				{if $readperm}</h3></a>{/if}

				{if $dispinfo}{if $dispuname}{gt text='by %s' tag1=$uname|profilelinkbyuname}
				{if $dispdate} {gt text='on %s' tag1=$from|dateformat:$dateformat} {elseif $dispreads OR $dispcomments}{$dispsplitchar} {/if}{/if}
				{if $dispreads}{if $counter gt 0}{gt text='%s pageview' plural='%s pageviews' count=$counter tag1=$counter}{/if}{if $dispcomments}{$dispsplitchar} {/if}{/if}
				{if $dispcomments and $comments gt 0}{gt text='%s comment' plural='%s comments' count=$comments tag1=$comments}{/if}
				{/if}

				{if $disphometext}
				<div class="storiesext_hometext">
				{if $hometextwrapped}
					{$hometext|notifyfilters:'news.filter_hooks.articles.filter'|truncatehtml:$maxhometextlength:''|safehtml|paragraph}
					{if $readperm}<a href="{modurl modname='News' type='user' func='display' sid=$sid}">{/if}
					<button type="button" class="btn btn-primary pull-right">{$hometextwraptxt|safehtml}</button>
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
	