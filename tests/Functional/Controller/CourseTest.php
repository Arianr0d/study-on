<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\CourseAndLessonsFixture;
use App\Tests\Functional\AbstractTest;
use App\Entity\Course;

class CourseTest extends AbstractTest
{
    // автоматическая загрузка фикстур
    protected function getFixtures() : array {
        return [CourseAndLessonsFixture::class];
    }

    public function urlSuccess() {
        yield['/courses/'];
        yield['/courses/new'];
    }

    public function urlNotFound() {
        yield['/courses/0'];
        yield['/random/'];
    }

    public function urlInternalServerError() {
        yield['courses/test'];
    }

    public function testResponsePages(): void {
        $client = AbstractTest::getClient();
        $courseRepository = self::getEntityManager()->getRepository(Course::class);
        $courses = $courseRepository->findAll();

        foreach ($courses as $course) {
            $client->request('GET', '/courses/' . $course->getId());
            $this->assertResponseOk();
            $client->request('GET', '/courses/' . $course->getId() . '/edit');
            $this->assertResponseOk();
            $client->request('GET', '/courses/' . $course->getId() . '/lessons/newLesson/');
            $this->assertResponseOk();

            $client->request('POST', '/courses/' . $course->getId() . '/edit');
            $this->assertResponseOk();
            $client->request('POST', '/courses/' . $course->getId() . '/lessons/newLesson/');
            $this->assertResponseOk();
        }
        $client->request('GET', '/courses/new');
        $this->assertResponseOk();
        $client->request('POST', '/courses/new');
        $this->assertResponseOk();
    }

    public function testUrlSuccess($url): void {
        $client = AbstractTest::getClient();
        $client->request('GET', $url);
        $this->assertResponseOk();
    }

    public function testUrlNotFound($url): void {
        $client = AbstractTest::getClient();
        $client->request('GET', $url);
        $this->assertResponseNotFound();
    }

    public function testUrlInternalServerError($url) {
        $client = AbstractTest::getClient();
        $client->request('GET', $url);
        $this->assertResponseCode(500);
    }

    public function testCountCourses() : void {
        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', '/courses/');
        $courseRepository = self::getEntityManager()->getRepository(Course::class);
        $courseCount = count($courseRepository->findAll());
        $this->assertCount($courseCount, $crawler->filter('.course-card'));
    }

    public function testCountLessons() : void {
        $client = AbstractTest::getClient();
        $courseRepository = self::getEntityManager()->getRepository(Course::class);
        $courses = $courseRepository->findAll();
        self::assertEmpty($courses);

        foreach ($courses as $course) {
            $crawler = $client->request('GET', '/courses/'.$course->getId());
            $this->assertResponseOk();
            $lessonsCount = count($course->getLessons());
            $this->assertCount($lessonsCount, $crawler->filter('.list-group-item'));
        }
    }

    public function testInsertCourseWithBlank() : void {

        // проверка перехода на стартовую страницу сайта
        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', '/courses/');
        $this->assertResponseRedirect();

        // проверка перехода на страницу с добавлением нового курса
        $link = $crawler->filter('.new-course-link')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        // проверка на пустоту поля с кодом курса
        $buttonSave = $crawler->selectButton('Сохранить');
        $form = $buttonSave->form([
            'course[codeCourse]' => '',
            'course[nameCourse]' => 'Тестовый курс',
            'course[descriptionCourse]' => 'Описание тестового курса'
        ]);
        $crawler = $client->submit($form);
        $error = $crawler->filter('.invalid-feedback')->first();
        $this->assertEquals('Значение не может быть пустым!', $error->text());

        // проверка на пустоту поля с названием курса
        $buttonSave = $crawler->selectButton('Сохранить');
        $form = $buttonSave->form([
            'course[codeCourse]' => 'TEST',
            'course[nameCourse]' => '',
            'course[descriptionCourse]' => 'Описание тестового курса'
        ]);
        $crawler = $client->submit($form);
        $error = $crawler->filter('.invalid-feedback')->first();
        $this->assertEquals('Значение не может быть пустым!', $error->text());

    }

