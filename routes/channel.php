<?php

Broadcast::channel('Taxi_New_Order-channel', function () {
    return true; //Always return true or false
});

// Broadcast::channel('admin.{userId}', function ($user, $userId) {
//     return $user->id === $userId;
// });

