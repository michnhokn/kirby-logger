<?php

namespace Michnhokn;

class Log
{
    private ?string $type;
    private ?string $action;
    private ?string $user;
    private ?string $slug;
    private ?string $language;
    private $oldData;
    private $newData;

    public function __construct(array $parameter = null)
    {
        $this->type = $parameter['type'] ?? null;
        $this->action = $parameter['action'] ?? null;
        $this->user = $parameter['user'] ?? null;
        $this->slug = $parameter['slug'] ?? null;
        $this->language = $parameter['language'] ?? null;
    }

    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'action' => $this->action,
            'user' => $this->user,
            'slug' => $this->slug,
            'language' => $this->language,
            'oldData' => $this->oldData,
            'newData' => $this->newData,
        ];
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param  string  $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getUser(): string
    {
        return $this->user;
    }

    /**
     * @param  string  $user
     */
    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param  string  $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    /**
     * @return string
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @param  string  $language
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    /**
     * @return
     */
    public function getOldData()
    {
        return $this->oldData;
    }

    /**
     * @param $oldData
     */
    public function setOldData($oldData = null)
    {
        $this->oldData = $oldData;
    }

    /**
     * @return
     */
    public function getNewData()
    {
        return $this->newData;
    }

    /**
     * @param   $newData
     */
    public function setNewData($newData): void
    {
        $this->newData = $newData;
    }

    /**
     * @param  string  $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
}