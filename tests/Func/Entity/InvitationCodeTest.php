<?php

namespace App\Tests\Func\Entity;

use App\Entity\InvitationCode;
use App\Repository\InvitationCodeRepository;
use App\Repository\UserRepository;
use DateTime;
use Exception;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintValidator;

class InvitationCodeTest extends KernelTestCase
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
        $this->invitationCodeRepository = static::getContainer()->get(InvitationCodeRepository::class);
    }

    /**
     * @throws Exception
     */
    private function loadInvitationCodeFixtures(): void
    {
        $databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->usersFixtures = $databaseTool->loadAliceFixture([
            static::getContainer()->get('kernel')->getProjectDir().'/tests/fixtures/InvitationCodeRepositoryTestFixtures.yaml'
        ]);
    }

    public function getEntity(string $code, string $description, DateTime $expireAt = new DateTime()): InvitationCode
    {
        return (new InvitationCode())
            ->setCode($code)
            ->setDescription($description)
            ->setExpireAt($expireAt)
            ;
    }

    public function assertHasError(InvitationCode $invitationCode, int $numberOfError): void
    {
        self::bootKernel();
        $errors = self::getContainer()->get('validator')->validate($invitationCode);
        $messages = [];
        /** @var ConstraintValidator $error */
        foreach ($errors as $error) {
            $messages[] = $error->getPropertyPath().' => '.$error->getMessage();
        }
        $this->assertCount($numberOfError, $errors, implode((', '), $messages));
    }

    /**
     * @throws Exception
     */
    public function testValidEntity():void
    {
        $this->assertHasError($this->getEntity('12345', 'Nouvelle Invitation'), 0);
    }

    /**
     * @throws Exception
     */
    public function testInvalidCode():void
    {
        $this->assertHasError($this->getEntity('1e345', 'une lettre'), 1);
        $this->assertHasError($this->getEntity('1345', '4 chiffres'), 1);
        $this->assertHasError($this->getEntity('-1345', 'un négatif'), 1);
    }

    /**
     * @throws Exception
     */
    public function testInvalidBlankCode():void
    {
        $this->assertHasError($this->getEntity('', 'BLANK'), 1);
    }

    /**
     * @throws Exception
     */
    public function testInvalidBlankDescription():void
    {
        $this->assertHasError($this->getEntity('13245', ''), 1);
    }

    /**
     * @throws Exception
     */
    public function testInvalidUsedCode():void
    {
        $this->loadInvitationCodeFixtures();
        $this->assertHasError($this->getEntity('54321', 'KO already used by fixture'), 1);
    }

}