<?php

class affiliates_adv_controller extends HBController
{
    public $module;
    public $authorization;
    public $template;
    public $affiliate;

    protected function lang($key, $default = null)
    {
        return $this->module->translate($key, $default);
    }

    protected function isAffiliate()
    {
        return (bool) $this->module->getAffiliateObject($this->authorization->get_id());
    }

    public function beforeCall($params)
    {
        $this->template->pageTitle = $this->module->getModName();
        $this->template->module_template_dir = APPDIR_MODULES . "Other" . DS . strtolower($this->module->getModuleDirName()) . DS . "templates" . DS . "user";
        $this->template->assign("modulename", $this->module->getModuleName());
        $this->template->assign("modname", $this->module->getModName());
        $this->template->assign("moduleid", $this->module->getModuleId());
        $this->template->showtpl = "template";
        if (!$this->authorization->get_login_status()) {
            Engine::addInfo("restrictedarea");
            Utilities::redirect("?cmd=root");
        }
    }

    public function _default($params)
    {
        $clientId = (int) $this->authorization->get_id();
        if (isset($params["make"]) && $params["make"] === "activate" && !empty($params["token_valid"])) {
            $this->module->activateClientAffiliate($clientId);
            Utilities::redirect("?cmd=affiliates_adv");
        }
        if ($this->isAffiliate()) {
            if (isset($params["make"]) && $params["make"] === "save_plan" && !empty($params["token_valid"]) && !empty($params["plan_id"])) {
                $this->module->saveClientCommissionPlan($clientId, $params["plan_id"]);
                Utilities::redirect("?cmd=affiliates_adv");
            }
            if (isset($params["make"]) && $params["make"] === "add_voucher" && !empty($params["token_valid"]) && !empty($params["plan_id"])) {
                $this->module->createClientVoucher($clientId, $params["plan_id"], $params);
                Utilities::redirect("?cmd=affiliates_adv");
            }
            if (isset($params["make"]) && $params["make"] === "delete_voucher" && !empty($params["token_valid"]) && !empty($params["id"])) {
                $this->module->deleteClientVoucher($clientId, $params["id"]);
                Utilities::redirect("?cmd=affiliates_adv");
            }
        }
        $affiliate = $this->module->getAffiliateObject($clientId);
        $this->template->assign("is_affiliate", (bool) $affiliate);
        if (!$affiliate) {
            return null;
        }
        $affiliate->currency = Utilities::getCurrency($affiliate->currency_id);
        $commissionPlans = $this->module->getClientCommissionPlans($clientId);
        $voucherPlans = $this->module->getClientCommissionPlans($clientId, true);
        $commissions = $this->module->getClientCommissions($clientId, $params);
        $this->template->assign("affiliate", (array) $affiliate);
        $this->template->assign("affiliate_info", $this->module->getAffiliateInfoByClient($clientId));
        $this->template->assign("affiliate_stats", $this->module->getAffiliateStatsByClient($clientId));
        $this->template->assign("commission_plans", $commissionPlans);
        $this->template->assign("voucher_plans", $voucherPlans);
        $this->template->assign("selected_plan_id", $this->module->getSelectedCommissionPlanId($clientId));
        $this->template->assign("select_commission_plan", HBConfig::getConfig("AffiliateSelectCommissionPlan") == "manual");
        $this->template->assign("vouchers", $this->module->getClientVouchers($clientId));
        $this->template->assign("commissions", $commissions["list"]);
        $this->template->assign("commission_currentfilter", $commissions["currentfilter"]);
        $this->template->assign($this->template->sorterUpdate($commissions["pagination"]));
        $this->template->assign("landingpage", $affiliate->getlandingUrl());
        $this->template->assign("referral_url", Utilities::checkSecureURL(HBConfig::getConfig("InstallURL")) . "?affid=" . $affiliate->getId());
        $this->template->assign("paid_status_options", [
            "" => $this->lang("affadv_all_statuses", "All statuses"),
            "paid" => $this->lang("affadv_paid_status", "Paid"),
            "unpaid" => $this->lang("affadv_unpaid_status", "Unpaid")
        ]);
    }
}

?>