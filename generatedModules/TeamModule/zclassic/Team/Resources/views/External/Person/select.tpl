{* Purpose of this template: Display a popup selector for Forms and Content integration *}
{assign var='baseID' value='person'}
<div id="{$baseID}Preview" style="float: right; width: 300px; border: 1px dotted #a3a3a3; padding: .2em .5em; margin-right: 1em">
    <p><strong>{gt text='Person information'}</strong></p>
    {img id='ajax_indicator' modname='core' set='ajax' src='indicator_circle.gif' alt='' class='hidden'}
    <div id="{$baseID}PreviewContainer">&nbsp;</div>
</div>
<br />
<br />
{assign var='leftSide' value=' style="float: left; width: 10em"'}
{assign var='rightSide' value=' style="float: left"'}
{assign var='break' value=' style="clear: left"'}
<p>
    <label for="{$baseID}Id"{$leftSide}>{gt text='Person'}:</label>
    <select id="{$baseID}Id" name="id"{$rightSide}>
        {foreach item='person' from=$items}
            <option value="{$person.id}"{if $selectedId eq $person.id} selected="selected"{/if}>{$person->getTitleFromDisplayPattern()}</option>
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
        <option value="lastName"{if $sort eq 'lastName'} selected="selected"{/if}>{gt text='Last name'}</option>
        <option value="firstName"{if $sort eq 'firstName'} selected="selected"{/if}>{gt text='First name'}</option>
        <option value="teamMemberImage"{if $sort eq 'teamMemberImage'} selected="selected"{/if}>{gt text='Team member image'}</option>
        <option value="teamMemberFunction"{if $sort eq 'teamMemberFunction'} selected="selected"{/if}>{gt text='Team member function'}</option>
        <option value="teamMemberDescription"{if $sort eq 'teamMemberDescription'} selected="selected"{/if}>{gt text='Team member description'}</option>
        <option value="phoneNumber"{if $sort eq 'phoneNumber'} selected="selected"{/if}>{gt text='Phone number'}</option>
        <option value="mobileNumber"{if $sort eq 'mobileNumber'} selected="selected"{/if}>{gt text='Mobile number'}</option>
        <option value="personEmailAddress"{if $sort eq 'personEmailAddress'} selected="selected"{/if}>{gt text='Person email address'}</option>
        <option value="personAddress"{if $sort eq 'personAddress'} selected="selected"{/if}>{gt text='Person address'}</option>
        <option value="registeredUser"{if $sort eq 'registeredUser'} selected="selected"{/if}>{gt text='Registered user'}</option>
        <option value="filter"{if $sort eq 'filter'} selected="selected"{/if}>{gt text='Filter'}</option>
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
    <input type="button" id="rKTeamModuleSearchGo" name="gosearch" value="{gt text='Filter'}" class="btn btn-default" />
    <br{$break} />
</p>
<br />
<br />

<script type="text/javascript">
/* <![CDATA[ */
    ( function($) {
        $(document).ready(function() {
            rKTeamModule.itemSelector.onLoad('{{$baseID}}', {{$selectedId|default:0}});
        });
    })(jQuery);
/* ]]> */
</script>
