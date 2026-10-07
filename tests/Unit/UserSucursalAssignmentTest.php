<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserSucursalAssignmentTest extends TestCase
{
    public function test_sucursal_id_can_be_mass_assigned_to_a_user(): void
    {
        $user = new User(['sucursal_id' => 17]);

        $this->assertSame(17, $user->sucursal_id);
    }
}
