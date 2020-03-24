{**
 * templates/forthcoming.tpl
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Display Forthcoming articles
 *}

{include file="frontend/components/header.tpl" pageTitleTranslated=$title}
<div class="page page_forthcoming">

    <div class="current_page_title">
        <h3 class="text-center">
            {translate key="plugins.generic.forthcoming.defaultPageTitle"}
        </h3>
    </div>
    <div class="article_summary">
        {foreach from=$forthcoming item=article}
            {assign var=articlePath value=$article->getBestId()}
            <div class="summary_title_wrapper">
                <a class="article_title"
                   {if $journal}href="{url journal=$journal->getPath() page="article" op="view" path=$articlePath}"
                   {else}href="{url page="article" op="view" path=$articlePath}"{/if}>
                    {$article->getLocalizedFullTitle()|escape}
                </a>
            </div>
            <div class="article_authors">
                <ul class="authors_list">
                    {strip}
                        {foreach from=$article->getAuthors() item=author key=authorNumber}
                            {assign var="familyName" value=$author->getFamilyName($currentLocale)|escape}
                            {assign var="givenName" value= $author->getGivenName($currentLocale)|escape}
                            <li class="entry_author_block">
                                {if $author->getOrcid()}
                                    <a class="orcid-image-url" href="{$author->getOrcid()}"><img
                                                src="{$baseUrl}/{$orcidImageUrl}"></a>
                                {/if}
                                {if $familyName eq "" && $givenName eq ""}
                                {else}
                                    <span class="author_name_wrapper">
								{if $currentLocale == "hu_HU"}
                                    {capture assign=localizedAuthorName}{$familyName} {$givenName}{/capture}
                                {else}
                                    {capture assign=localizedAuthorName}{$givenName} {$familyName}{/capture}
                                {/if}

								<a href="{url router=$smarty.const.ROUTE_PAGE page="search" authors=$author->getFullName()}">
									{if $author->getUserGroupId() == 203}
                                        {$localizedAuthorName}
                                        <span>({translate key="plugins.themes.deenk.defult.translator"})</span>
                                    {else}
                                        {$localizedAuthorName}
                                    {/if}
								</a>
							</span>
                                    {if $authorNumber+1 !== $article->getAuthors()|@count}
                                        <span class="author-delimiter">;</span>
                                    {/if}
                                {/if}
                            </li>
                        {/foreach}
                    {/strip}
                </ul>
            </div>
            <div class="article_summary_meta">
                {if $article->getPages()}
                    <div class="article_pages">
                        {$article->getPages()|escape}
                    </div>
                {/if}
                {assign var=abstract value=$article->getLocalizedAbstract()|strip_unsafe_html}
                {assign var=abstractSort value=$article->getLocalizedAbstract()|strip_tags:false:'<b><br>'|mb_convert_encoding:'UTF-8'|replace:'?':''|substr:0:1399}
                {if $currentJournal->getSetting('showabstract') eq "1"}
                    {if $article->getLocalizedAbstract() }
                        <div class="abstract-meta">
                            <div class="article_abstract">
                                {if $article->getLocalizedAbstract()|strlen lt 1400}
                                    <div class="abstract-sort">
                                        {$article->getLocalizedAbstract()}
                                    </div>
                                {/if}
                                {if $article->getLocalizedAbstract()|strlen gt 1400}
                                    <div class="abstract-sort">
                                        {$abstractSort}<span class="three_dots">...</span><span
                                                class="abstract_complete">{$article->getLocalizedAbstract()|strip_tags:false:'<b><br>'|mb_convert_encoding:'UTF-8'|replace:'?':''|substr:1399}</span>
                                    </div>
                                {/if}
                            </div>
                            {if $article->getLocalizedAbstract()|strlen gt 1400}
                                <a class="abstract_show_more ojs-show-more">{translate key="plugins.themes.deenk.defult.abstract.show"}</a>
                            {/if}
                        </div>
                    {/if}
                {/if}

{*                <div class="article_stat">*}
{*                    <div class="view_stat">*}
{*                        <i style="color:gray;" class="fas fa-eye"></i> {$article->getViews()}*}
{*                    </div>*}
{*                    <div class="download_stat">*}
{*                        {assign var=galleys value=$article->getGalleys()}*}
{*                        {if $galleys}*}
{*                            {foreach from=$galleys item=galley name=galleyList}*}
{*                                <i style="color:green;" class="fas fa-download"></i>*}
{*                                {$galley->getViews()}*}
{*                            {/foreach}*}
{*                        {/if}*}
{*                    </div>*}
{*                </div>*}

                <div class="galleys_links">
                    {foreach from=$article->getGalleys() item=galley}
                        {if $primaryGenreIds}
                            {assign var="file" value=$galley->getFile()}
                            {if !$galley->getRemoteUrl() && !($file && in_array($file->getGenreId(), $primaryGenreIds))}
                                {continue}
                            {/if}
                        {/if}
                        {assign var="hasArticleAccess" value=$hasAccess}
                        {assign var="hasArticleAccess" value=1}
                        {include file="frontend/objects/galley_link.tpl" parent=$article hasAccess=$hasArticleAccess purchaseFee=$currentJournal->getSetting('purchaseArticleFee') purchaseCurrency=$currentJournal->getSetting('currency')}
                    {/foreach}
                </div>
            </div>
        {/foreach}
    </div>

</div>

{include file="frontend/components/footer.tpl"}
