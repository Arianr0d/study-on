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
        $this->assertResponseOk();

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
        $this->assertEquals('Error This value should not be blank.', $error->text());

        // проверка на пустоту поля с названием курса
        $buttonSave = $crawler->selectButton('Сохранить');
        $form = $buttonSave->form([
            'course[codeCourse]' => 'TEST',
            'course[nameCourse]' => '',
            'course[descriptionCourse]' => 'Описание тестового курса'
        ]);
        $crawler = $client->submit($form);
        $error = $crawler->filter('.invalid-feedback')->first();
        $this->assertEquals('Error This value should not be blank.', $error->text());

    }

    public function testInsertCourseWithInvalidLengthCodeCourse() : void {

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
        self::assertEquals('Error This value is too long. It should have 255 characters or less.', $error->text());
    }

    public function testInsertCourseWithInvalidLengthNameCourse() : void {

        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', '/courses/');
        $this->assertResponseOk();

        $link = $crawler->filter('.new-course-link')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        // проверка на длину введённого кода курса
        $submitButton = $crawler->selectButton('Сохранить');
        // проверка на длину введённого названия курса
        $form = $submitButton->form([
            'course[codeCourse]' => 'TEST',
            'course[nameCourse]' => 'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST',
            'course[descriptionCourse]' => 'Описание тестового курса'
        ]);
        $crawler = $client->submit($form);
        $error = $crawler->filter('.invalid-feedback')->first();
        self::assertEquals('Error This value is too long. It should have 255 characters or less.', $error->text());
    }

    public function testInsertCourseWithInvalidLengthDescriptionCourse() : void {

        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', '/courses/');
        $this->assertResponseOk();

        $link = $crawler->filter('.new-course-link')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        // проверка на длину введённого кода курса
        $submitButton = $crawler->selectButton('Сохранить');
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
        self::assertEquals('Error This value is too long. It should have 1000 characters or less.', $error->text());
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
        $form['course[descriptionCourse]'] = 'Изменение описания тестового курса';
        $client->submit($form);
        self::assertTrue($client->getResponse()->isRedirect('/courses/' . $course->getId()));
        $crawler = $client->followRedirect();
        $this->assertResponseOk();

        // проверка изменения курса
        $courseName = $crawler->filter('.card_title')->text();
        self::assertEquals('Изменение названия тестового курса', $courseName);
        $courseDescription = $crawler->filter('.card_text')->text();
        self::assertEquals('Изменение описания тестового курса', $courseDescription);
    }
}
