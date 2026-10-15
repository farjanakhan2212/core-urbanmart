<?php

/**
 * UrbanMart Grocery - Dashboard API
 * GET /api/Grocerydashboard/stats
 *
 * All figures come from the live database (GroceryService::buzz()).
 */
class GrocerydashboardApi extends GroceryApiBase
{
    public function stats($data)
    {
        $this->guard();
        echo json_encode(['success' => 1, 'data' => GroceryService::buzz()]);
    }
}