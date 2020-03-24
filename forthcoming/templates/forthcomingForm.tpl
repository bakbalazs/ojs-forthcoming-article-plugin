{**
 * plugins/generic/forthcoming/templates/forthcomingForm.tpl
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Edit forthcoming 
 *
 *}
<script type="text/javascript">
    $(function () {ldelim}
        $('#forthcomingSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
        {rdelim});
</script>
{*{fbvElement type="checkbox" id="forthcoming" label="plugins.generic.forthcoming.fieldDescription" checked=$forthcoming|compare:true}*}
{*{fbvFormButtons id="WGLSettingsFormSubmit" submitText="common.save" hideCancel=true}*}
<form class="pkp_form" id="forthcomingSettingsForm" method="post"
      action="{url op="manage" category="generic" plugin=$pluginName verb="save"}">
    {csrf}

    {fbvElement type="checkbox" id="forthcoming" label="plugins.generic.forthcoming.fieldDescription" checked=$forthcoming|compare:true}

    {fbvFormButtons id="WGLSettingsFormSubmit" submitText="common.save" hideCancel=true}

</form>