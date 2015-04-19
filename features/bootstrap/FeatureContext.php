<?php

namespace Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context\MinkContext;

class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{
    private $elements = [
        'login form' => 'form#gaia_loginform',
        'email field' => 'form#gaia_loginform #Email',
        'password field' => 'form#gaia_loginform #Passwd',
        'submit button' => 'form#gaia_loginform #signIn'
    ];

    private $username;
    private $password;

    public function __construct($username = '', $password = '')
    {
        if (empty($username) || empty($password)) {
            $this->username = isset($_SERVER['GMAIL_USERNAME']) ? $_SERVER['GMAIL_USERNAME'] : null;
            $this->password = isset($_SERVER['GMAIL_PASSWD']) ? $_SERVER['GMAIL_PASSWD'] : null;
        } else {
            $this->username = $username;
            $this->password = $password;
        }
    }

    /**
     * @When /^I should see "([^"]+)" element$/
     */
    public function iShouldSeeElement($element)
    {
        $this->assertElementOnPage($this->elements[$element]);
    }

    /**
     * @When /^I fill in login form with my sign\-up data$/
     */
    public function iFillInWithMySignUpData()
    {
        $page = $this->getSession()->getPage();

        $emailField = $page->find('css', $this->elements['email field']);
        $emailField->setValue($this->username);

        $passwordField = $page->find('css', $this->elements['password field']);
        $passwordField->setValue($this->password);

        $submitButton = $page->find('css', $this->elements['submit button']);
        $submitButton->click();
    }

    /**
     * @Then /^I should be redirected to my email account$/
     */
    public function iShouldBeRedirectedToMyEmailAccount()
    {
        $this->waitFor(
            function(FeatureContext $context) {
                $page = $context->getSession()->getPage();
                $navigationElement = $page->find('css', '[role="navigation"]');

                return $navigationElement !== null;
            }
        );
    }

    /**
     * Execute $callable as long as it returns false
     *
     * @param callable $callable
     * @return bool
     */
    private function waitFor($callable)
    {
        while (true) {
            if ($callable($this)) {
                return true;
            }

            sleep(1);
        }

        return false;
    }
}
