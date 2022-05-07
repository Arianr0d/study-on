<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\CourseAndLessonsFixture;
use App\Tests\Functional\AbstractTest;

class CourseTest extends AbstractTest
{
    protected function getFixtures(): array
    {
        return [CourseAndLessonsFixture::class];
    }

    public function testSomething(): void
    {
        $client = AbstractTest::getClient();
        $url = '/course/';

        $crawler = $client->request('GET', $url);

        $link = $crawler->selectLink('Подробнее')->link();
        $crawler = $client->click($link);

        $this->assertResponseOk();
    }
}
