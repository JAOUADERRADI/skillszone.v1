<?php

namespace App\Tests\Controller;

use App\Entity\Course;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CourseControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/course/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Course::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Course index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'course[title]' => 'Testing',
            'course[description]' => 'Testing',
            'course[image]' => 'Testing',
            'course[createdAt]' => 'Testing',
            'course[updatedAt]' => 'Testing',
            'course[user]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Course();
        $fixture->setTitle('My Title');
        $fixture->setDescription('My Title');
        $fixture->setImage('My Title');
        $fixture->setCreatedAt('My Title');
        $fixture->setUpdatedAt('My Title');
        $fixture->setUser('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Course');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Course();
        $fixture->setTitle('Value');
        $fixture->setDescription('Value');
        $fixture->setImage('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setUpdatedAt('Value');
        $fixture->setUser('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'course[title]' => 'Something New',
            'course[description]' => 'Something New',
            'course[image]' => 'Something New',
            'course[createdAt]' => 'Something New',
            'course[updatedAt]' => 'Something New',
            'course[user]' => 'Something New',
        ]);

        self::assertResponseRedirects('/course/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getDescription());
        self::assertSame('Something New', $fixture[0]->getImage());
        self::assertSame('Something New', $fixture[0]->getCreatedAt());
        self::assertSame('Something New', $fixture[0]->getUpdatedAt());
        self::assertSame('Something New', $fixture[0]->getUser());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Course();
        $fixture->setTitle('Value');
        $fixture->setDescription('Value');
        $fixture->setImage('Value');
        $fixture->setCreatedAt('Value');
        $fixture->setUpdatedAt('Value');
        $fixture->setUser('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/course/');
        self::assertSame(0, $this->repository->count([]));
    }
}
