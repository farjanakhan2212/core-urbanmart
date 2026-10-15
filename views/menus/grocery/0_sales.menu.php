<?php
echo Menu::item([
    "id" => "grocery-pos",
    "name" => "Point of Sale",
    "icon" => iconClass([
        "adminlte" => "nav-icon fa fa-wrench",
        "staradmin" => "menu-icon mdi mdi-table",
        "intellect" => "fas fa-question-circle",
        "deskapp" => "nav-icon fa fa-wrench",
        "ruangadmin" => "nav-icon fa fa-solid fa-cash-register",
    ]),
    "route" => "#pos",
    "links" => [
        ["route" => "GroceryPos", "text" => "POS Terminal", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
        ["route" => "GrocerySale", "text" => "Sales & Invoices", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
        ["route" => "GroceryPurchase", "text" => "Purchases", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
        ["route" => "GroceryOrder", "text" => "Orders", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
        ["route" => "GroceryDelivery", "text" => "Deliveries", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
    ],
]);