<?php

namespace App\Tests\Func\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Exception;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserRepositoryTest extends KernelTestCase
{
    private UserRepository $userRepository;
    private array $usersFixtures;

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->loadUsersFixtures();
    }

    /**
     * @throws Exception
     */
    private function loadUsersFixtures(): void
    {
        $databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->usersFixtures = $databaseTool->loadAliceFixture([
            __DIR__ . '/UserRepositoryTestFixtures.yaml'
        ]);
    }

    /**
     * @throws Exception
     */
    public function testFindAll(): void
    {
        $usersDb = $this->userRepository->findAll();
        $this->assertNotEmpty($usersDb, 'Aucun utilisateur trouvé dans la base de données.');
    }

    /**
     * @throws Exception
     * @dataProvider userProvider
     */
    public function testGetUser(User $userFixtureId, $userRow): void
    {
        $usersDb = $this->userRepository->findAll();
        $this->assertNotEmpty($usersDb, 'Aucun utilisateur trouvé dans la base de données.');
        $this->assertInstanceOf(User::class, $usersDb[0]);
        $this->assertEquals($userFixtureId->getEmail(), $usersDb[$userRow]->getEmail());
        $this->assertEquals($userFixtureId->getPassword(), $usersDb[$userRow]->getPassword());
    }

    /**
     * @throws Exception
     */
    public function testCount(): void
    {
        $usersCpt = $this->userRepository->count([]);
        $this->assertEquals(count($this->usersFixtures), $usersCpt);
    }

    /**
     * @throws Exception
     */
    private function userProvider(): array
    {
        $this->loadUsersFixtures();
        $returnArray = [];
        $cpt = 0;
        foreach ($this->usersFixtures as $iValue) {
            $returnArray[] = [$iValue, $cpt++];
        }

        return $returnArray;
    }
}