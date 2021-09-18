<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Constants
    |--------------------------------------------------------------------------
    |
    |
    |
    */

    "sys_statuses" => [
        "waiting",
        "pending",
        "delivered",
        "ReturnedToDeliver",
        "ReturnedToClient",
    ],

    "notfications" => [
        "users",
        "stores",
    ],

    "statuses" => [
        "New" => 1600,
        "Reviewed" => 1601,
        "AssignToSupervisor" => 1602,
        "AssignToDistributor" => 1603,
        "Pickup" => 1604,
        "Avaiable" => 1605,
        "Blocked" => 1606,
        "Complete" => 1607,
        "Reject" => 1608,
        "UnderProcess" => 1609,
        "ClientNotExist" => 1610,
        "DeliveredTooffice" => 1611,
        "Cancel" => 1612,
        "NotDelivered" => 1613,
        "SupervisorApprovedDelivery" => 1614,
        "SupervisorApprovedNotDelivery" => 1615
       
    ],

    "store_types" => [
        "VIRTUAL",
        "REAL",
    ],

    "Support_Premissions" => [

        ["Reports", null],

        ["Accounts", [
            "Delivers",
            "Users",
            "Stores",
            "Support",
        ]],
        
        ["Orders", [
            "edit",
            "Change status by excel",
            "Print Invoices",
            "Print Single Invoice",
            "Download Excel",
            "Change status by selector",
            "checkbox",
            "update order status"
        ]],
        ["Prices", [
            "add",
            "delete",
        ]],
        ["Learn With HodHod", [
            "add",
            "delete",
        ]],
        
        ["Policies", [
            "add",
            "delete",
        ]],
        ["Discounts", [
            "add",
            "delete",
            "pin"
        ]],
        ["Withdraw Orders", [
            "withdrawing",
            "download excel"
        ]],
        

    ],

];