    public function testInsertCourseWithInvalidLength() : void {

        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', '/courses/');
        $this->assertResponseOk();

        $link = $crawler->filter('.new-course-link')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        // проверка на длину введённого кода курса
        $submitButton = $crawler->selectButton('Сохранить');
        $form = $submitButton->form([
            'course[codeCourse]' => 'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST',
            'course[nameCourse]' => 'Тестовый курс',
            'course[descriptionCourse]' => 'Описание тестового курса'
        ]);
        $crawler = $client->submit($form);
        $error = $crawler->filter('.invalid-feedback')->first();
        $this->assertEquals('Значение не должно превышать 255 символов!', $error->text());

        // проверка на длину введённого названия курса
        $form = $submitButton->form([
            'course[codeCourse]' => 'TEST',
            'course[nameCourse]' => 'Тестовый курс',
            'course[descriptionCourse]' => 'Описание тестового курса'
        ]);
        $crawler = $client->submit($form);
        $error = $crawler->filter('.invalid-feedback')->first();
        $this->assertEquals('Значение не должно превышать 255 символов!', $error->text());

        // проверка на длину введённого описания курса
        $form = $submitButton->form([
            'course[codeCourse]' => 'TEST',
            'course[nameCourse]' => 'Тестовый курс',
            'course[descriptionCourse]' => 'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'
        ]);
        $crawler = $client->submit($form);
        $error = $crawler->filter('.invalid-feedback')->first();
        $this->assertEquals('Значение не должно превышать 1000 символов!', $error->text());
    }

    public function testInsertCourseWithNotUniqueCode() : void {

        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', '/courses/');
        $this->assertResponseOk();

        $link = $crawler->filter('.new-course-link')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        // проверка на уникальность кода курса
        $submitButton = $crawler->selectButton('Сохранить');
        $form = $submitButton->form([
            'course[codeCourse]' => '01.02',
            'course[nameCourse]' => 'Тестовый курс',
            'course[descriptionCourse]' => 'Описание тестового курса'
        ]);
        $crawler = $client->submit($form);
        $error = $crawler->filter('.invalid-feedback')->first();
        $this->assertEquals('Указанный код курса уже используется!', $error->text());
    }

    public function testCourseDelete() : void {

        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', '/courses/');
        $this->assertResponseOk();

        $link = $crawler->filter('.course-link')->first()->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        // проверка удаления курса
        $client->submitForm('course-delete');
        self::assertTrue($client->getResponse()->isRedirect('/courses/'));
        $crawler = $client->followRedirect();
        $this->assertResponseOk();

        // проверка на ненулевое количество курсов на странице курсов
        $courseRepository = self::getEntityManager()->getRepository(Course::class);
        $courses = $courseRepository->findAll();
        self::assertNotEmpty($courses);
        $actualCoursesCount = count($courses);
        $this->assertCount($actualCoursesCount, $crawler->filter('.course-card'));
    }

    public function testCourseUpdate() : void {

        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', '/courses/');
        $this->assertResponseOk();

        $link = $crawler->filter('.course-link')->first()->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        // проверка успешности перехода на страницу с редактиронием курса
        $link = $crawler->filter('.course-edit')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        // изменение курса
        $submitButton = $crawler->selectButton('Сохранить');
        $form = $submitButton->form();
        $course = self::getEntityManager()
            ->getRepository(Course::class)
            ->findOneBy(['codeCourse' => $form['course[codeCourse]']->getValue()]);

        $form['course[codeCourse]'] = 'TestEditCodeCourse';
        $form['course[nameCourse]'] = 'Изменение названия тестового курса';
        $form['course[descriptionCourse]'] = 'Изменение описания тестового курс';
        $client->submit($form);
        self::assertTrue($client->getResponse()->isRedirect('/courses/' . $course->getId()));
        $crawler = $client->followRedirect();
        $this->assertResponseOk();

        // проверка изменения курса
        $courseName = $crawler->filter('.card-title')->text();
        self::assertEquals('Измененный курс', $courseName);
        $courseDescription = $crawler->filter('.card-text')->text();
        self::assertEquals('Измененный курс', $courseDescription);
    }
}
