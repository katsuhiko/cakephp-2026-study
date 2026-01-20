<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * TagsFixture
 */
class TagsFixture extends TestFixture
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
                'title' => 'First Tag',
                'created' => '2026-01-12 15:40:53',
                'modified' => '2026-01-12 15:40:53',
            ],
            [
                'id' => 2,
                'title' => 'Second Tag',
                'created' => '2026-01-12 15:40:53',
                'modified' => '2026-01-12 15:40:53',
            ],
            [
                'id' => 3,
                'title' => 'Third Tag',
                'created' => '2026-01-12 15:40:53',
                'modified' => '2026-01-12 15:40:53',
            ],
        ];
        parent::init();
    }
}
