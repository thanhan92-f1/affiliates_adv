<?php

class affiliates_adv extends OtherModule
{
    protected $version = "2.0.0";
    protected $description = "Advanced affiliate module package with admin, user and Location V2 style API routes.";
    protected $modname = "Affiliates Advanced";
    protected $info = [
        "haveadmin" => false,
        "haveuser" => true,
        "havetpl" => false,
        "haveapi" => true,
        "extras_menu" => false,
        "client_mainmenu" => false,
        "leftmenu" => false
    ];
    protected $lang = [
        "english" => [
            "affadv_title" => "Affiliates Advanced",
            "affadv_summary" => "Affiliate Summary",
            "affadv_stats" => "Affiliate Stats",
            "affadv_commission_plans" => "Commission Plans",
            "affadv_vouchers" => "Affiliate Vouchers",
            "affadv_create_voucher" => "Create Voucher",
            "affadv_commissions" => "Commissions",
            "affadv_activate" => "Activate affiliate account",
            "affadv_not_affiliate" => "Your client account is not active as an affiliate yet.",
            "affadv_affiliate_id" => "Affiliate ID",
            "affadv_balance" => "Balance",
            "affadv_pending" => "Pending",
            "affadv_total_commissions" => "Total commissions",
            "affadv_total_withdrawn" => "Total withdrawn",
            "affadv_visits" => "Visits",
            "affadv_conversion" => "Conversion",
            "affadv_referral_url" => "Referral URL",
            "affadv_landing_page" => "Landing page",
            "affadv_total_signups" => "Total signups",
            "affadv_monthly_signups" => "Monthly signups",
            "affadv_total_commission_stat" => "Total commission",
            "affadv_monthly_commission_stat" => "Monthly commission",
            "affadv_total_visits" => "Total visits",
            "affadv_monthly_visits" => "Monthly visits",
            "affadv_use" => "Use",
            "affadv_name" => "Name",
            "affadv_type" => "Type",
            "affadv_rate" => "Rate",
            "affadv_recurring" => "Recurring",
            "affadv_voucher" => "Voucher",
            "affadv_yes" => "Yes",
            "affadv_no" => "No",
            "affadv_no_commission_plans" => "No commission plans available.",
            "affadv_save_plan" => "Save commission plan",
            "affadv_code" => "Code",
            "affadv_value" => "Value",
            "affadv_cycle" => "Cycle",
            "affadv_expires" => "Expires",
            "affadv_usage" => "Usage",
            "affadv_plan" => "Plan",
            "affadv_action" => "Action",
            "affadv_delete" => "Delete",
            "affadv_no_vouchers" => "No vouchers found.",
            "affadv_discount_value" => "Discount value",
            "affadv_max_usage" => "Max usage",
            "affadv_audience" => "Audience",
            "affadv_new_clients" => "New clients",
            "affadv_existing_clients" => "Existing clients",
            "affadv_all_clients" => "All clients",
            "affadv_create" => "Create voucher",
            "affadv_date" => "Date",
            "affadv_order" => "Order",
            "affadv_customer" => "Customer",
            "affadv_total" => "Total",
            "affadv_commission" => "Commission",
            "affadv_paid" => "Paid",
            "affadv_no_commissions" => "No commissions found.",
            "affadv_filters" => "Filters",
            "affadv_client_id" => "Client ID",
            "affadv_status" => "Status",
            "affadv_search" => "Search",
            "affadv_reset" => "Reset",
            "affadv_activate_client" => "Activate Client",
            "affadv_view_client" => "View",
            "affadv_manage_affiliate" => "Manage Affiliate",
            "affadv_client" => "Client",
            "affadv_email" => "Email",
            "affadv_plan_selection" => "Plan Selection",
            "affadv_voucher_plan" => "Voucher Plan",
            "affadv_delete_confirm" => "Delete this voucher?",
            "affadv_invalid_request" => "Invalid request",
            "affadv_client_required" => "Client ID is required",
            "affadv_affiliate_not_found" => "Affiliate account not found",
            "affadv_plan_required" => "Client ID and plan ID are required",
            "affadv_commission_required" => "Client ID and commission ID are required",
            "affadv_voucher_create_failed" => "Unable to create voucher",
            "affadv_commission_save_failed" => "Unable to change commission plan",
            "affadv_invalid_commission_plan" => "Invalid commission plan",
            "affadv_page" => "Page",
            "affadv_per_page" => "Per page",
            "affadv_all_statuses" => "All statuses",
            "affadv_paid_status" => "Paid",
            "affadv_unpaid_status" => "Unpaid",
            "affadv_module_clientarea" => "Client Area",
            "affadv_module_adminarea" => "Admin Area"
        ]
    ];
    protected $affiliateModel;

