<?php

namespace ion\Settings;

interface SettingsInterface {

    function has(string $name): bool;

    function getAsBool(string $name, bool $default = false): bool;

    function getAsString(string $name, string $default = ""): string;

    function getAsInt(string $name, int $default = 0): int;

    function getAsFloat(string $name, float $default = 0): float;

    function getAsArray(string $name, array $default = []): array;

    function getSection(string $name): SettingsInterface;

    function toArray(): array;

    function offsetExists($offset): bool;

    function offsetGet($offset): mixed;

    function offsetSet($offset, $value): void;

    function offsetUnset($offset): void;

}
