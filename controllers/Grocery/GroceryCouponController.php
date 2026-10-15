<?php
require_once(__DIR__ . '/GroceryCrudController.php');

class GroceryCouponController extends GroceryCrudController
{
    protected $model = 'GroceryCoupon';
    protected $statusInt = false;
    protected $formFields = ['code', 'type', 'value', 'min_order_amount', 'max_discount', 'starts_at', 'ends_at', 'usage_limit', 'status'];

    public function save($obj = null, $file = null)
    {
        if (!$this->couponDatesValid()) {
            $_SESSION["flash"]["error"] = 'Expiry date must not be earlier than the start date.';
            redirect('create');
            return;
        }
        parent::save($obj, $file);
    }

    public function update($id, $obj = null, $file = null)
    {
        if (!$this->couponDatesValid()) {
            $_SESSION["flash"]["error"] = 'Expiry date must not be earlier than the start date.';
            redirect('edit/' . (int)$id);
            return;
        }
        parent::update($id, $obj, $file);
    }

    private function couponDatesValid()
    {
        $start = isset($_POST['starts_at']) ? str_replace('T', ' ', trim($_POST['starts_at'])) : '';
        $end = isset($_POST['ends_at']) ? str_replace('T', ' ', trim($_POST['ends_at'])) : '';
        if ($start !== '' && $end !== '') {
            return strtotime($end) >= strtotime($start);
        }
        return true;
    }
}