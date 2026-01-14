<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'email' => 'user1@example.com',
                'password' => 'Lorem ipsum dolor sit amet',
                'created' => '2026-01-12 15:40:53',
                'modified' => '2026-01-12 15:40:53',
            ],
        ];
        parent::init();
    }
}
