<div class="padding white-bg">
    <div style="margin-bottom:15px;">
        <a class="btn btn-default" href="?cmd=affiliates_adv&action=clients">&larr; {$lang.affadv_manage_affiliate|default:'Manage Affiliate'}</a>
    </div>
    <h3>{$lang.affadv_title|default:'Affiliates Advanced'} - #{$client.id}</h3>
    <p><strong>{$lang.affadv_client|default:'Client'}:</strong> {$client.firstname|escape} {$client.lastname|escape} {if $client.email}({$client.email|escape}){/if}</p>

    {include file='../user/template.tpl'}
</div>
