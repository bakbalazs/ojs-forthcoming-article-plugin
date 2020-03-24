<script type="text/javascript">
    $(function () {ldelim}
        $('#forthcomingSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
        {rdelim});
</script>

<form class="pkp_form" id="forthcomingSettingsForm" method="post"
      action="{url op="manage" category="generic" plugin=$pluginName verb="save"}">
    {csrf}

    {include file="controllers/notification/inPlaceNotification.tpl" notificationId="forthcomingFormNotification"}

    {fbvFormArea id="forthcomingDisplayOption" title="plugins.generic.forthcoming.settings.title"}

    {fbvFormSection for="title"}
    {fbvElement type="text" label="plugins.generic.forthcoming.settings.title.label" id="title" value=$title multilingual="true"}
    {/fbvFormSection}

    {/fbvFormArea}

    {fbvFormButtons id="WGLSettingsFormSubmit" submitText="common.save" hideCancel=true}

</form>
