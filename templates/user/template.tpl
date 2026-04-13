<div class="padding white-bg">
    <h3>{$lang.affadv_title|default:'Affiliates Advanced'}</h3>

    {if !$is_affiliate}
        <p>{$lang.affadv_not_affiliate|default:'Your client account is not active as an affiliate yet.'}</p>
        <form action="" method="post">
            <input type="hidden" name="make" value="activate" />
            <button type="submit" class="btn btn-success">{$lang.affadv_activate|default:'Activate affiliate account'}</button>
            {securitytoken}
        </form>
    {else}
        <div class="row-fluid">
            <div class="span6">
                <table class="table table-bordered">
                    <tr><th colspan="2">{$lang.affadv_summary|default:'Affiliate Summary'}</th></tr>
                    <tr><td>{$lang.affadv_affiliate_id|default:'Affiliate ID'}</td><td>{$affiliate.id}</td></tr>
                    <tr><td>{$lang.affadv_balance|default:'Balance'}</td><td>{$affiliate.balance}</td></tr>
                    <tr><td>{$lang.affadv_pending|default:'Pending'}</td><td>{$affiliate.pending}</td></tr>
                    <tr><td>{$lang.affadv_total_commissions|default:'Total commissions'}</td><td>{$affiliate.total_commissions}</td></tr>
                    <tr><td>{$lang.affadv_total_withdrawn|default:'Total withdrawn'}</td><td>{$affiliate.total_withdrawn}</td></tr>
                    <tr><td>{$lang.affadv_visits|default:'Visits'}</td><td>{$affiliate.visits}</td></tr>
                    <tr><td>{$lang.affadv_conversion|default:'Conversion'}</td><td>{$affiliate.conversion}%</td></tr>
                    <tr><td>{$lang.affadv_referral_url|default:'Referral URL'}</td><td><a href="{$referral_url}" target="_blank">{$referral_url}</a></td></tr>
                    <tr><td>{$lang.affadv_landing_page|default:'Landing page'}</td><td>{$landingpage|escape}</td></tr>
                </table>
            </div>
            <div class="span6">
                <table class="table table-bordered">
                    <tr><th colspan="2">{$lang.affadv_stats|default:'Affiliate Stats'}</th></tr>
                    <tr><td>{$lang.affadv_total_signups|default:'Total signups'}</td><td>{$affiliate_stats.total_singups|default:0}</td></tr>
                    <tr><td>{$lang.affadv_monthly_signups|default:'Monthly signups'}</td><td>{$affiliate_stats.monthly_singups|default:0}</td></tr>
                    <tr><td>{$lang.affadv_total_commission_stat|default:'Total commission'}</td><td>{$affiliate_stats.total_commision|default:0}</td></tr>
                    <tr><td>{$lang.affadv_monthly_commission_stat|default:'Monthly commission'}</td><td>{$affiliate_stats.monthly_commision|default:0}</td></tr>
                    <tr><td>{$lang.affadv_total_visits|default:'Total visits'}</td><td>{$affiliate_stats.total_visits|default:0}</td></tr>
                    <tr><td>{$lang.affadv_monthly_visits|default:'Monthly visits'}</td><td>{$affiliate_stats.monthly_visits|default:0}</td></tr>
                </table>
            </div>
        </div>

        <h4>{$lang.affadv_commission_plans|default:'Commission Plans'}</h4>
        <form action="" method="post">
            <input type="hidden" name="make" value="save_plan" />
            <table class="table table-bordered table-striped">
                <tr>
                    {if $select_commission_plan}<th width="30">{$lang.affadv_use|default:'Use'}</th>{/if}
                    <th>{$lang.affadv_name|default:'Name'}</th>
                    <th>{$lang.affadv_type|default:'Type'}</th>
                    <th>{$lang.affadv_rate|default:'Rate'}</th>
                    <th>{$lang.affadv_recurring|default:'Recurring'}</th>
                    <th>{$lang.affadv_voucher|default:'Voucher'}</th>
                </tr>
                {foreach from=$commission_plans item=plan}
                    <tr>
                        {if $select_commission_plan}
                            <td><input type="radio" name="plan_id" value="{$plan.id}" {if $selected_plan_id == $plan.id}checked="checked"{/if} /></td>
                        {/if}
                        <td>{$plan.name|escape}</td>
                        <td>{$plan.type|escape}</td>
                        <td>{$plan.rate}</td>
                        <td>{if $plan.recurring}{$lang.affadv_yes|default:'Yes'}{else}{$lang.affadv_no|default:'No'}{/if}</td>
                        <td>{if $plan.enable_voucher}{$lang.affadv_yes|default:'Yes'}{else}{$lang.affadv_no|default:'No'}{/if}</td>
                    </tr>
                {foreachelse}
                    <tr><td colspan="6">{$lang.affadv_no_commission_plans|default:'No commission plans available.'}</td></tr>
                {/foreach}
            </table>
            {if $select_commission_plan && $commission_plans}
                <button type="submit" class="btn btn-primary">{$lang.affadv_save_plan|default:'Save commission plan'}</button>
            {/if}
            {securitytoken}
        </form>

        <h4>{$lang.affadv_vouchers|default:'Affiliate Vouchers'}</h4>
        <table class="table table-bordered table-striped">
            <tr>
                <th>{$lang.affadv_code|default:'Code'}</th>
                <th>{$lang.affadv_value|default:'Value'}</th>
                <th>{$lang.affadv_cycle|default:'Cycle'}</th>
                <th>{$lang.affadv_expires|default:'Expires'}</th>
                <th>{$lang.affadv_usage|default:'Usage'}</th>
                <th>{$lang.affadv_plan|default:'Plan'}</th>
                <th width="60">{$lang.affadv_action|default:'Action'}</th>
            </tr>
            {foreach from=$vouchers item=voucher}
                <tr>
                    <td>{$voucher.code|escape}</td>
                    <td>{$voucher.value}</td>
                    <td>{$voucher.cycle|escape}</td>
                    <td>{$voucher.expires|escape}</td>
                    <td>{$voucher.num_usage}/{$voucher.max_usage}</td>
                    <td>{$voucher.commision_plan}</td>
                    <td><a class="btn btn-danger btn-mini" href="?cmd=affiliates_adv&make=delete_voucher&id={$voucher.id}&security_token={$security_token}{if $client.id}&client_id={$client.id}&action=affiliate{/if}" onclick="return confirm('{$lang.affadv_delete_confirm|default:'Delete this voucher?'}')">{$lang.affadv_delete|default:'Delete'}</a></td>
                </tr>
            {foreachelse}
                <tr><td colspan="7">{$lang.affadv_no_vouchers|default:'No vouchers found.'}</td></tr>
            {/foreach}
        </table>

        <h4>{$lang.affadv_create_voucher|default:'Create Voucher'}</h4>
        <form action="" method="post">
            <input type="hidden" name="make" value="add_voucher" />
            <div class="row-fluid">
                <div class="span4">
                    <label>{$lang.affadv_plan|default:'Plan'}</label>
                    <select name="plan_id" class="span12">
                        {foreach from=$voucher_plans item=plan}
                            <option value="{$plan.id}">{$plan.name|escape} ({$plan.rate})</option>
                        {/foreach}
                    </select>
                </div>
                <div class="span4">
                    <label>{$lang.affadv_code|default:'Code'}</label>
                    <input type="text" name="code" value="" class="span12" />
                </div>
                <div class="span4">
                    <label>{$lang.affadv_discount_value|default:'Discount value'}</label>
                    <input type="text" name="discount" value="1" class="span12" />
                </div>
            </div>
            <div class="row-fluid">
                <div class="span4">
                    <label>{$lang.affadv_cycle|default:'Cycle'}</label>
                    <input type="text" name="cycle" value="once" class="span12" />
                </div>
                <div class="span4">
                    <label>{$lang.affadv_expires|default:'Expires'}</label>
                    <input type="text" name="expires" value="" class="span12" placeholder="YYYY-MM-DD" />
                </div>
                <div class="span4">
                    <label>{$lang.affadv_max_usage|default:'Max usage'}</label>
                    <input type="text" name="max_usage" value="0" class="span12" />
                </div>
            </div>
            <div class="row-fluid">
                <div class="span4">
                    <label>{$lang.affadv_audience|default:'Audience'}</label>
                    <select name="audience" class="span12">
                        <option value="new">{$lang.affadv_new_clients|default:'New clients'}</option>
                        <option value="existing">{$lang.affadv_existing_clients|default:'Existing clients'}</option>
                        <option value="all">{$lang.affadv_all_clients|default:'All clients'}</option>
                    </select>
                </div>
            </div>
            <br />
            <button type="submit" class="btn btn-success" {if !$voucher_plans}disabled="disabled"{/if}>{$lang.affadv_create|default:'Create voucher'}</button>
            {if $client.id}<input type="hidden" name="client_id" value="{$client.id}" />{/if}
            {securitytoken}
        </form>

        <h4>{$lang.affadv_commissions|default:'Commissions'}</h4>
        <div class="filterdatablock" style="display:block;padding:10px 0;">
            <form class="searchform filterform" action="" method="get">
                {if $client.id}<input type="hidden" name="cmd" value="affiliates_adv" /><input type="hidden" name="action" value="affiliate" /><input type="hidden" name="client_id" value="{$client.id}" />{else}<input type="hidden" name="cmd" value="affiliates_adv" />{/if}
                <div class="row-fluid">
                    <div class="span3">
                        <label>{$lang.affadv_order|default:'Order'}</label>
                        <input type="text" name="filter[order_id]" value="{$commission_currentfilter.order_id|escape}" class="span12" />
                    </div>
                    <div class="span3">
                        <label>{$lang.affadv_status|default:'Status'}</label>
                        <select name="filter[paid_status]" class="span12">
                            {foreach from=$paid_status_options key=pk item=pv}
                                <option value="{$pk}" {if $commission_currentfilter.paid_status == $pk}selected="selected"{/if}>{$pv}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="span3">
                        <label>Date from</label>
                        <input type="text" name="filter[date_from]" value="{$commission_currentfilter.date_from|escape}" class="span12" placeholder="YYYY-MM-DD" />
                    </div>
                    <div class="span3">
                        <label>Date to</label>
                        <input type="text" name="filter[date_to]" value="{$commission_currentfilter.date_to|escape}" class="span12" placeholder="YYYY-MM-DD" />
                    </div>
                </div>
                <br />
                <button type="submit" class="btn btn-primary btn-sm">{$lang.Search}</button>
                <a href="{if $client.id}?cmd=affiliates_adv&action=affiliate&client_id={$client.id}{else}?cmd=affiliates_adv{/if}" class="btn btn-default btn-sm">{$lang.affadv_reset|default:'Reset'}</a>
            </form>
        </div>
        <table class="table table-bordered table-striped">
            <tr>
                <th>{$lang.affadv_date|default:'Date'}</th>
                <th>{$lang.affadv_order|default:'Order'}</th>
                <th>{$lang.affadv_customer|default:'Customer'}</th>
                <th>{$lang.affadv_total|default:'Total'}</th>
                <th>{$lang.affadv_commission|default:'Commission'}</th>
                <th>{$lang.affadv_paid|default:'Paid'}</th>
            </tr>
            {foreach from=$commissions item=commission}
                <tr>
                    <td>{$commission.date_created|escape}</td>
                    <td>{$commission.order_id}</td>
                    <td>{$commission.firstname|escape} {$commission.lastname|escape}</td>
                    <td>{$commission.total}</td>
                    <td>{$commission.commission}</td>
                    <td>{if $commission.paid}{$lang.affadv_yes|default:'Yes'}{else}{$lang.affadv_no|default:'No'}{/if}</td>
                </tr>
            {foreachelse}
                <tr><td colspan="6">{$lang.affadv_no_commissions|default:'No commissions found.'}</td></tr>
            {/foreach}
        </table>
        <div class="blu">
            <div class="right"><div class="pagination"></div></div>
            <div class="clear"></div>
        </div>
    {/if}
</div>
