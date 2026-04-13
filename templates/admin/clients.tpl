<div style="padding: 10px">
    <a href="?cmd=affiliates_adv&action=clients&resetfilter=1" {if $currentfilter}style="display:inline"{/if} class="freseter">{$lang.filterisactive}</a>
</div>
<div class="filterdatablock" style="display:none;padding:10px;">
    <form class="searchform filterform" action="?cmd=affiliates_adv&action=clients" method="post" onsubmit="return filter(this)">
        <div class="row">
            <div class="col-12 col-md-3">
                <label>{$lang.affadv_client_id|default:'Client ID'}</label>
                <input type="text" value="{$currentfilter.client_id}" class="form-control" name="filter[client_id]" />
            </div>
            <div class="col-12 col-md-4">
                <label>{$lang.affadv_email|default:'Email'}</label>
                <input type="text" value="{$currentfilter.email}" class="form-control" name="filter[email]" />
            </div>
            <div class="col-12 col-md-3">
                <label>{$lang.affadv_status|default:'Status'}</label>
                <select class="form-control" name="filter[status]">
                    {foreach from=$status_options key=sk item=sv}
                        <option value="{$sk}" {if $currentfilter.status == $sk}selected="selected"{/if}>{$sv}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <table width="100%" cellspacing="2" cellpadding="3" border="0">
            <tbody>
                <tr>
                    <td colspan="6">
                        <div class="text-center" style="margin-top:20px;">
                            <input type="submit" value="{$lang.Search}" class="btn btn-primary btn-sm" />
                            <input type="submit" value="{$lang.Cancel}" class="btn btn-default btn-sm" onclick="$('.filterdatablock').toggle();return false;"/>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        {securitytoken}
    </form>
    <script type="text/javascript">bindFreseter();</script>
</div>
<div class="blu">
    <div class="right">
        <a href="#" class="fadvanced" onclick="$('.filterdatablock').toggle();">{$lang.filterdata}</a>
    </div>
    <div class="clear"></div>
</div>
<div class="clear clearfix"></div>
<a href="?cmd=affiliates_adv&action=clients" id="currentlist" style="display:none" updater="#updater"></a>
<input type="hidden" value="{$totalpages}" name="totalpages2" id="totalpages"/>
<table cellspacing="0" cellpadding="3" border="0" width="100%" class="whitetable hover">
    <thead>
    <tr>
        <th><a href="?cmd=affiliates_adv&action=clients&orderby=cd.id|ASC" class="sortorder">{$lang.affadv_client_id|default:'Client ID'}</a></th>
        <th><a href="?cmd=affiliates_adv&action=clients&orderby=cd.firstname|ASC" class="sortorder">{$lang.affadv_client|default:'Client'}</a></th>
        <th><a href="?cmd=affiliates_adv&action=clients&orderby=cd.email|ASC" class="sortorder">{$lang.affadv_email|default:'Email'}</a></th>
        <th><a href="?cmd=affiliates_adv&action=clients&orderby=a.status|ASC" class="sortorder">{$lang.affadv_status|default:'Status'}</a></th>
        <th width="140">{$lang.affadv_action|default:'Action'}</th>
    </tr>
    </thead>
    <tbody id="updater">
    {foreach from=$clients item=client}
        <tr>
            <td>{$client.id}</td>
            <td>{$client.firstname|escape} {$client.lastname|escape}</td>
            <td>{$client.email|escape}</td>
            <td>{if $client.affiliate_id}{$client.affiliate_status|default:'Active'}{else}Not Affiliate{/if}</td>
            <td>
                <a class="btn btn-mini btn-primary" href="?cmd=affiliates_adv&action=affiliate&client_id={$client.id}">{$lang.affadv_view_client|default:'View'}</a>
                {if !$client.affiliate_id}
                    <a class="btn btn-mini btn-success" href="?cmd=affiliates_adv&action=clients&make=activate&client_id={$client.id}&security_token={$security_token}">{$lang.affadv_activate_client|default:'Activate Client'}</a>
                {/if}
            </td>
        </tr>
    {foreachelse}
        <tr><td colspan="5">{$lang.norec|default:'No records found'}</td></tr>
    {/foreach}
    </tbody>
    <tbody id="psummary">
    <tr>
        <th colspan="100%">
            {$lang.showing} <span id="sorterlow">{$sorterlow}</span> - <span id="sorterhigh">{$sorterhigh}</span> {$lang.of} <span id="sorterrecords">{$sorterrecords}</span>
        </th>
    </tr>
    </tbody>
</table>
<div class="blu">
    <div class="right"><div class="pagination"></div></div>
    <div class="clear"></div>
</div>