    protected function lang()
    {
        return HBRegistry::language();
    }

    public function translate($key, $default = null)
    {
        $lang = $this->lang();
        if ($lang) {
            $translated = $lang->translate($key);
            if ($translated && $translated !== $key) {
                return $translated;
            }
        }
        return $default !== null ? $default : $key;
    }

    protected function getAffiliateModel()
    {
        if (!$this->affiliateModel) {
            $this->affiliateModel = HBLoader::LoadModel("affiliates/affiliates_user");
        }
        return $this->affiliateModel;
    }

    public function getAffiliateObject($clientId)
    {
        $clientId = (int) $clientId;
        if (!$clientId) {
            return false;
        }
        HBLoader::LoadComponent("Affiliate");
        $affiliate = new Affiliate($clientId);
        if (!($affiliate->getState() & Affiliate::STATE_FULLY_LOADED)) {
            return false;
        }
        return $affiliate;
    }

    public function getClientAffiliateRecord($clientId)
    {
        $q = $this->db->prepare("SELECT cd.id, cd.firstname, cd.lastname, cd.companyname, cd.email, a.id AS affiliate_id, a.status AS affiliate_status\n            FROM hb_client_details cd\n            LEFT JOIN hb_aff a ON a.client_id = cd.id\n            WHERE cd.id = ?\n            LIMIT 1");
        $q->execute([(int) $clientId]);
        $row = $q->fetch(PDO::FETCH_ASSOC);
        $q->closeCursor();
        return $row ?: false;
    }

    public function listAffiliateClients($params = [])
    {
        $sorter = new Sorter("affadv_admin_clients", ["cd.id", "cd.firstname", "cd.lastname", "cd.email", "a.status"], $params);
        $query = "SELECT cd.id, cd.firstname, cd.lastname, cd.companyname, cd.email, a.id AS affiliate_id, a.status AS affiliate_status\n            FROM hb_client_details cd\n            LEFT JOIN hb_aff a ON a.client_id = cd.id";
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
        $count = $this->db->prepare("SELECT COUNT(*) FROM (" . $query . ") t");
        $count->execute($exec);
        $total = (int) $count->fetch(PDO::FETCH_COLUMN);
        $count->closeCursor();
        $order = isset($params["orderby"]) ? explode("|", $params["orderby"]) : ["cd.id", "DESC"];
        $allowed = ["cd.id", "cd.firstname", "cd.lastname", "cd.email", "a.status"];
        $field = in_array($order[0], $allowed, true) ? $order[0] : "cd.id";
        $direction = isset($order[1]) && strtoupper($order[1]) === "ASC" ? "ASC" : "DESC";
        $perPage = $sorter->getPerPage();
        $page = max(0, (int) $sorter->getCurrentPage());
        $offset = $page * $perPage;
        $stmt = $this->db->prepare($query . " ORDER BY {$field} {$direction} LIMIT {$offset}, {$perPage}");
        $stmt->execute($exec);
        $list = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return [
            "list" => $list,
            "pagination" => [
                "perpage" => $perPage,
                "totalpages" => max(1, (int) ceil($total / max(1, $perPage))),
                "sorterrecords" => $total,
                "sorterpage" => $page
            ],
            "currentfilter" => $current
        ];
    }

    protected function getClientGroupId($clientId)
    {
        $q = $this->db->prepare("SELECT group_id FROM hb_client_access WHERE id = ? LIMIT 1");
        $q->execute([(int) $clientId]);
        $groupId = $q->fetch(PDO::FETCH_COLUMN);
        $q->closeCursor();
        return $groupId ? (int) $groupId : 0;
    }

