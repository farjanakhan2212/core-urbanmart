<?php
class GrocerySettingsController extends Controller
{
    public $module = "Grocery";

    public function index()
    {
        $keys = ['shop_name', 'shop_phone', 'shop_email', 'shop_address', 'currency_symbol', 'default_payment_method', 'allow_negative_stock', 'receipt_footer'];
        $rows = GrocerySetting::rows("setting_key IN ('" . implode("','", $keys) . "')");
        $data = [];
        foreach ($rows as $r) $data[$r['setting_key']] = $r['setting_value'];
        $this->view(['data' => $data, 'payment_methods' => GroceryPaymentMethod::rows('status = 1', [], 'name ASC')]);
    }

    public function save($obj = null, $file = null)
    {
        foreach (['shop_name', 'shop_phone', 'shop_email', 'shop_address', 'currency_symbol', 'default_payment_method', 'allow_negative_stock', 'receipt_footer'] as $key) {
            if (isset($_POST[$key])) {
                GrocerySetting::set($key, $_POST[$key]);
            }
        }
        redirect('index');
    }
}