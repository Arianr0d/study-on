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

    public function urlNotFound() {
        yield['/lessons/'];
    }
    public function urlInternalServerError() {
        yield['lessons/new'];
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

    public function testInsertLessonWithBlank() : void {

        // проверка перехода на стартовую страницу сайта
        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', '/courses/');
        $this->assertResponseRedirect();

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
        $this->assertEquals('Значение не может быть пустым!', $error->text());

        // проверка на пустоту поля с описанием урока
        $buttonSave = $crawler->selectButton('Сохранить');
        $form = $buttonSave->form([
            'lesson[nameLesson]' => 'Название тестового урока',
            'lesson[contentLesson]' => '',
            'lesson[numberLesson]' => 10
        ]);
        $crawler = $client->submit($form);
        $error = $crawler->filter('.invalid-feedback')->first();
        $this->assertEquals('Значение не может быть пустым!', $error->text());

        // проверка на пустоту поля с номером урока
        $buttonSave = $crawler->selectButton('Сохранить');
        $form = $buttonSave->form([
            'lesson[nameLesson]' => 'Название тестового урока',
            'lesson[contentLesson]' => 'Описание тестового урока',
            'lesson[numberLesson]' => ''
        ]);
        $crawler = $client->submit($form);
        $error = $crawler->filter('.invalid-feedback')->first();
        $this->assertEquals('Значение не может быть пустым!', $error->text());
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
        $this->assertEquals('Значение не должно превышать 255 символов!', $error->text());

        // проверка на значение введённого номера урока
        $submitButton = $crawler->selectButton('Сохранить');
        $form = $submitButton->form([
            'lesson[nameLesson]' => 'Название тестового урока',
            'lesson[contentLesson]' => 'Описание тестового урока',
            'lesson[numberLesson]' => 0
        ]);
        $crawler = $client->submit($form);
        $error = $crawler->filter('.invalid-feedback')->first();
        $this->assertEquals('Значение должно быть больше 1 и не должно превышать 10000!', $error->text());
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

        $lessonLink = $crawler->filter('.list-group-item > a')->first()->link();
        $crawler = $client->click($lessonLink);
        $this->assertResponseOk();

        // проверка удаления урока
        $client->submitForm('lesson-delete');
        self::assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertResponseOk();
        self::assertCount(3, $crawler->filter('.list-group-item'));
    }

    public function testLessonUpdate(): void {
        $client = AbstractTest::getClient();
        $crawler = $client->request('GET', '/courses/');
        $this->assertResponseOk();

        $link = $crawler->filter('.course-link')->first()->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $linkLesson = $crawler->filter('.list-group-item > a')->first()->link();
        $crawler = $client->click($linkLesson);
        $this->assertResponseOk();

        $link = $crawler->filter('.lesson-edit')->link();
        $crawler = $client->click($link);
        $this->assertResponseOk();

        $submitButton = $crawler->selectButton('Сохранить');
        $form = $submitButton->form();
        $course = self::getEntityManager()->getRepository(Course::class)
            ->findOneBy(['id' => $form['lesson[idCourse]']->getValue()]);
        $form['lesson[nameLesson]'] = 'Измененное название урока';
        $form['lesson[contentLesson]'] = 'Измененное описание урока';
        $form['lesson[numberLesson]'] = 13;
        $client->submit($form);
        self::assertTrue($client->getResponse()->isRedirect());
        $crawler = $client->followRedirect();
        $this->assertResponseOk();

        // проверка изменения урока
        $lessonName = $crawler->filter('.lesson_name')->text();
        $this->assertEquals('Измененное название урока', $lessonName);

        $lessonDescription = $crawler->filter('.lesson_content')->text();
        $this->assertEquals('Измененное описание урока', $lessonDescription);
    }
}
