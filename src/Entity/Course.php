<?php

namespace App\Entity;

use App\Repository\CourseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CourseRepository::class)
 */
class Course
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $codeCourse;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nameCourse;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptionCourse;

    /**
     * @ORM\OneToMany( mappedBy="idCourse", targetEntity="Lesson", cascade = {"persist"}, orphanRemoval = true)
     * @ORM\OrderBy({"numberLesson" = "ASC"})
     */
    private $lessons;

    public function __construct()
    {
        $this->lessons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodeCourse(): ?string
    {
        return $this->codeCourse;
    }

    public function setCodeCourse(string $codeCourse): self
    {
        $this->codeCourse = $codeCourse;

        return $this;
    }

    public function getNameCourse(): ?string
    {
        return $this->nameCourse;
    }

    public function setNameCourse(string $nameCourse): self
    {
        $this->nameCourse = $nameCourse;

        return $this;
    }

    public function getDescriptionCourse(): ?string
    {
        return $this->descriptionCourse;
    }

    public function setDescriptionCourse(?string $descriptionCourse): self
    {
        $this->descriptionCourse = $descriptionCourse;

        return $this;
    }

    /**
     * @return Collection<int, Lesson>
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    public function addLesson(Lesson $lesson): self
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons[] = $lesson;
            $lesson->setIdCourse($this);
        }

        return $this;
    }

    public function removeLesson(Lesson $lesson): self
    {
        if ($this->lessons->removeElement($lesson)) {
            // set the owning side to null (unless already changed)
            if ($lesson->getIdCourse() === $this) {
                $lesson->setIdCourse(null);
            }
        }

        return $this;
    }
}
