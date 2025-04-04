<?php

namespace App\Tests\Controller;

use App\Entity\Image;
use App\Entity\Tag;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class ImageControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $imageRepository;
    private string $path = '/image/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->imageRepository = $this->manager->getRepository(Image::class);

        foreach ($this->imageRepository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $this->client->followRedirects();
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Image index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'image[title]' => 'Testing',
            'image[path]' => 'Testing',
            'image[id_tag]' => 'Testing',
            'image[post]' => 'Testing',
            'image[user]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->imageRepository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Image();
        $fixture->setTitle('My Title');
        $fixture->setPath('My Title');
        $fixture->setIdTag('My Title');
        $fixture->setPost('My Title');
        $fixture->setuser('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s/%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Image');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Image();
        $fixture->setTitle('Value');
        $fixture->setPath('Value');
        $fixture->setId_tag('Value');
        $fixture->setPost('Value');
        $fixture->setuser('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'image[title]' => 'Something New',
            'image[path]' => 'Something New',
            'image[id_tag]' => 'Something New',
            'image[post]' => 'Something New',
            'image[user]' => 'Something New',
        ]);

        self::assertResponseRedirects('/image/');

        $fixture = $this->imageRepository->findAll();

        self::assertSame('Something New', $fixture[0]->getTitle());
        self::assertSame('Something New', $fixture[0]->getPath());
        self::assertSame('Something New', $fixture[0]->getId_tag());
        self::assertSame('Something New', $fixture[0]->getPost());
        self::assertSame('Something New', $fixture[0]->getuser());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Image();
        $fixture->setTitle('Value');
        $fixture->setPath('Value');
        $fixture->setId_tag('Value');
        $fixture->setPost('Value');
        $fixture->setuser('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/image/');
        self::assertSame(0, $this->imageRepository->count([]));
    }
}
