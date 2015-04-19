Feature: Checking email presence in GMail account
  As a user
  I want to be able to check if any mail are present on my GMail account

  Scenario: Login form presence
    When I am on the homepage
    Then I should see "login form" element

  Scenario: Successful login
    Given I am on the homepage
    When I fill in login form with my sign-up data
    Then I should be redirected to my email account

  Scenario: Email presence
    When I successfully log in into my Google account
    Then I should see mails
