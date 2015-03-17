Feature: Blog Posts CRUD API
  #  Why
  In order to provide clients with blog content
  #  Who
  As an Admin
  #  What
  I need a CRUD API for blog posts

  Scenario: Viewing a list of blog posts
    Given there are posts
    And I am on "/posts"
    Then I should see "Blog"

  Scenario: Returning a collection of posts
    Given there are posts
    And I request "GET" "/api/posts"
    Then I get a "200" response
    And scope into the first "data" property
      And the properties exist:
        """
        id
        title
        body
        """