    protected function parseOrderNames(array $orders)
    {
        if (empty($orders)) {
            return $orders;
        }
        foreach ($orders as &$order) {
            if (!empty($order["pname"])) {
                list($order["pname"]) = Engine::singleton()->getObject("language")->parseTag([$order["pname"]]);
            }
        }
        return $orders;
    }

    public function getAffiliateInfoByClient($clientId)
    {
        $clientId = (int) $clientId;
        if (!$clientId) {
            return false;
        }
        $affiliate = $this->getAffiliateObject($clientId);
        if (!$affiliate) {
            return false;
        }
        $q = $this->db->prepare("SELECT a.*, (a.total_commissions-a.total_withdrawn) AS balance, cb.currency_id\n            FROM hb_aff a\n            JOIN hb_client_billing cb ON (cb.client_id=a.client_id)\n            WHERE a.client_id=? LIMIT 1");
        $q->execute([$clientId]);
        $data = $q->fetch(PDO::FETCH_ASSOC);
        $q->closeCursor();
        if (!$data) {
            return false;
        }
        $data["stats"] = $this->getAffiliateStatsByClient($clientId);
        $data["landingpage"] = $affiliate->getlandingUrl();
        $data["selected_commision_plan"] = $this->getAffiliateModel()->clientCommissionPlan($affiliate->getId());
        $data["referral_url"] = Utilities::checkSecureURL(HBConfig::getConfig("InstallURL")) . "?affid=" . $affiliate->getId();
        return $data;
    }

    public function getAffiliateStatsByClient($clientId)
    {
        $affiliate = $this->getAffiliateObject($clientId);
        if (!$affiliate) {
            return false;
        }
        return $this->getAffiliateModel()->getAffiliateStats($affiliate->getId());
    }

    public function getClientCommissionPlans($clientId, $voucherEnabled = false)
    {
        $affiliate = $this->getAffiliateObject($clientId);
        if (!$affiliate) {
            return [];
        }
        return $this->getAffiliateModel()->getCommisionPlans($voucherEnabled, $affiliate->commision_plans, $this->getClientGroupId($clientId));
    }

    public function getSelectedCommissionPlanId($clientId)
    {
        $affiliate = $this->getAffiliateObject($clientId);
        if (!$affiliate) {
            return false;
        }
        return $this->getAffiliateModel()->clientCommissionPlan($affiliate->getId());
    }

    public function saveClientCommissionPlan($clientId, $commissionId)
    {
        $affiliate = $this->getAffiliateObject($clientId);
        if (!$affiliate) {
            return false;
        }
        return $this->getAffiliateModel()->setClientCommissionPlan($affiliate->getId(), (int) $commissionId);
    }

    public function getClientVouchers($clientId)
    {
        $affiliate = $this->getAffiliateObject($clientId);
        if (!$affiliate) {
            return [];
        }
        return $this->getAffiliateModel()->getVouchers($affiliate->getId(), $affiliate->commision_plans);
    }

    public function deleteClientVoucher($clientId, $couponId)
    {
        $affiliate = $this->getAffiliateObject($clientId);
        if (!$affiliate) {
            return false;
        }
        return $this->getAffiliateModel()->removeVoucher((int) $couponId, $affiliate->getId());
    }

    public function createClientVoucher($clientId, $planId, $params)
    {
        $affiliate = $this->getAffiliateObject($clientId);
        if (!$affiliate) {
            Engine::addError($this->translate("affadv_affiliate_not_found", "Affiliate account not found"));
            return false;
        }
        $planId = (int) $planId;
        $plan = false;
        foreach ($this->getClientCommissionPlans($clientId, true) as $item) {
            if ((int) $item["id"] === $planId) {
                $plan = $item;
                break;
            }
        }
        if (!$plan) {
            Engine::addError($this->translate("affadv_invalid_commission_plan", "Invalid commission plan"));
            return false;
        }
        $params["code"] = isset($params["code"]) ? preg_replace("/[^A-Za-z0-9]/", "", $params["code"]) : "";
        if ($params["code"] && !$this->getAffiliateModel()->canUseNewCode($params["code"])) {
            Engine::addError("youcantusethiscode");
            return false;
        }
        if (empty($params["code"])) {
            $params["code"] = $this->getAffiliateModel()->getUniqueCouponCode();
        }
        if (!isset($params["discount"]) || $params["discount"] === "") {
            $params["discount"] = 1;
        }
        if (!isset($params["cycle"]) || $params["cycle"] === "") {
            $params["cycle"] = "once";
        }
        if (!empty($params["expires"])) {
            $params["expires"] = Utilities::format_date($params["expires"]);
        }
        $params["max_usage"] = isset($params["max_usage"]) ? (int) $params["max_usage"] : 0;
        if (!empty($params["audience"]) && !in_array($params["audience"], ["new", "existing", "all"], true)) {
            $params["audience"] = "new";
        }
        return $this->getAffiliateModel()->addVoucher($affiliate->getId(), $plan, $params);
    }

