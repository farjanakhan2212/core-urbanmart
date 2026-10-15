<?php
echo Menu::item([
    "id" => "grocery-parties",
    "name" => "Partners",
    "icon" => iconClass([
        "adminlte" => "nav-icon fa fa-wrench",
        "staradmin" => "menu-icon mdi mdi-table",
        "intellect" => "fas fa-question-circle",
        "deskapp" => "nav-icon fa fa-wrench",
        "ruangadmin" => "nav-icon fa fa-solid fa-users",
    ]),
    "route" => "#partners",
    "links" => [
        ["route" => "GrocerySupplier", "text" => "Suppliers", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
        ["route" => "GroceryCustomer", "text" => "Customers", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
        ["route" => "GroceryCoupon", "text" => "Coupons", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
    ],
]);

echo Menu::item([
    "id" => "grocery-money",
    "name" => "Money",
    "icon" => iconClass([
        "adminlte" => "nav-icon fa fa-wrench",
        "staradmin" => "menu-icon mdi mdi-table",
        "intellect" => "fas fa-question-circle",
        "deskapp" => "nav-icon fa fa-wrench",
        "ruangadmin" => "nav-icon fa fa-solid fa-money-bill-wave",
    ]),
    "route" => "#money",
    "links" => [
        ["route" => "GroceryPayment", "text" => "Payments", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
        ["route" => "GroceryExpense", "text" => "Expenses", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
        ["route" => "GroceryExpenseCategory", "text" => "Expense Categories", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
        ["route" => "GroceryReport", "text" => "Reports & KPIs", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
        ["route" => "GrocerySettings", "text" => "Settings", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
    ],
]);