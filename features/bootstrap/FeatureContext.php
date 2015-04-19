<?php

namespace Context;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\SnippetAcceptingContext;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\MinkExtension\Context\MinkContext;

class FeatureContext extends MinkContext implements Context, SnippetAcceptingContext
{
    private $elements = [
        'login form' => 'form#gaia_loginform',
        'email field' => 'form#gaia_loginform #Email',
        'password field' => 'form#gaia_loginform #Passwd',
        'submit button' => 'form#gaia_loginform #signIn',
        'email' => '[role="main"] [role="link"]'
    ];

    private $username;
    private $password;

    public function __construct($username = '', $password = '')
    {
        if (empty($username) || empty($password)) {
            $username = isset($_SERVER['GMAIL_USERNAME']) ? $_SERVER['GMAIL_USERNAME'] : null;
            $password = isset($_SERVER['GMAIL_PASSWD']) ? $_SERVER['GMAIL_PASSWD'] : null;
            if (empty($username) || empty($username)) {
                throw new \Exception(
                    "You must provide your Google account's username and password\n"
                    . "Please take a look at README.md file to see how to do this."
                );
            }
        } else {
            $this->username = $username;
            $this->password = $password;
        }
    }

    /**
     * @AfterScenario
     */
    public function afterScenario()
    {
        $this->getSession()->stop();
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

    /**
     * @When /^I successfully log in into my Google account$/
     */
    public function iSuccessfullyLogInIntoMyGoogleAccount()
    {
        $this->iAmOnHomepage();
        $this->iFillInWithMySignUpData();
        $this->iShouldBeRedirectedToMyEmailAccount();
    }

    /**
     * @Then /^I should see mails$/
     */
    public function iShouldSeeMails()
    {
        $page = $this->getSession()->getPage();
        $emailElement = $page->find('css', $this->elements['email']);
        if ($emailElement == null) {
            throw new \Exception("No email found");
        }
    }
}
