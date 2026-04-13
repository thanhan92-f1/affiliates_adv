<?php

class affiliates_adv_apiroutes
{
    /**
     * All routes in this class are expected to be called through HostBill UserApi
     * with JWT bearer authentication header:
     * Authorization: Bearer <token>
     */
    protected function module()
    {
        return HBLoader::LoadModule("Other/affiliates_adv");
    }

    protected function requireClientId($clientId)
    {
        $clientId = (int) $clientId;
        if (!$clientId) {
            Engine::addError($this->module()->translate("affadv_client_required", "Client ID is required"));
            return false;
        }
        return $clientId;
    }

    /**
     * Affiliate info
        * Required header: Authorization: Bearer <token>
     *
     * @route GET /affiliates_adv/@client_id/info
     */
    public function getInfo($client_id)
    {
        $clientId = $this->requireClientId($client_id);
        if (!$clientId) {
            UserApi::render(["success" => false]);
            return;
        }
        $affiliate = $this->module()->getAffiliateInfoByClient($clientId);
        UserApi::render(["success" => (bool) $affiliate, "affiliate" => $affiliate ?: []]);
    }

    /**
     * Commission plans
        * Required header: Authorization: Bearer <token>
     *
     * @route GET /affiliates_adv/@client_id/commission-plans
     */
    public function getCommissionPlans($client_id)
    {
        $clientId = $this->requireClientId($client_id);
        if (!$clientId) {
            UserApi::render(["success" => false]);
            return;
        }
        $params = UserApi::params();
        $plans = $this->module()->getClientCommissionPlans($clientId, !empty($params["voucher_enabled"]));
        UserApi::render(["success" => true, "commisions" => array_values($plans)]);
    }

    /**
     * Set commission plan
        * Required header: Authorization: Bearer <token>
     *
     * @route POST /affiliates_adv/@client_id/commission-plan/@commission_id
     */
    public function setCommissionPlan($client_id, $commission_id)
    {
        $clientId = $this->requireClientId($client_id);
        if (!$clientId || !(int) $commission_id) {
            UserApi::render(["success" => false]);
            return;
        }
        $saved = $this->module()->saveClientCommissionPlan($clientId, $commission_id);
        UserApi::render(["success" => (bool) $saved, "commission_id" => (int) $commission_id]);
    }

    /**
     * Create voucher
        * Required header: Authorization: Bearer <token>
     *
     * @route POST /affiliates_adv/@client_id/vouchers/@plan_id
     */
    public function addVoucher($client_id, $plan_id)
    {
        $clientId = $this->requireClientId($client_id);
        if (!$clientId || !(int) $plan_id) {
            UserApi::render(["success" => false]);
            return;
        }
        $voucherId = $this->module()->createClientVoucher($clientId, $plan_id, UserApi::params());
        UserApi::render(["success" => (bool) $voucherId, "voucher_id" => (int) $voucherId]);
    }

    /**
     * Vouchers list
        * Required header: Authorization: Bearer <token>
     *
     * @route GET /affiliates_adv/@client_id/vouchers
     */
    public function getVouchers($client_id)
    {
        $clientId = $this->requireClientId($client_id);
        if (!$clientId) {
            UserApi::render(["success" => false]);
            return;
        }
        UserApi::render(["success" => true, "vouchers" => array_values($this->module()->getClientVouchers($clientId))]);
    }

    /**
     * Commissions list
        * Required header: Authorization: Bearer <token>
     *
     * @route GET /affiliates_adv/@client_id/commissions
     */
    public function getCommissions($client_id)
    {
        $clientId = $this->requireClientId($client_id);
        if (!$clientId) {
            UserApi::render(["success" => false]);
            return;
        }
        $result = $this->module()->getClientCommissions($clientId, ["filter" => UserApi::params()] + UserApi::params());
        UserApi::render([
            "success" => true,
            "orders" => array_values($result["list"]),
            "pagination" => $result["pagination"]
        ]);
    }
}

?>