<?php
namespace App\Entity\Doctrine;

use Core\Entity\Entity;

class CommentEntity extends Entity
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var CommandeEntity
     */
    protected $bill;

    /**
     * @var UserEntity
     */
    protected $author;

    /**
     * @var CommentTypeEntity
     */
    protected $type;

    /**
     * @var null|string
     */
    protected $text;

    /**
     * @var null|\DateTime
     */
    protected $date;

    /**
     * @var \DateTime
     */
    protected $created_at;

    /**
     * @var array
     */
    protected $excluded = ['bill'];

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getText();
    }

    /**
     * @return int
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return CommentEntity
     */
    public function setId(int $id): CommentEntity
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return CommandeEntity
     */
    public function getBill(): CommandeEntity
    {
        return $this->bill;
    }

    /**
     * @param CommandeEntity $bill
     * @return CommentEntity
     */
    public function setBill(CommandeEntity $bill): CommentEntity
    {
        $this->bill = $bill;
        return $this;
    }

    /**
     * @return UserEntity
     */
    public function getAuthor(): UserEntity
    {
        return $this->author;
    }

    /**
     * @param UserEntity $author
     * @return CommentEntity
     */
    public function setAuthor(UserEntity $author): CommentEntity
    {
        $this->author = $author;
        return $this;
    }

    /**
     * @return CommentTypeEntity
     */
    public function getType(): CommentTypeEntity
    {
        return $this->type;
    }

    /**
     * @param CommentTypeEntity $type
     * @return CommentEntity
     */
    public function setType(CommentTypeEntity $type): CommentEntity
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string $text
     * @return CommentEntity
     */
    public function setText(string $text): CommentEntity
    {
        $text = trim(strip_tags($text));

        if (!empty($text)) {
            $this->text = $text;
        }

        return $this;
    }

    /**
     * @return null|\DateTime
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     * @return CommentEntity
     */
    public function setDate(\DateTime $date): CommentEntity
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        if ($this->created_at instanceof \DateTime === false) {
            $this->setCreatedAt(new \DateTime);
        }

        return $this->created_at;
    }

    /**
     * @param \DateTime $created_at
     * @return CommentEntity
     */
    public function setCreatedAt(\DateTime $created_at): CommentEntity
    {
        $this->created_at = $created_at;
        return $this;
    }
}