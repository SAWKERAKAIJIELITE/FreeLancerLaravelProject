<?php

use Illuminate\Support\Facades\Broadcast;

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('super-admin.signup-requests', function ($user) {
    return $user->isAdmin();
});

Broadcast::channel('networker.{userId}.signup-requests', function ($user, $userId) {
    return $user->isNetworker() && (int) $user->id === (int) $userId;
});
