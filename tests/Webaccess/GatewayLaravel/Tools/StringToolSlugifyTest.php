<?php

use Webaccess\GatewayLaravel\Tools\StringTool;

class UploadedFileNameTest extends PHPUnit_Framework_TestCase {

    public function testRemoveSpaces()
    {
        $this->assertEquals('a-name-with-spaces', StringTool::slugify('A name with spaces'));
    }

    public function testRemoveSpecialCharaacters()
    {
        $this->assertEquals('at-name-with-pcia-characters', StringTool::slugify('@-name with $p€cïa| chàractêrs'));
    }

    public function testRemoveQuotes()
    {
        $this->assertEquals('a-name-with-quotes', StringTool::slugify('a name with \' "quotes"'));
    }

    public function testRepaceImageName()
    {
        $this->assertEquals('my-image.jpg', StringTool::slugify('my image.jpg'));
        $this->assertEquals('other-image.png', StringTool::slugify('Other image.png'));
    }

}
 