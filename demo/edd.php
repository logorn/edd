<?php
/**
 * Copyright (c) 2009-2011 Stefan Priebsch <stefan@priebsch.de>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 *   * Redistributions of source code must retain the above copyright notice,
 *     this list of conditions and the following disclaimer.
 *
 *   * Redistributions in binary form must reproduce the above copyright notice,
 *     this list of conditions and the following disclaimer in the documentation
 *     and/or other materials provided with the distribution.
 *
 *   * Neither the name of Stefan Priebsch nor the names of contributors
 *     may be used to endorse or promote products derived from this software
 *     without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO,
 * THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR
 * PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER ORCONTRIBUTORS
 * BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY,
 * OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @package    edd
 * @author     Stefan Priebsch <stefan@priebsch.de>
 * @copyright  Stefan Priebsch <stefan@priebsch.de>. All rights reserved.
 * @license    BSD License
 */

namespace spriebsch\edd;

require __DIR__ . '/../lib/factory.phar';
require __DIR__ . '/../src/autoload.php';

$factory = new ApplicationFactory();
$factory->register(new PageFactory());

$user = new User();
$session = new Session();

// Randomly choose DE or EN as session language
$languages = array('DE', 'EN');
$language = $languages[rand(0, count($languages) - 1)];

if ($session->hasExperiment('NewProfile')) {
    $experiment = $session->getExperiment('NewProfile');
} else {
    $experiment = new NewProfileExperiment($session->getId(), new Environment($language, $user));
    $session->registerExperiment($experiment);
}

$experiment->run($factory);

// This will usually return an instance of ProfilePage
// When the experiment runs, it returns an instance of NewProfile page instead
$page = $factory->getInstanceFor('ProfilePage');
var_dump($page->render());

if ($experiment->isRunning()) {

    // Randomly choose a rating between 0 and 5
    $ratings = array(0, 1, 2, 3, 4, 5);
    $rating = $ratings[rand(0, count($ratings) - 1)];

    $experiment->setRating($rating);
}

$experiment->end(new Logger());
