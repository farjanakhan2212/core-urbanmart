<?php

/**
 * Shared helpers for UrbanMart Grocery entity APIs.
 * All entity API classes extend this so every endpoint can enforce
 * the Bearer token before touching the database.
 */
abstract class GroceryApiBase
{
    public function __construct()
    {
    }

    protected function guard()
    {
        if (!GroceryauthApi::verify()) {
            http_response_code(401);
            echo json_encode(['success' => 0, 'message' => 'Unauthorized: valid Bearer token required']);
            exit;
        }
    }

    protected function gridRows($cls, $data)
    {
        $page = max(1, (int)($data->page ?? 1));
        $per = max(1, min(200, (int)($data->perpage ?? 20)));
        $q = isset($data->q) ? trim($data->q) : '';
        $list = call_user_func([$cls, 'grid'], $page, $per, $q);
        return [
            'rows' => $list['rows'],
            'total' => (int)$list['total'],
            'page' => (int)$list['page'],
            'per' => (int)$list['per'],
        ];
    }

    protected function saveRow($cls, $data, array $allowed = [])
    {
        $row = $this->pickFields($data, $allowed);
        if (!$row) {
            echo json_encode(['success' => 0, 'message' => 'No valid fields provided']);
            return;
        }
        $id = call_user_func([$cls, 'insertRow'], $row);
        echo json_encode(['success' => 1, 'message' => 'Saved', 'id' => (int)$id]);
    }

    protected function updateRow($cls, $id, $data, array $allowed = [])
    {
        $row = $this->pickFields($data, $allowed);
        if (!$row) {
            echo json_encode(['success' => 0, 'message' => 'No valid fields provided']);
            return;
        }
        call_user_func([$cls, 'updateRow'], (int)$id, $row);
        echo json_encode(['success' => 1, 'message' => 'Updated', 'id' => (int)$id]);
    }

    protected function deleteRow($cls, $id)
    {
        call_user_func([$cls, 'deleteRow'], (int)$id);
        echo json_encode(['success' => 1, 'message' => 'Deleted', 'id' => (int)$id]);
    }

    protected function findRow($cls, $id)
    {
        $row = call_user_func([$cls, 'findRow'], (int)$id);
        if (!$row) {
            http_response_code(404);
            echo json_encode(['success' => 0, 'message' => 'Record not found']);
            return;
        }
        echo json_encode(['success' => 1, 'row' => $row]);
    }

    protected function pickFields($data, array $allowed)
    {
        $out = [];
        foreach ($allowed as $f) {
            if (isset($data->$f) && $data->$f !== '') {
                $out[$f] = $data->$f;
            }
        }
        return $out;
    }
}