<?php

namespace App\Entity;

use App\Repository\LessonRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LessonRepository::class)
 */
class Lesson
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Course::class, cascade = {"persist"}, inversedBy = "lessons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $idCourse;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameLesson;

    /**
     * @ORM\Column(type="text")
     */
    private $contentLesson;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numberLesson;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCourse(): ?Course
    {
        return $this->idCourse;
    }

    public function setIdCourse(?Course $idCourse): self
    {
        $this->idCourse = $idCourse;

        return $this;
    }

    public function getNameLesson(): ?string
    {
        return $this->nameLesson;
    }

    public function setNameLesson(string $nameLesson): self
    {
        $this->nameLesson = $nameLesson;

        return $this;
    }

    public function getContentLesson(): ?string
    {
        return $this->contentLesson;
    }

    public function setContentLesson(string $contentLesson): self
    {
        $this->contentLesson = $contentLesson;

        return $this;
    }

    public function getNumberLesson(): ?int
    {
        return $this->numberLesson;
    }

    public function setNumberLesson(?int $numberLesson): self
    {
        $this->numberLesson = $numberLesson;

        return $this;
    }
}
