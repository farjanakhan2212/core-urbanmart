<?php
abstract class GroceryCrudController extends Controller
{
    public $module = "Grocery";

    //Subclasses override these
    protected $model = '';
    protected $formFields = [];

    //When true, 'active'/'inactive' posted status is stored as 1/0 tinyint.
    //Set to false for entities whose status column is an enum string.
    protected $statusInt = true;

    public function index()
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $q = isset($_GET['q']) ? trim($_GET['q']) : '';
        $c = $this->model;
        $list = call_user_func([$c, 'grid'], $page, 20, $q);
        $this->view(['page' => $page, 'q' => $q, 'list' => $list]);
    }

    public function create()
    {
        $this->view([]);
    }

    public function save($obj = null, $file = null)
    {
        $data = $this->transformFields($obj);
        if (!$data) {
            redirect('create');
            return;
        }
        $c = $this->model;
        $id = call_user_func([$c, 'insertRow'], $data);
        redirect('index');
    }

    public function edit($id, $obj = null, $file = null)
    {
        $this->view(['id' => (int)$id]);
    }

    public function update($id, $obj = null, $file = null)
    {
        $c = $this->model;
        $data = $this->transformFields($obj);
        if ($data) call_user_func([$c, 'updateRow'], (int)$id, $data);
        redirect('index');
    }

    public function delete($id, $obj = null, $file = null)
    {
        $c = $this->model;
        call_user_func([$c, 'deleteRow'], (int)$id);
        redirect('index');
    }

    protected function transformFields($obj)
    {
        $src = is_object($obj) ? (array)$obj : (is_array($obj) ? $obj : []);
        $data = [];
        foreach ($this->formFields as $f) {
            if (array_key_exists($f, $src) && $src[$f] !== '') {
                $data[$f] = $src[$f];
            }
        }
        foreach ($data as $k => $v) {
            if (is_string($v) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/', $v)) {
                $data[$k] = str_replace('T', ' ', substr($v, 0, 10) . ' ' . substr($v, 11, 8));
            }
        }
        if (!$this->statusInt && array_key_exists('status', $data) && !in_array($data['status'], ['active', 'inactive', 'expired', 'pending', 'approved', 'rejected'])) {
            $data['status'] = (int)$data['status'] === 1 ? 'active' : 'inactive';
        }
        if ($this->statusInt && array_key_exists('status', $data)) {
            if ($data['status'] === 'active') $data['status'] = 1;
            elseif ($data['status'] === 'inactive') $data['status'] = 0;
        }
        return $data;
    }
}