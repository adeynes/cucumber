<?php
declare(strict_types=1);

namespace src\cucumber\command;

class ParsedCommand
{

    /** @var string */
    protected $name;

    /** @var string[] */
    protected $args;

    /** @var string[] */
    protected $tags;

    /**
     * @param string $name
     * @param string[] $args
     * @param string[] $tags
     */
    public function __construct(string $name, array $args, array $tags)
    {
        $this->name = $name;
        $this->args = $args;
        $this->tags = $tags;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     *
     * @param int[]|int[][] $requests
     * @return string[]
     */
    public function get(array $requests): array
    {
        $args = [];

        foreach ($requests as $request) {
            if (is_array($request)) {
                // $requested[0] is offset, $request[1] is length. Negative length means start from back
                if ($request[1] < 0) $request[1] += count($this->getArgs());
                $args[] = trim(implode(' ', array_slice($this->getArgs(), ...$request)));
            } else
                $args[] = array_slice($this->getArgs(), $request, 1)[0]; // allow negative offsets
        }

        return $args;
    }

    /**
     * @return string[]
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @return string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    public function getTag(string $tag): ?string
    {
        return $this->getTags()[$tag] ?? null;
    }

}