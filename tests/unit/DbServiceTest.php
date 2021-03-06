<?php

/**
 * Copyright 2017 Christoph M. Becker
 *
 * This file is part of Moved_XH.
 *
 * Moved_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moved_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moved_XH.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Moved;

use PHPUnit_Framework_TestCase;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;

class DbServiceTest extends PHPUnit_Framework_TestCase
{
    const CONTENT = <<<EOF
foo=Languages
bar=https://google.de
baz
*bar*=$1qux$2

EOF;

    /**
     * @var vfsStreamDirectory
     */
    private $root;

    /**
     * @var DbService
     */
    private $subject;

    protected function setUp()
    {
        global $pth;

        $this->root = vfsStream::setup('root');
        $pth = array(
            'folder' => ['content' => vfsStream::url('root/')]
        );
        $this->subject = new DbService;
    }

    public function testReadMissingContent()
    {
        $this->assertNull($this->subject->readTextContent());
    }

    public function testStoreAndReadContent()
    {
        $this->subject->storeTextContent(self::CONTENT);
        $this->assertSame(self::CONTENT, $this->subject->readTextContent());
    }

    /**
     * @dataProvider findRedirectForData
     * @param string $su
     * @param mixed $expected
     */
    public function testFindRedirectFor($su, $expected)
    {
        $this->subject->storeTextContent(self::CONTENT);
        $this->assertSame($expected, $this->subject->findRedirectFor($su));
    }

    /**
     * @return array
     */
    public function findRedirectForData()
    {
        return array(
            ['foo', 'Languages'],
            ['bar', 'https://google.de'],
            ['baz', ''],
            ['qux', null],
            ['foobarbaz', 'fooquxbaz']
        );
    }
}
