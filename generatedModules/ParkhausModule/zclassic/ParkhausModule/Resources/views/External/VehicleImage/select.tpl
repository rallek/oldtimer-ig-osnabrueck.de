{* Purpose of this template: Display a popup selector for Forms and Content integration *}
{assign var='baseID' value='vehicleImage'}
<div id="{$baseID}Preview" style="float: right; width: 300px; border: 1px dotted #a3a3a3; padding: .2em .5em; margin-right: 1em">
    <p><strong>{gt text='Vehicle image information'}</strong></p>
    {img id='ajax_indicator' modname='core' set='ajax' src='indicator_circle.gif' alt='' class='hidden'}
    <div id="{$baseID}PreviewContainer">&nbsp;</div>
</div>
<br />
<br />
{assign var='leftSide' value=' style="float: left; width: 10em"'}
{assign var='rightSide' value=' style="float: left"'}
{assign var='break' value=' style="clear: left"'}
<p>
    <label for="{$baseID}Id"{$leftSide}>{gt text='Vehicle image'}:</label>
    <select id="{$baseID}Id" name="id"{$rightSide}>
        {foreach item='vehicleImage' from=$items}
            <option value="{$vehicleImage.id}"{if $selectedId eq $vehicleImage.id} selected="selected"{/if}>{$vehicleImage->getTitleFromDisplayPattern()}</option>
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
        <option value="titel"{if $sort eq 'titel'} selected="selected"{/if}>{gt text='Titel'}</option>
        <option value="vehicleImage"{if $sort eq 'vehicleImage'} selected="selected"{/if}>{gt text='Vehicle image'}</option>
        <option value="copyright"{if $sort eq 'copyright'} selected="selected"{/if}>{gt text='Copyright'}</option>
        <option value="imageDate"{if $sort eq 'imageDate'} selected="selected"{/if}>{gt text='Image date'}</option>
        <option value="description"{if $sort eq 'description'} selected="selected"{/if}>{gt text='Description'}</option>
        <option value="viewImage"{if $sort eq 'viewImage'} selected="selected"{/if}>{gt text='View image'}</option>
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
    <input type="button" id="rKParkHausModuleSearchGo" name="gosearch" value="{gt text='Filter'}" class="btn btn-default" />
    <br{$break} />
</p>
<br />
<br />

<script type="text/javascript">
/* <![CDATA[ */
    ( function($) {
        $(document).ready(function() {
            rKParkHausModule.itemSelector.onLoad('{{$baseID}}', {{$selectedId|default:0}});
        });
    })(jQuery);
/* ]]> */
</script>
