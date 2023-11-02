<?php

namespace ion;

interface SettingsInterface {

    static function parseJson(string $data): SettingsInterface;

    function getSetting(string $name, $default = null);

    function getSettingAsBool(string $name, bool $default = false): bool;

    function getSettingAsString(string $name, string $default = ""): string;

    function getSettingAsInt(string $name, int $default = 0): int;

    function getSettingAsFloat(string $name, float $default = 0): float;

    function getSettingAsArray(string $name, array $default = []): array;

    function toArray(): array;

    function offsetExists($offset): bool;

    function offsetGet($offset): mixed;

    function offsetSet($offset, $value): void;

    function offsetUnset($offset): void;

}
