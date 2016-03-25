<?php

namespace Webaccess\ProjectSquare\Repositories;

interface FileRepository
{
    public static function getFile($fileID);

    public static function getFilesByTicket($ticketID);

    public static function createFile($name, $path, $thumbnailPath, $mimeType, $size, $ticketID);

    public static function updateFile($fileID, $name, $path, $thumbnailPath, $mimeType, $size, $ticketID);

    public static function deleteFile($fileID);
}
