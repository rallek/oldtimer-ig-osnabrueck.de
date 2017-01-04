{* Purpose of this template: Display a popup selector for Forms and Content integration *}
{assign var='baseID' value='vehicle'}
<div id="{$baseID}Preview" style="float: right; width: 300px; border: 1px dotted #a3a3a3; padding: .2em .5em; margin-right: 1em">
    <p><strong>{gt text='Vehicle information'}</strong></p>
    {img id='ajax_indicator' modname='core' set='ajax' src='indicator_circle.gif' alt='' class='hidden'}
    <div id="{$baseID}PreviewContainer">&nbsp;</div>
</div>
<br />
<br />
{assign var='leftSide' value=' style="float: left; width: 10em"'}
{assign var='rightSide' value=' style="float: left"'}
{assign var='break' value=' style="clear: left"'}
<p>
    <label for="{$baseID}Id"{$leftSide}>{gt text='Vehicle'}:</label>
    <select id="{$baseID}Id" name="id"{$rightSide}>
        {foreach item='vehicle' from=$items}
            <option value="{$vehicle.id}"{if $selectedId eq $vehicle.id} selected="selected"{/if}>{$vehicle->getTitleFromDisplayPattern()}</option>
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
        <option value="vehicleType"{if $sort eq 'vehicleType'} selected="selected"{/if}>{gt text='Vehicle type'}</option>
        <option value="titleImage"{if $sort eq 'titleImage'} selected="selected"{/if}>{gt text='Title image'}</option>
        <option value="copyrightTitleImage"{if $sort eq 'copyrightTitleImage'} selected="selected"{/if}>{gt text='Copyright title image'}</option>
        <option value="vehicleImage"{if $sort eq 'vehicleImage'} selected="selected"{/if}>{gt text='Vehicle image'}</option>
        <option value="copyrightVehicleImage"{if $sort eq 'copyrightVehicleImage'} selected="selected"{/if}>{gt text='Copyright vehicle image'}</option>
        <option value="vehicleDescriptionTeaser"{if $sort eq 'vehicleDescriptionTeaser'} selected="selected"{/if}>{gt text='Vehicle description teaser'}</option>
        <option value="vehicleDescription"{if $sort eq 'vehicleDescription'} selected="selected"{/if}>{gt text='Vehicle description'}</option>
        <option value="manufacturer"{if $sort eq 'manufacturer'} selected="selected"{/if}>{gt text='Manufacturer'}</option>
        <option value="manufacturerImage"{if $sort eq 'manufacturerImage'} selected="selected"{/if}>{gt text='Manufacturer image'}</option>
        <option value="model"{if $sort eq 'model'} selected="selected"{/if}>{gt text='Model'}</option>
        <option value="built"{if $sort eq 'built'} selected="selected"{/if}>{gt text='Built'}</option>
        <option value="engine"{if $sort eq 'engine'} selected="selected"{/if}>{gt text='Engine'}</option>
        <option value="displacement"{if $sort eq 'displacement'} selected="selected"{/if}>{gt text='Displacement'}</option>
        <option value="cylinders"{if $sort eq 'cylinders'} selected="selected"{/if}>{gt text='Cylinders'}</option>
        <option value="compression"{if $sort eq 'compression'} selected="selected"{/if}>{gt text='Compression'}</option>
        <option value="fuelManagement"{if $sort eq 'fuelManagement'} selected="selected"{/if}>{gt text='Fuel management'}</option>
        <option value="fuel"{if $sort eq 'fuel'} selected="selected"{/if}>{gt text='Fuel'}</option>
        <option value="horsePower"{if $sort eq 'horsePower'} selected="selected"{/if}>{gt text='Horse power'}</option>
        <option value="maxSpeed"{if $sort eq 'maxSpeed'} selected="selected"{/if}>{gt text='Max speed'}</option>
        <option value="weight"{if $sort eq 'weight'} selected="selected"{/if}>{gt text='Weight'}</option>
        <option value="brakes"{if $sort eq 'brakes'} selected="selected"{/if}>{gt text='Brakes'}</option>
        <option value="gearbox"{if $sort eq 'gearbox'} selected="selected"{/if}>{gt text='Gearbox'}</option>
        <option value="rim"{if $sort eq 'rim'} selected="selected"{/if}>{gt text='Rim'}</option>
        <option value="tire"{if $sort eq 'tire'} selected="selected"{/if}>{gt text='Tire'}</option>
        <option value="interior"{if $sort eq 'interior'} selected="selected"{/if}>{gt text='Interior'}</option>
        <option value="infoField1"{if $sort eq 'infoField1'} selected="selected"{/if}>{gt text='Info field 1'}</option>
        <option value="infoField2"{if $sort eq 'infoField2'} selected="selected"{/if}>{gt text='Info field 2'}</option>
        <option value="infoField3"{if $sort eq 'infoField3'} selected="selected"{/if}>{gt text='Info field 3'}</option>
        <option value="owner"{if $sort eq 'owner'} selected="selected"{/if}>{gt text='Owner'}</option>
        <option value="showVehicleOwner"{if $sort eq 'showVehicleOwner'} selected="selected"{/if}>{gt text='Show vehicle owner'}</option>
        <option value="titleTextColor"{if $sort eq 'titleTextColor'} selected="selected"{/if}>{gt text='Title text color'}</option>
        <option value="stillMyOwn"{if $sort eq 'stillMyOwn'} selected="selected"{/if}>{gt text='Still my own'}</option>
        <option value="createdDate"{if $sort eq 'createdDate'} selected="selected"{/if}>{gt text='Creation date'}</option>
        <option value="createdBy"{if $sort eq 'createdBy'} selected="selected"{/if}>{gt text='Creator'}</option>
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