    protected function buildCommissionFilters($affiliateId, array $filter = [])
    {
        $exec = [":aff_id" => (int) $affiliateId];
        $where = ["a.aff_id = :aff_id", "a.commission > 0"];

        if (!empty($filter["date_from"])) {
            $where[] = "DATE(a.date_created) >= :date_from";
            $exec[":date_from"] = $filter["date_from"];
        }
        if (!empty($filter["date_to"])) {
            $where[] = "DATE(a.date_created) <= :date_to";
            $exec[":date_to"] = $filter["date_to"];
        }
        if (!empty($filter["paid_status"])) {
            if ($filter["paid_status"] === "paid") {
                $where[] = "a.paid = 1";
            } elseif ($filter["paid_status"] === "unpaid") {
                $where[] = "a.paid = 0";
            }
        }
        if (!empty($filter["order_id"])) {
            $where[] = "CAST(a.order_id AS CHAR) LIKE :order_id";
            $exec[":order_id"] = "%" . trim((string) $filter["order_id"]) . "%";
        }

        return ["where" => $where, "exec" => $exec];
    }

    protected function getCommissionOrderBy($orderBy)
    {
        $parts = strpos((string) $orderBy, "|") !== false ? explode("|", $orderBy, 2) : [(string) $orderBy, "DESC"];
        $field = !empty($parts[0]) ? $parts[0] : "id";
        $direction = !empty($parts[1]) && strtoupper($parts[1]) === "ASC" ? "ASC" : "DESC";
        $map = [
            "id" => "a.id",
            "date_created" => "COALESCE(NULLIF(a.date_created, 0), NULLIF(i.datepaid, 0), o.date_created)",
            "commission" => "a.commission",
            "paid" => "a.paid",
            "order_id" => "a.order_id",
            "total" => "o.total"
        ];
        $column = isset($map[$field]) ? $map[$field] : $map["id"];
        return $column . " " . $direction;
    }

    protected function getCommissionDetailsByIds(array $ids)
    {
        if (!$ids) {
            return [];
        }

        $placeholders = implode(",", array_fill(0, count($ids), "?"));
        $sql = "SELECT a.*, o.total, o.status,
            COALESCE(d.name,ac.domain) as domain, d.id as dom_id, ac.id as acc_id,
            ac.status as acstatus, d.status as domstatus, cb.currency_id,
            o.client_id, cd.firstname, cd.lastname, cd.companyname, p.name as pname,
            oi.status inv_status, oi.paid_id inv_paid, oi.id inv_id, oi.total inv_total, oi.date inv_date, oi.duedate inv_due,
            i.status as invstatus, COALESCE(NULLIF(a.date_created, 0), NULLIF(i.datepaid, 0), o.date_created) date_created,
            i.date invoice_date, i.duedate invoice_due, i.`total` + i.`credit` as invoice_total
            FROM hb_aff_orders a
            LEFT JOIN hb_orders o ON(o.id=a.order_id)
            LEFT JOIN hb_invoices i ON (a.invoice_id=i.id)
            LEFT JOIN hb_client_details cd ON (a.client_id=cd.id)
            LEFT JOIN hb_client_billing cb ON (cd.id=cb.client_id)
            LEFT JOIN hb_domains d ON (d.order_id=o.id)
            LEFT JOIN hb_accounts ac ON (ac.order_id=o.id)
            LEFT JOIN hb_products p ON (p.id=ac.product_id)
            LEFT JOIN hb_invoices oi ON (o.invoice_id=oi.id)
            WHERE a.id IN (" . $placeholders . ")";
        $q = $this->db->prepare($sql);
        $q->execute(array_values($ids));
        $rows = $q->fetchAll(PDO::FETCH_ASSOC);
        $q->closeCursor();

        $orders = [];
        $lang = Engine::singleton()->getObject("language");
        foreach ($rows as $row) {
            if (!isset($orders[$row["id"]])) {
                $orders[$row["id"]] = $row;
            }
            $orders[$row["id"]]["pname"] = $lang ? $lang->parseTag($row["pname"]) : $row["pname"];
            if (!empty($row["dom_id"])) {
                $orders[$row["id"]]["domains"][] = ["name" => $row["domain"], "id" => $row["dom_id"], "status" => $row["domstatus"]];
            }
            if (!empty($row["acc_id"])) {
                $orders[$row["id"]]["accounts"][] = ["name" => $orders[$row["id"]]["pname"], "id" => $row["acc_id"], "status" => $row["acstatus"]];
            }
        }

        $sorted = [];
        foreach ($ids as $id) {
            if (isset($orders[$id])) {
                $sorted[] = $orders[$id];
            }
        }

        return $sorted;
    }

