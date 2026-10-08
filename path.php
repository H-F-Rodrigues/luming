<?php

function getPath(string $folder): string {
    // BasePath/src/folder/
    return BASE_PATH . DIRECTORY_SEPARATOR . SOURCES . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR;
}

function getPublicPath(string $folder): string {
    // BasePath/src/folder/
    return BASE_PATH . DIRECTORY_SEPARATOR . PUB . DIRECTORY_SEPARATOR . $folder . DIRECTORY_SEPARATOR;
}

function getComponentsPath(): string {
    return getPath('components');
}

function getControllersPath(): string {
    return getPath('controllers');
}

function getFunctionsPath(): string {
    return getPath('functions');
}

function getViewsPath(): string {
    return getPath('views');
}

function getConfigsPath(): string {
    return getPath('configs');
}

function getServicesPath(): string {
    return getPath('services');
}

function getModelsPath(): string {
    return getPath('models');
}

function getStoragePath(): string {
    return getPublicPath('storage');
}