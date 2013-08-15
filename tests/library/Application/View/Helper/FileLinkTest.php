<?php
/**
 * This file is part of OPUS. The software OPUS has been originally developed
 * at the University of Stuttgart with funding from the German Research Net,
 * the Federal Department of Higher Education and Research and the Ministry
 * of Science, Research and the Arts of the State of Baden-Wuerttemberg.
 *
 * OPUS 4 is a complete rewrite of the original OPUS software and was developed
 * by the Stuttgart University Library, the Library Service Center
 * Baden-Wuerttemberg, the Cooperative Library Network Berlin-Brandenburg,
 * the Saarland University and State Library, the Saxon State Library -
 * Dresden State and University Library, the Bielefeld University Library and
 * the University Library of Hamburg University of Technology with funding from
 * the German Research Foundation and the European Regional Development Fund.
 *
 * LICENCE
 * OPUS is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the Licence, or any later version.
 * OPUS is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details. You should have received a copy of the GNU General Public License
 * along with OPUS; if not, write to the Free Software Foundation, Inc., 51
 * Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 */

/**
 * Unit Tests fuer View Helper zum Rendern von Datei-Links.
 *
 * @category    Application
 * @package     Application_View_Helper
 * @author      Jens Schwidder <schwidder@zib.de>
 * @copyright   Copyright (c) 2008-2013, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 * @version     $Id$
 */
class Application_View_Helper_FileLinkTest extends ControllerTestCase {

    /**
     * Keine serverUrl und keine baseUrl in Unit Tests, daher "http:///files/...".
     */
    public function testFileLink() {
        $helper = new Application_View_Helper_FileLink();

        $helper->setView(Zend_Registry::get('Opus_View'));

        $file = new Opus_File(126);

        $this->assertEquals('<a href="http:///files/146/test.pdf">foo-pdf</a>', $helper->fileLink($file));
    }

    public function testFileLinkSpecialCharacters() {
        $helper = new Application_View_Helper_FileLink();

        $helper->setView(Zend_Registry::get('Opus_View'));

        $file = new Opus_File(130);

        $this->assertEquals(
            '<a href="http:///files/147/special-chars-%25-%22-%23-%26.pdf">Dateiname-mit-Sonderzeichen.pdf</a>',
            $helper->fileLink($file));
    }

    public function testFileLinkSpacesAndQuotes() {
        $helper = new Application_View_Helper_FileLink();

        $helper->setView(Zend_Registry::get('Opus_View'));

        $file = new Opus_File(131);

        $this->assertEquals(
            '<a href="http:///files/147/%27many%27++-++spaces++and++quotes.pdf">'
            . 'Dateiname-mit-vielen-Spaces-und-Quotes.pdf</a>',
            $helper->fileLink($file));
    }

    public function testFileLinkNoLabel() {
        $helper = new Application_View_Helper_FileLink();

        $helper->setView(Zend_Registry::get('Opus_View'));

        $file = new Opus_File(126);
        $file->setLabel(null);

        $this->assertEquals('<a href="http:///files/146/test.pdf">test.pdf</a>', $helper->fileLink($file));
    }

}