    public function getClientCommissions($clientId, $params = [])
    {
        $affiliate = $this->getAffiliateObject($clientId);
        if (!$affiliate) {
            return ["list" => [], "pagination" => ["perpage" => 20, "totalpages" => 1, "sorterrecords" => 0, "sorterpage" => 0], "currentfilter" => []];
        }
        $sorter = new Sorter("affadv_commissions_" . (int) $clientId, ["id", "date_created", "commission", "paid", "order_id", "total"], $params);
        if (empty($params["perpage"])) {
            $sorter->setPerPage(20);
        }
        $current = $sorter->getCurrentFilter();
        $filterData = $this->buildCommissionFilters($affiliate->getId(), $current);
        $perPage = max(1, (int) $sorter->getPerPage());

        $countSql = "SELECT COUNT(*)
            FROM hb_aff_orders a
            WHERE " . implode(" AND ", $filterData["where"]);
        $count = $this->db->prepare($countSql);
        $count->execute($filterData["exec"]);
        $total = (int) $count->fetch(PDO::FETCH_COLUMN);
        $count->closeCursor();

        $totalPages = max(1, (int) ceil($total / $perPage));
        $page = max(0, min((int) $sorter->getCurrentPage(), $total > 0 ? $totalPages - 1 : 0));
        $offset = $page * $perPage;

        $idSql = "SELECT a.id
            FROM hb_aff_orders a
            LEFT JOIN hb_orders o ON(o.id=a.order_id)
            LEFT JOIN hb_invoices i ON (a.invoice_id=i.id)
            WHERE " . implode(" AND ", $filterData["where"]) . "
            ORDER BY " . $this->getCommissionOrderBy(isset($params["orderby"]) ? $params["orderby"] : "id|DESC") . "
            LIMIT " . (int) $offset . ", " . (int) $perPage;
        $idsStmt = $this->db->prepare($idSql);
        $idsStmt->execute($filterData["exec"]);
        $ids = $idsStmt->fetchAll(PDO::FETCH_COLUMN);
        $idsStmt->closeCursor();

        $orders = $this->getCommissionDetailsByIds(array_map("intval", $ids));
        $pagination = [
            "perpage" => $perPage,
            "totalpages" => $totalPages,
            "sorterrecords" => $total,
            "sorterpage" => $page
        ];
        return [
            "list" => $orders,
            "pagination" => $pagination,
            "currentfilter" => $current
        ];
    }

    public function activateClientAffiliate($clientId)
    {
        $clientId = (int) $clientId;
        if (!$clientId) {
            return false;
        }
        HBLoader::LoadComponent("Affiliate");
        $affiliate = Affiliate::activateAffiliate($clientId);
        if ($affiliate && ($affiliate->getState() & Affiliate::STATE_FULLY_LOADED)) {
            return $affiliate;
        }
        return false;
    }
}

?>