Feature: Dummy Check
  # Why
  In order to begin acceptance testing
  # Who
  As the Developer
  # What
  I want to check that Behat is setup properly

  Scenario: Home Page
    Given I am on the homepage
    Then I should see "Laravel 5"