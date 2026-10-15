<?php
echo Menu::item([
    "id" => "grocery-inventory",
    "name" => "Inventory",
    "icon" => iconClass([
        "adminlte" => "nav-icon fa fa-wrench",
        "staradmin" => "menu-icon mdi mdi-table",
        "intellect" => "fas fa-question-circle",
        "deskapp" => "nav-icon fa fa-wrench",
        "ruangadmin" => "nav-icon fa fa-solid fa-boxes-stacked",
    ]),
    "route" => "#inventory",
    "links" => [
        ["route" => "GroceryProduct", "text" => "Products", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
        ["route" => "GroceryProduct/create", "text" => "New Product", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
        ["route" => "GroceryStock", "text" => "Stock Report", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
        ["route" => "GroceryStock/movements", "text" => "Stock Movements", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
        ["route" => "GroceryStock/adjustments", "text" => "Stock Adjustments", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
        ["route" => "GroceryCategory", "text" => "Categories", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
        ["route" => "GrocerySubcategory", "text" => "Subcategories", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
        ["route" => "GroceryBrand", "text" => "Brands", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
        ["route" => "GroceryUnit", "text" => "Units", "icon" => iconClass(["adminlte" => "far fa-circle nav-icon", "deskapp" => "far fa-circle nav-icon", "intellect" => "far fa-circle nav-icon", "ruangadmin" => "far fa-circle nav-icon"])],
    ],
]);