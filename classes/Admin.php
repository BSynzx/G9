<?php

require_once __DIR__ . '/User.php';

class Admin extends User
{
    public function getRole(): string      { return 'Administrator'; }
    public function getTableName(): string { return 'Admin'; }
    public function getIdColumn(): string  { return 'Admin_ID'; }
}
