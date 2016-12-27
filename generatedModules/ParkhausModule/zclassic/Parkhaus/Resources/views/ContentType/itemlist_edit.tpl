{* Purpose of this template: edit view of generic item list content type *}
<div class="form-group">
    {gt text='Object type' domain='rkparkhausmodule' assign='objectTypeSelectorLabel'}
    {formlabel for='rKParkhausModuleObjectType' text=$objectTypeSelectorLabel cssClass='col-sm-3 control-label'}
    <div class="col-sm-9">
        {rkparkhausmoduleObjectTypeSelector assign='allObjectTypes'}
        {formdropdownlist id='rKParkhausModuleOjectType' dataField='objectType' group='data' mandatory=true items=$allObjectTypes cssClass='form-control'}
        <span class="help-block">{gt text='If you change this please save the element once to reload the parameters below.' domain='rkparkhausmodule'}</span>
    </div>
</div>

{if $featureActivationHelper->isEnabled(const('RK\\ParkhausModule\\Helper\\FeatureActivationHelper::CATEGORIES', $objectType))}
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
            {gt text='Category' domain='rkparkhausmodule' assign='categorySelectorLabel'}
            {assign var='selectionMode' value='single'}
            {if $hasMultiSelection eq true}
                {gt text='Categories' domain='rkparkhausmodule' assign='categorySelectorLabel'}
                {assign var='selectionMode' value='multiple'}
            {/if}
            {formlabel for="rKParkhausModuleCatIds`$propertyName`" text=$categorySelectorLabel cssClass='col-sm-3 control-label'}
            <div class="col-sm-9">
                {formdropdownlist id="rKParkhausModuleCatIds`$propName`" items=$categories.$propName dataField="catids`$propName`" group='data' selectionMode=$selectionMode cssClass='form-control'}
                <span class="help-block">{gt text='This is an optional filter.' domain='rkparkhausmodule'}</span>
            </div>
        </div>
    {/foreach}
    {/nocache}
{/if}
{/formvolatile}
{/if}

<div class="form-group">
    {gt text='Sorting' domain='rkparkhausmodule' assign='sortingLabel'}
    {formlabel text=$sortingLabel cssClass='col-sm-3 control-label'}
    <div class="col-sm-9">
        {formradiobutton id='rKParkhausModuleSortRandom' value='random' dataField='sorting' group='data' mandatory=true}
        {gt text='Random' domain='rkparkhausmodule' assign='sortingRandomLabel'}
        {formlabel for='rKParkhausModuleSortRandom' text=$sortingRandomLabel}
        {formradiobutton id='rKParkhausModuleSortNewest' value='newest' dataField='sorting' group='data' mandatory=true}
        {gt text='Newest' domain='rkparkhausmodule' assign='sortingNewestLabel'}
        {formlabel for='rKParkhausModuleSortNewest' text=$sortingNewestLabel}
        {formradiobutton id='rKParkhausModuleSortDefault' value='default' dataField='sorting' group='data' mandatory=true}
        {gt text='Default' domain='rkparkhausmodule' assign='sortingDefaultLabel'}
        {formlabel for='rKParkhausModuleSortDefault' text=$sortingDefaultLabel}
    </div>
</div>

<div class="form-group">
    {gt text='Amount' domain='rkparkhausmodule' assign='amountLabel'}
    {formlabel for='rKParkhausModuleAmount' text=$amountLabel cssClass='col-sm-3 control-label'}
    <div class="col-sm-9">
        {formintinput id='rKParkhausModuleAmount' dataField='amount' group='data' mandatory=true maxLength=2}
    </div>
</div>

<div class="form-group">
    {gt text='Template' domain='rkparkhausmodule' assign='templateLabel'}
    {formlabel for='rKParkhausModuleTemplate' text=$templateLabel cssClass='col-sm-3 control-label'}
    <div class="col-sm-9">
        {rkparkhausmoduleTemplateSelector assign='allTemplates'}
        {formdropdownlist id='rKParkhausModuleTemplate' dataField='template' group='data' mandatory=true items=$allTemplates cssClass='form-control'}
    </div>
</div>

<div id="customTemplateArea" class="form-group" data-switch="rKParkhausModuleTemplate" data-switch-value="custom">
    {gt text='Custom template' domain='rkparkhausmodule' assign='customTemplateLabel'}
    {formlabel for='rKParkhausModuleCustomTemplate' text=$customTemplateLabel cssClass='col-sm-3 control-label'}
    <div class="col-sm-9">
        {formtextinput id='rKParkhausModuleCustomTemplate' dataField='customTemplate' group='data' mandatory=false maxLength=80 cssClass='form-control'}
        <span class="help-block">{gt text='Example' domain='rkparkhausmodule'}: <em>itemlist_[objectType]_display.tpl</em></span>
    </div>
</div>

<div class="form-group">
    {gt text='Filter (expert option)' domain='rkparkhausmodule' assign='filterLabel'}
    {formlabel for='rKParkhausModuleFilter' text=$filterLabel cssClass='col-sm-3 control-label'}
    <div class="col-sm-9">
        {formtextinput id='rKParkhausModuleFilter' dataField='filter' group='data' mandatory=false maxLength=255 cssClass='form-control'}
        <span class="help-block">
            <a class="fa fa-filter" data-toggle="modal" data-target="#filterSyntaxModal">{gt text='Show syntax examples' domain='rkparkhausmodule'}</a>
        </span>
    </div>
</div>

{include file='include_filterSyntaxDialog.tpl'}

{pageaddvar name='stylesheet' value='web/bootstrap/css/bootstrap.min.css'}
{pageaddvar name='stylesheet' value='web/bootstrap/css/bootstrap-theme.min.css'}
{pageaddvar name='javascript' value='web/bootstrap/js/bootstrap.min.js'}
