@anonymous @home @basic

Feature: Basic home page functionality

  Scenario: Anonymous user should see expected text on the home page
    Given I am on the homepage
    Then I should see "Welcome to The Learning Campus"
    And I should see "Search for a topic or course"
    And I should see "Use the advanced search below to search for any relevant information."
    And I should see "Explore The Learning Campus"
    And I should see "Choose one of the items below to browse our wide range of topics and content."
    And I should see "Unlock Your Potential at The Learning Campus"
    And I should see "Learning Campus Tools"
    And I should see "The Learning Campus Featured Content"
    And I should see "Browse our featured content, focused on delivering world-class training."

  Scenario: Anonymous user should be able to see & use the Evidence Report link
    Given I am on the homepage
    When I follow "Evidence Report"
    Then I should see "Filter by"
    And I should see the pattern "\d+ results"
    And I should not see "No results found."
    # @todo And the "Evidence Report" checkbox should be checked
    # @todo And the "Webinar" checkbox should not be checked

  Scenario: Anonymous user should be able to see & use the Webinar link
    Given I am on the homepage
    When I follow "Webinar"
    Then I should see "Filter by"
    And I should see the pattern "\d+ results"
    And I should not see "No results found."
    # @todo And the "Webinar" checkbox should be checked
    # @todo And the "Evidence report" checkbox should not be checked

  @search
  Scenario: Anonymous user should be able to find a large set of results from the home page
    Given I am on "/"
    And I fill in "edit-keywords" with "new york"
    And I press "edit-submit-search"
    Then I should see the pattern "\d+ results"
    And I should not see "No results found."

  @search
  Scenario: Anonymous user should be able to find no results from the home page
    Given I am on "/"
    And I fill in "edit-keywords" with "foo bar"
    And I press "edit-submit-search"
    Then I should see "No results found."
    And I should not see the pattern "\d+ results"
