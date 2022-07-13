<?php

namespace App\Tests\Functional\Controller;

use App\DataFixtures\CourseAndLessonsFixture;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Tests\Functional\AbstractTest;

class LessonTest extends AbstractTest
{
    // автоматическая загрузка фикстур
    protected function getFixtures(): array {
        return [CourseAndLessonsFixture::class];
    }

    public function testResponseLessonPages(): void {
        $client = AbstractTest::getClient();
        $courseRepository = self::getEntityManager()->getRepository(Course::class);
        $courses = $courseRepository->findAll();

        foreach ($courses as $course) {
            foreach ($course->getLessons() as $lesson) {
                $client->request('GET', '/lessons/' . $lesson->getId());
                $this->assertResponseOk();

                $client->request('GET', '/lessons/' . $lesson->getId() . '/edit');
                $this->assertResponseOk();

                $client->request('POST', '/lessons/' . $lesson->getId() . '/edit');
                $this->assertResponseOk();
            }
            $client->request('GET', '/courses/' . $course->getId() . '/lessons/newLesson/');
            $this->assertResponseOk();
        }
    }

    public function testInsertLessonWithBlank() : void {

        // проверка перехода на стартовую страницу сайта
        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', '/courses/');
        $this->assertResponseOk();

        // проверка перехода на страницу курса
        $link = $crawler->filter('.course-link')->first()->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $link = $crawler->filter('.lesson-add')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        // проверка на пустоту поля с названием урока
        $buttonSave = $crawler->selectButton('Сохранить');
        $form = $buttonSave->form([
            'lesson[nameLesson]' => '',
            'lesson[contentLesson]' => 'Описание тестового урока',
            'lesson[numberLesson]' => 10
        ]);
        $crawler = $client->submit($form);
        $error = $crawler->filter('.invalid-feedback')->first();
        $this->assertEquals('Error This value should not be blank.', $error->text());

        // проверка на пустоту поля с описанием урока
        $buttonSave = $crawler->selectButton('Сохранить');
        $form = $buttonSave->form([
            'lesson[nameLesson]' => 'Название тестового урока',
            'lesson[contentLesson]' => '',
            'lesson[numberLesson]' => 10
        ]);
        $crawler = $client->submit($form);
        $error = $crawler->filter('.invalid-feedback')->first();
        $this->assertEquals('Error This value should not be blank.', $error->text());

        // проверка на пустоту поля с номером урока
        $buttonSave = $crawler->selectButton('Сохранить');
        $form = $buttonSave->form([
            'lesson[nameLesson]' => 'Название тестового урока',
            'lesson[contentLesson]' => 'Описание тестового урока',
            'lesson[numberLesson]' => ''
        ]);
        $crawler = $client->submit($form);
        $error = $crawler->filter('.invalid-feedback')->first();
        $this->assertEquals('Error This value should not be blank.', $error->text());
    }

    public function testInsertCourseWithInvalidLength() : void {

        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', '/courses/');
        $this->assertResponseOk();

        // проверка перехода на страницу курса
        $link = $crawler->filter('.course-link')->first()->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $link = $crawler->filter('.lesson-add')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        // проверка на длину введённого названия урока
        $submitButton = $crawler->selectButton('Сохранить');
        $form = $submitButton->form([
            'lesson[nameLesson]' => 'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST'.
                'TESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTESTTEST',
            'lesson[contentLesson]' => 'Описание тестового урока',
            'lesson[numberLesson]' => ''
        ]);
        $crawler = $client->submit($form);
        $error = $crawler->filter('.invalid-feedback')->first();
        $this->assertEquals('Error This value is too long. It should have 255 characters or less.', $error->text());

        // проверка на значение введённого номера урока
        $submitButton = $crawler->selectButton('Сохранить');
        $form = $submitButton->form([
            'lesson[nameLesson]' => 'Название тестового урока',
            'lesson[contentLesson]' => 'Описание тестового урока',
            'lesson[numberLesson]' => 0
        ]);
        $crawler = $client->submit($form);
        $error = $crawler->filter('.invalid-feedback')->first();
        $this->assertEquals('Error This value should be between 1 and 10000.', $error->text());
    }

    public function testLessonDeleteAfterCourse() : void {

        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', '/courses/');
        $this->assertResponseOk();

        $link = $crawler->filter('.course-link')->first()->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $lessonLinks = $crawler->filter('.list-group-item > a')->link();

        // проверка удаления курса
        $client->submitForm('course-delete');
        self::assertTrue($client->getResponse()->isRedirect('/courses/'));
        $crawler = $client->followRedirect();
        $this->assertResponseOk();

        foreach ($lessonLinks as $link) {
            $client->request('GET', $link);
            $this->assertResponseNotFound();
        }
    }

    public function testLessonDelete() : void {

        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', '/courses/');
        $this->assertResponseOk();

        $courseLink = $crawler->filter('.course-link')->first()->link();
        $crawler = $client->click($courseLink);
        $this->assertResponseOk();

        $lessonLinks = $crawler->filter('.list-group-item > a');
        $lessonCount = $lessonLinks->count();
        $lessonLink = $lessonLinks->first()->link();
        $crawler = $client->click($lessonLink);
        $this->assertResponseOk();

        // проверка удаления урока
        $client->submitForm('lesson-delete');
        self::assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertResponseOk();
        self::assertCount($lessonCount - 1, $crawler->filter('.list-group-item > a'));
    }

    public function testLessonUpdate(): void {
        $client = self::getClient();
        $crawler = $client->request('GET', '/courses/');
        $this->assertResponseOk();

        $link = $crawler->filter('.course-link')->first()->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $courseName = $crawler->filter('.card_title')->first()->text();
        $linkLesson = $crawler->filter('.list-group-item > a')->first()->link();
        $crawler = $client->click($linkLesson);
        $this->assertResponseOk();

        $link = $crawler->filter('.lesson-edit')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $submitButton = $crawler->selectButton('Сохранить');
        $form = $submitButton->form();
        $course = self::getEntityManager()
            ->getRepository(Course::class)
            ->findOneBy(['nameCourse' => $courseName]);
        $form['lesson[nameLesson]'] = 'Измененное название урока';
        $form['lesson[contentLesson]'] = 'Измененное описание урока';
        $form['lesson[numberLesson]'] = 100;
        $client->submit($form);
        self::assertTrue($client->getResponse()->isRedirect('/courses/' . $course->getId()));
        $crawler = $client->followRedirect();
        $this->assertResponseOk();

        $lessonEdit = $crawler->filter('.list-group-item > a')->last()->link();
        $crawler = $client->click($lessonEdit);
        $this->assertResponseOk();

        // проверка изменения урока
        $lessonName = $crawler->filter('.lesson_name')->text();
        $this->assertEquals('Измененное название урока', $lessonName);

        $lessonDescription = $crawler->filter('.lesson_content')->text();
        $this->assertEquals('Измененное описание урока', $lessonDescription);
    }
}
