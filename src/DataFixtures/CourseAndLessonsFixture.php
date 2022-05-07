<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Course;
use App\Entity\Lesson;

class CourseAndLessonsFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // Курс "Программирование и разработка веб-приложений"
        $course_01_01 = new Course();
        $course_01_01->setCodeCourse('01.01');
        $course_01_01->setNameCourse('Программирование и разработка веб-приложений');
        $course_01_01->setDescriptionCourse('Целью курса является формирование базовых знаний, умений и навыков решения наиболее ' .
        'важных и часто встречаемых на практике задач по веб-программированию на языке программирования Python, а также создание систем и приложений ' .
         'с использованием CMS Django.');

        // Занятие 1
        $lesson = new Lesson();
        $lesson->setNameLesson('Структуры данных Python');
        $lesson->setContentLesson('Математические операции, Работа со строками, Строки, списки, кортежи, последовательности');
        $lesson->setNumberLesson(1);
        $course_01_01->addLesson($lesson);

        // Занятие 2
        $lesson = new Lesson();
        $lesson->setNameLesson('Функциональное программирование');
        $lesson->setContentLesson('Функции, Функции и лямбда функции, Список из функций');
        $lesson->setNumberLesson(2);
        $course_01_01->addLesson($lesson);

        // Занятие 3
        $lesson = new Lesson();
        $lesson->setNameLesson('Основы системного программирования');
        $lesson->setContentLesson('Шифрование и работа со строками');
        $lesson->setNumberLesson(3);
        $course_01_01->addLesson($lesson);

        // Занятие 4
        $lesson = new Lesson();
        $lesson->setNameLesson('Объектно-ориентированное программирование – классы, объекты, наследование');
        $lesson->setContentLesson('Классы и методы, Множественное наследование');
        $lesson->setNumberLesson(4);
        $course_01_01->addLesson($lesson);

        // Занятие 5
        $lesson = new Lesson();
        $lesson->setNameLesson('Объектно-ориентированное программирование – декораторы и генераторы');
        $lesson->setContentLesson('Рубежное тестирование');
        $lesson->setNumberLesson(5);
        $course_01_01->addLesson($lesson);

        $manager->persist($course_01_01);


        // Курс "Функциональное программирование: базовый курс"
        $course_01_02 = new Course();
        $course_01_02->setCodeCourse('01.02');
        $course_01_02->setNameCourse('Функциональное программирование: базовый курс');
        $course_01_02->setDescriptionCourse('По окончанию курса обучающиеся смогут применять базовые концепции ' .
            'фукнционального программирования при написании программ на любых языках, а также получат опыт использования языка Lisp ' .
            'для решения практических задач.');

        // Занятие 1
        $lesson = new Lesson();
        $lesson->setNameLesson('Введение в функциональное программирование и формальные основания функционального программирования');
        $lesson->setContentLesson('Введение');
        $lesson->setNumberLesson(1);
        $course_01_02->addLesson($lesson);

        // Занятие 2
        $lesson = new Lesson();
        $lesson->setNameLesson('Базовые синтаксические конструкции, типы, символы и списки в языке Lisp');
        $lesson->setContentLesson('Типы в Lisp');
        $lesson->setNumberLesson(2);
        $course_01_02->addLesson($lesson);

        // Занятие 3
        $lesson = new Lesson();
        $lesson->setNameLesson('Ввод и вывод в языке Lisp');
        $lesson->setContentLesson('Ввод и вывод в языке Lisp');
        $lesson->setNumberLesson(3);
        $course_01_02->addLesson($lesson);

        // Занятие 4
        $lesson = new Lesson();
        $lesson->setNameLesson('Функции высших порядков');
        $lesson->setContentLesson('Функции высших порядков');
        $lesson->setNumberLesson(4);
        $course_01_02->addLesson($lesson);

        $manager->persist($course_01_02);


        // Курс "Веб-программирование"
        $course_01_05 = new Course();
        $course_01_05->setCodeCourse('01.05');
        $course_01_05->setNameCourse('Веб-программирование');
        $course_01_05->setDescriptionCourse('Курс посвящен базовым технологиям веб-программирования – HTML и CSS и рассчитан ' .
            'на людей с минимальными знаниями в области веб-технологий.\r\nЦель курса – научить \"с нуля\" создавать современные веб-интерфейсы, ' .
            'работая с кодом вручную, на основе графических макетов, подготовленных дизайнером. Вы сможете самостоятельно создавать веб-страницы ' .
            'начального и среднего уровня сложности.');

        // Занятие 1
        $lesson = new Lesson();
        $lesson->setNameLesson('Введение в веб-технологии');
        $lesson->setContentLesson('Знакомство');
        $lesson->setNumberLesson(1);
        $course_01_05->addLesson($lesson);

        // Занятие 2
        $lesson = new Lesson();
        $lesson->setNameLesson('Знакомство с HTML');
        $lesson->setContentLesson('Структура HTML-документа, Разметка текста с помощью HTML, Ссылки и изображения');
        $lesson->setNumberLesson(2);
        $course_01_05->addLesson($lesson);

        // Занятие 3
        $lesson = new Lesson();
        $lesson->setNameLesson('Знакомство с CSS');
        $lesson->setContentLesson('Знакомство с CSS, Селекторы, Наследование и каскадирование');
        $lesson->setNumberLesson(3);
        $course_01_05->addLesson($lesson);

        // Занятие 4
        $lesson = new Lesson();
        $lesson->setNameLesson('Разметка');
        $lesson->setContentLesson('Знакомство с таблицами , Знакомство с формами');
        $lesson->setNumberLesson(4);
        $course_01_05->addLesson($lesson);

        $manager->persist($course_01_05);


        // Курс "Прикладное программирование на языке Python"
        $course_01_23 = new Course();
        $course_01_23->setCodeCourse('01.23');
        $course_01_23->setNameCourse('Прикладное программирование на языке Python');
        $course_01_23->setDescriptionCourse('В целом вы познакомитесь с основными управляющими конструкциями языка, парадишмами ' .
            'фкункционального и объектно-ориентированного программирования. Научитесь настраивать IDE для работы с Python. Узнаете, что такое ' .
            'репозитории кода и в частности github. Откроете для себя огромное многообразие уже готовых библиотек для всех сфер программирования и ' .
            'научитесь их применять.');

        // Занятие 1
        $lesson = new Lesson();
        $lesson->setNameLesson('Философия Python. Введение в программирование. Интерпретируемые языки программированмия. Интерпретатор. IDE');
        $lesson->setContentLesson('Введение');
        $lesson->setNumberLesson(1);
        $course_01_23->addLesson($lesson);

        // Занятие 2
        $lesson = new Lesson();
        $lesson->setNameLesson('Переменные, основные типы данных');
        $lesson->setContentLesson('Переменные, основные типы данных');
        $lesson->setNumberLesson(2);
        $course_01_23->addLesson($lesson);

        // Занятие 3
        $lesson = new Lesson();
        $lesson->setNameLesson('Основы структур данных');
        $lesson->setContentLesson('Основы структур данных');
        $lesson->setNumberLesson(3);
        $course_01_23->addLesson($lesson);

        // Занятие 4
        $lesson = new Lesson();
        $lesson->setNameLesson('Процедурное программирование. Понятие функции. Встроенная библиотека');
        $lesson->setContentLesson('Процедуры, Функции, Библиотеки');
        $lesson->setNumberLesson(4);
        $course_01_23->addLesson($lesson);

        // Занятие 5
        $lesson = new Lesson();
        $lesson->setNameLesson('Элементы функционального программирования');
        $lesson->setContentLesson('Функциональное программирование');
        $lesson->setNumberLesson(5);
        $course_01_23->addLesson($lesson);

        $manager->persist($course_01_23);
        $manager->flush();
    }
}
