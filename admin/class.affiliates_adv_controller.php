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

    protected function listAffiliateClientsCompat($params)
    {
        if (method_exists($this->module, "listAffiliateClients")) {
            return $this->module->listAffiliateClients($params);
        }

        $db = HBRegistry::db();
        $sorter = new Sorter("affadv_admin_clients", ["cd.id", "cd.firstname", "cd.lastname", "cd.email", "a.status"], $params);
        $query = "SELECT cd.id, cd.firstname, cd.lastname, cd.companyname, cd.email, a.id AS affiliate_id, a.status AS affiliate_status
            FROM hb_client_details cd
            LEFT JOIN hb_aff a ON a.client_id = cd.id";
        $filters = [];
        $exec = [];
        $current = $sorter->getCurrentFilter();

        if (!empty($current["client_id"])) {
            $filters[] = "cd.id = ?";
            $exec[] = (int) $current["client_id"];
        }
        if (!empty($current["email"])) {
            $filters[] = "cd.email LIKE ?";
            $exec[] = "%" . $current["email"] . "%";
        }
        if (!empty($current["status"])) {
            if ($current["status"] === "not_affiliate") {
                $filters[] = "a.id IS NULL";
            } else {
                $filters[] = "a.status = ?";
                $exec[] = $current["status"];
            }
        }

        if ($filters) {
            $query .= " WHERE " . implode(" AND ", $filters);
        }

        $count = $db->prepare("SELECT COUNT(*) FROM (" . $query . ") t");
        $count->execute($exec);
        $total = (int) $count->fetch(PDO::FETCH_COLUMN);
        $count->closeCursor();

        $order = isset($params["orderby"]) ? explode("|", $params["orderby"]) : ["cd.id", "DESC"];
        $allowed = ["cd.id", "cd.firstname", "cd.lastname", "cd.email", "a.status"];
        $field = in_array($order[0], $allowed, true) ? $order[0] : "cd.id";
        $direction = isset($order[1]) && strtoupper($order[1]) === "ASC" ? "ASC" : "DESC";
        $perPage = max(1, (int) $sorter->getPerPage());
        $page = max(0, (int) $sorter->getCurrentPage());
        $offset = $page * $perPage;

        $stmt = $db->prepare($query . " ORDER BY {$field} {$direction} LIMIT {$offset}, {$perPage}");
        $stmt->execute($exec);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return [
            "list" => $list,
            "pagination" => [
                "perpage" => $perPage,
                "totalpages" => max(1, (int) ceil($total / $perPage)),
                "sorterrecords" => $total,
                "sorterpage" => $page
            ],
            "currentfilter" => $current
        ];
    }

    public function beforeCall($params)
    {
        $modDir = strtolower($this->module->getModuleDirName());
        $this->template->pageTitle = $this->module->getModName();
        $this->template->module_template_dir = APPDIR_MODULES . "Other" . DS . $modDir . DS . "templates" . DS . "admin";
        $this->template->assign("moduleurl", Utilities::checkSecureURL(HBConfig::getConfig("InstallURL") . "includes/modules/Other/" . $modDir));
        $this->template->assign("modulename", strtolower($this->module->getModuleName()));
        $this->template->assign("modname", $this->module->getModName());
        $this->template->assign("moduleid", $this->module->getModuleId());
        $this->template->assign("action", !empty($params["action"]) ? $params["action"] : "clients");
        $this->template->showtpl = "default";
    }

    public function _default($params)
    {
        return $this->clients($params);
    }

    public function clients($params)
    {
        if (!empty($params["make"]) && $params["make"] === "activate" && !empty($params["token_valid"]) && !empty($params["client_id"])) {
            $this->module->activateClientAffiliate($params["client_id"]);
            Utilities::redirect("?cmd=affiliates_adv&action=clients&client_id=" . (int) $params["client_id"]);
        }

        $result = $this->listAffiliateClientsCompat($params);
        $this->template->assign("clients", $result["list"]);
        $this->template->assign("currentfilter", $result["currentfilter"]);
        $this->template->assign($this->template->sorterUpdate($result["pagination"]));
        $this->template->assign("status_options", [
            "" => $this->lang("affadv_all_statuses", "All statuses"),
            "Active" => "Active",
            "Disabled" => "Disabled",
            "not_affiliate" => "Not Affiliate"
        ]);
    }

    public function affiliate($params)
    {
        $clientId = !empty($params["client_id"]) ? (int) $params["client_id"] : 0;
        if (!$clientId) {
            Utilities::redirect("?cmd=affiliates_adv&action=clients");
        }
        if (!empty($params["make"]) && !empty($params["token_valid"])) {
            if ($params["make"] === "activate") {
                $this->module->activateClientAffiliate($clientId);
                Utilities::redirect("?cmd=affiliates_adv&action=affiliate&client_id=" . $clientId);
            }
            if ($params["make"] === "save_plan" && !empty($params["plan_id"])) {
                $this->module->saveClientCommissionPlan($clientId, $params["plan_id"]);
                Utilities::redirect("?cmd=affiliates_adv&action=affiliate&client_id=" . $clientId);
            }
            if ($params["make"] === "add_voucher" && !empty($params["plan_id"])) {
                $this->module->createClientVoucher($clientId, $params["plan_id"], $params);
                Utilities::redirect("?cmd=affiliates_adv&action=affiliate&client_id=" . $clientId);
            }
            if ($params["make"] === "delete_voucher" && !empty($params["id"])) {
                $this->module->deleteClientVoucher($clientId, $params["id"]);
                Utilities::redirect("?cmd=affiliates_adv&action=affiliate&client_id=" . $clientId);
            }
        }

        $client = $this->module->getClientAffiliateRecord($clientId);
        if (!$client) {
            Utilities::redirect("?cmd=affiliates_adv&action=clients");
        }
        $affiliate = $this->module->getAffiliateObject($clientId);
        $this->template->assign("client", $client);
        $this->template->assign("is_affiliate", (bool) $affiliate);
        if ($affiliate) {
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
        }
        $this->template->assign("paid_status_options", [
            "" => $this->lang("affadv_all_statuses", "All statuses"),
            "paid" => $this->lang("affadv_paid_status", "Paid"),
            "unpaid" => $this->lang("affadv_unpaid_status", "Unpaid")
        ]);
        $this->template->showtpl = "affiliate";
    }
}

?>