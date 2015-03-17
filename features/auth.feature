Feature: User Auth
  #  Why
  In order to protect content
  #  Who
  As an Admin
  #  What
  I need auth and registration for users

  Scenario: Registration
    When I register "john" "john@doe.com"
    Then I should have an account

  Scenario: Successful Authentication
    Given I have an account "john" "john@doe.com"
    When I sign in
    Then I should be logged in

  Scenario: Failed Authentication
    When I sign in with invalid creds
    Then I should not be logged in