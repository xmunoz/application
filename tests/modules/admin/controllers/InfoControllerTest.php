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
 *
 * @category    Application
 * @package     Tests
 * @author      Edouard Simon <edouard.simon@zib.de>
 * @copyright   Copyright (c) 2008-2010, OPUS 4 development team
 * @license     http://www.gnu.org/licenses/gpl.html General Public License
 * @version     $Id:$
 */
class Admin_InfoControllerTest extends ControllerTestCase {

    public function testIndexDisplayFailedWorkerJobs() {
        $asyncFlag = $this->setAsynchronousExecution();

        $this->assertEquals(0, Opus_Job::getCount(Opus_Job::STATE_FAILED), 'test data changed.');
        $jobIds = array();
        for ($i = 0; $i < 10; $i++) {
            $job = new Opus_Job();
            $job->setLabel('testjob' . ($i < 5 ? 1 : 2));
            $job->setData(array(
                'documentId' => $i,
                'task' => 'get-me-a-coffee'));
            $job->setState(Opus_Job::STATE_FAILED);
            $jobIds[] = $job->store();
        }

        $this->dispatch('/admin/info');
        $this->assertResponseCode(200);
        $this->assertQueryContentContains('table.worker-jobs td', 'testjob1');
        $this->assertQueryContentContains('table.worker-jobs td', 'testjob2');

        $testJobs = Opus_Job::getAll($jobIds);
        foreach ($testJobs as $job) {
            $job->delete();
        }

        $this->resetAsynchronousExecution($asyncFlag);
    }

    public function testMonitorFailedWorkerJobs() {
        $asyncFlag = $this->setAsynchronousExecution();

        $this->assertEquals(0, Opus_Job::getCount(Opus_Job::STATE_FAILED), 'test data changed.');
        $jobIds = array();
        for ($i = 0; $i < 10; $i++) {
            $job = new Opus_Job();
            $job->setLabel('testjob' . ($i < 5 ? 1 : 2));
            $job->setData(array(
                'documentId' => $i,
                'task' => 'get-me-a-coffee'));
            $job->setState(Opus_Job::STATE_FAILED);
            $jobIds[] = $job->store();
        }

        $this->dispatch('/admin/info/worker-monitor');
        $this->assertResponseCode(200);
        $this->assertEquals('1', $this->_response->getBody(), 'Expected value 1');

        $testJobs = Opus_Job::getAll($jobIds);
        foreach ($testJobs as $job) {
            $job->delete();
        }

        $this->resetAsynchronousExecution($asyncFlag);
    }

    private function setAsynchronousExecution($enabled = true) {
        $oldValue = null;
        $config = Zend_Registry::get('Zend_Config');
        if (isset($config->runjobs->asynchronous)) {
            $oldValue = $config->runjobs->asynchronous;
            $config->runjobs->asynchronous = $enabled;
        } else {
            $config->merge(new Zend_Config(array('runjobs' => array('asynchronous' => $enabled))));
        }
        return $oldValue;
    }

    private function resetAsynchronousExecution($oldValue) {
        $config = Zend_Registry::get('Zend_Config');
        if (is_null($oldValue)) {
            unset($config->runjobs->asynchronous);
        } else {
            $config->runjobs->asynchronous = $oldValue;
        }
    }

}
