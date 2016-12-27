{* Purpose of this template: Display a popup selector for Forms and Content integration *}
{assign var='baseID' value='linker'}
<div id="{$baseID}Preview" style="float: right; width: 300px; border: 1px dotted #a3a3a3; padding: .2em .5em; margin-right: 1em">
    <p><strong>{gt text='Linker information'}</strong></p>
    {img id='ajax_indicator' modname='core' set='ajax' src='indicator_circle.gif' alt='' class='hidden'}
    <div id="{$baseID}PreviewContainer">&nbsp;</div>
</div>
<br />
<br />
{assign var='leftSide' value=' style="float: left; width: 10em"'}
{assign var='rightSide' value=' style="float: left"'}
{assign var='break' value=' style="clear: left"'}
<p>
    <label for="{$baseID}Id"{$leftSide}>{gt text='Linker'}:</label>
    <select id="{$baseID}Id" name="id"{$rightSide}>
        {foreach item='linker' from=$items}
            <option value="{$linker.id}"{if $selectedId eq $linker.id} selected="selected"{/if}>{$linker->getTitleFromDisplayPattern()}</option>
        {foreachelse}
            <option value="0">{gt text='No entries found.'}</option>
        {/foreach}
    </select>
    <br{$break} />
</p>
<p>
    <label for="{$baseID}Sort"{$leftSide}>{gt text='Sort by'}:</label>
    <select id="{$baseID}Sort" name="sort"{$rightSide}>
        <option value="id"{if $sort eq 'id'} selected="selected"{/if}>{gt text='Id'}</option>
        <option value="workflowState"{if $sort eq 'workflowState'} selected="selected"{/if}>{gt text='Workflow state'}</option>
        <option value="linkerImage"{if $sort eq 'linkerImage'} selected="selected"{/if}>{gt text='Linker image'}</option>
        <option value="linkerHeadline"{if $sort eq 'linkerHeadline'} selected="selected"{/if}>{gt text='Linker headline'}</option>
        <option value="linkerText"{if $sort eq 'linkerText'} selected="selected"{/if}>{gt text='Linker text'}</option>
        <option value="theLink"{if $sort eq 'theLink'} selected="selected"{/if}>{gt text='The link'}</option>
        <option value="boostrapSetting"{if $sort eq 'boostrapSetting'} selected="selected"{/if}>{gt text='Boostrap setting'}</option>
        <option value="linkerLanguage"{if $sort eq 'linkerLanguage'} selected="selected"{/if}>{gt text='Linker language'}</option>
        <option value="sorting"{if $sort eq 'sorting'} selected="selected"{/if}>{gt text='Sorting'}</option>
        <option value="linkerGroup"{if $sort eq 'linkerGroup'} selected="selected"{/if}>{gt text='Linker group'}</option>
        <option value="createdDate"{if $sort eq 'createdDate'} selected="selected"{/if}>{gt text='Creation date'}</option>
        <option value="createdUserId"{if $sort eq 'createdUserId'} selected="selected"{/if}>{gt text='Creator'}</option>
        <option value="updatedDate"{if $sort eq 'updatedDate'} selected="selected"{/if}>{gt text='Update date'}</option>
    </select>
    <select id="{$baseID}SortDir" name="sortdir" class="form-control">
        <option value="asc"{if $sortdir eq 'asc'} selected="selected"{/if}>{gt text='ascending'}</option>
        <option value="desc"{if $sortdir eq 'desc'} selected="selected"{/if}>{gt text='descending'}</option>
    </select>
    <br{$break} />
</p>
<p>
    <label for="{$baseID}SearchTerm"{$leftSide}>{gt text='Search for'}:</label>
    <input type="text" id="{$baseID}SearchTerm" name="q" class="form-control"{$rightSide} />
    <input type="button" id="rKWebsiteHelperModuleSearchGo" name="gosearch" value="{gt text='Filter'}" class="btn btn-default" />
    <br{$break} />
</p>
<br />
<br />

<script type="text/javascript">
/* <![CDATA[ */
    ( function($) {
        $(document).ready(function() {
            rKWebsiteHelperModule.itemSelector.onLoad('{{$baseID}}', {{$selectedId|default:0}});
        });
    })(jQuery);
/* ]]> */
</script>
