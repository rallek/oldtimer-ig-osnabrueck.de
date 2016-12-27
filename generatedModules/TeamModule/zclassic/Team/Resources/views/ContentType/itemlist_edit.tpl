{* Purpose of this template: edit view of generic item list content type *}
<div class="form-group">
    {gt text='Object type' domain='rkteammodule' assign='objectTypeSelectorLabel'}
    {formlabel for='rKTeamModuleObjectType' text=$objectTypeSelectorLabel cssClass='col-sm-3 control-label'}
    <div class="col-sm-9">
        {rkteammoduleObjectTypeSelector assign='allObjectTypes'}
        {formdropdownlist id='rKTeamModuleOjectType' dataField='objectType' group='data' mandatory=true items=$allObjectTypes cssClass='form-control'}
        <span class="help-block">{gt text='If you change this please save the element once to reload the parameters below.' domain='rkteammodule'}</span>
    </div>
</div>

{if $featureActivationHelper->isEnabled(const('RK\\TeamModule\\Helper\\FeatureActivationHelper::CATEGORIES', $objectType))}
{formvolatile}
{if $properties ne null && is_array($properties)}
    {nocache}
    {foreach key='registryId' item='registryCid' from=$registries}
        {assign var='propName' value=''}
        {foreach key='propertyName' item='propertyId' from=$properties}
            {if $propertyId eq $registryId}
                {assign var='propName' value=$propertyName}
            {/if}
        {/foreach}
        <div class="form-group">
            {assign var='hasMultiSelection' value=$categoryHelper->hasMultipleSelection($objectType, $propertyName)}
            {gt text='Category' domain='rkteammodule' assign='categorySelectorLabel'}
            {assign var='selectionMode' value='single'}
            {if $hasMultiSelection eq true}
                {gt text='Categories' domain='rkteammodule' assign='categorySelectorLabel'}
                {assign var='selectionMode' value='multiple'}
            {/if}
            {formlabel for="rKTeamModuleCatIds`$propertyName`" text=$categorySelectorLabel cssClass='col-sm-3 control-label'}
            <div class="col-sm-9">
                {formdropdownlist id="rKTeamModuleCatIds`$propName`" items=$categories.$propName dataField="catids`$propName`" group='data' selectionMode=$selectionMode cssClass='form-control'}
                <span class="help-block">{gt text='This is an optional filter.' domain='rkteammodule'}</span>
            </div>
        </div>
    {/foreach}
    {/nocache}
{/if}
{/formvolatile}
{/if}

<div class="form-group">
    {gt text='Sorting' domain='rkteammodule' assign='sortingLabel'}
    {formlabel text=$sortingLabel cssClass='col-sm-3 control-label'}
    <div class="col-sm-9">
        {formradiobutton id='rKTeamModuleSortRandom' value='random' dataField='sorting' group='data' mandatory=true}
        {gt text='Random' domain='rkteammodule' assign='sortingRandomLabel'}
        {formlabel for='rKTeamModuleSortRandom' text=$sortingRandomLabel}
        {formradiobutton id='rKTeamModuleSortNewest' value='newest' dataField='sorting' group='data' mandatory=true}
        {gt text='Newest' domain='rkteammodule' assign='sortingNewestLabel'}
        {formlabel for='rKTeamModuleSortNewest' text=$sortingNewestLabel}
        {formradiobutton id='rKTeamModuleSortDefault' value='default' dataField='sorting' group='data' mandatory=true}
        {gt text='Default' domain='rkteammodule' assign='sortingDefaultLabel'}
        {formlabel for='rKTeamModuleSortDefault' text=$sortingDefaultLabel}
    </div>
</div>

<div class="form-group">
    {gt text='Amount' domain='rkteammodule' assign='amountLabel'}
    {formlabel for='rKTeamModuleAmount' text=$amountLabel cssClass='col-sm-3 control-label'}
    <div class="col-sm-9">
        {formintinput id='rKTeamModuleAmount' dataField='amount' group='data' mandatory=true maxLength=2}
    </div>
</div>

<div class="form-group">
    {gt text='Template' domain='rkteammodule' assign='templateLabel'}
    {formlabel for='rKTeamModuleTemplate' text=$templateLabel cssClass='col-sm-3 control-label'}
    <div class="col-sm-9">
        {rkteammoduleTemplateSelector assign='allTemplates'}
        {formdropdownlist id='rKTeamModuleTemplate' dataField='template' group='data' mandatory=true items=$allTemplates cssClass='form-control'}
    </div>
</div>

<div id="customTemplateArea" class="form-group" data-switch="rKTeamModuleTemplate" data-switch-value="custom">
    {gt text='Custom template' domain='rkteammodule' assign='customTemplateLabel'}
    {formlabel for='rKTeamModuleCustomTemplate' text=$customTemplateLabel cssClass='col-sm-3 control-label'}
    <div class="col-sm-9">
        {formtextinput id='rKTeamModuleCustomTemplate' dataField='customTemplate' group='data' mandatory=false maxLength=80 cssClass='form-control'}
        <span class="help-block">{gt text='Example' domain='rkteammodule'}: <em>itemlist_[objectType]_display.tpl</em></span>
    </div>
</div>

<div class="form-group">
    {gt text='Filter (expert option)' domain='rkteammodule' assign='filterLabel'}
    {formlabel for='rKTeamModuleFilter' text=$filterLabel cssClass='col-sm-3 control-label'}
    <div class="col-sm-9">
        {formtextinput id='rKTeamModuleFilter' dataField='filter' group='data' mandatory=false maxLength=255 cssClass='form-control'}
        <span class="help-block">
            <a class="fa fa-filter" data-toggle="modal" data-target="#filterSyntaxModal">{gt text='Show syntax examples' domain='rkteammodule'}</a>
        </span>
    </div>
</div>

{include file='include_filterSyntaxDialog.tpl'}

{pageaddvar name='stylesheet' value='web/bootstrap/css/bootstrap.min.css'}
{pageaddvar name='stylesheet' value='web/bootstrap/css/bootstrap-theme.min.css'}
{pageaddvar name='javascript' value='web/bootstrap/js/bootstrap.min.js'}
